<?php

namespace App\Http\Controllers;

use App\Actions\IntegrationSettings\GetAllGlobalIntegrationSettings;
use App\Enums\Permission;
use App\Enums\Subscriptions\SubscriptionType;
use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Http\Resources\Organization\GlobalIntegrationSettingResource;
use App\Http\Resources\Organization\IntegrationSettingResource;
use App\Models\Organizations\IntegrationSetting;
use App\Models\Organizations\Organization;
use App\Models\Organizations\OrganizationUser;
use App\Models\Permissions\Permission as PermissionModel;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Organizations/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrganizationRequest $request)
    {
        $org = Organization::create([
            'name' => $request->name,
            'owner_id' => $request->user()->id,
        ]);

        $user = auth()->user();

        $orgMembership = OrganizationUser::create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
        ]);

        $user->update([
            'current_organization_id' => $org->id,
        ]);

        return redirect()->route('dashboard');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization)
    {
        return $this->showUsers($organization);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrganizationRequest $request, Organization $organization)
    {
        $organization->update($request->validated());

        return redirect()->route('organizations.edit', $organization);
    }

    public function showUsers(Organization $organization)
    {
        Gate::authorize('view', $organization);

        $subscription = $organization->subscription(SubscriptionType::USER_SEAT->value);
        $seatUsage = \App\Actions\Organizations\CheckSeatLimits::make()->getSeatUsage($organization);

        return Inertia::render('Organizations/Users', [
            'organization' => $organization->load('owner', 'users'),
            'invites' => $organization->invites,
            'subscription' => $subscription ? [
                'id' => $subscription->id,
                'type' => $subscription->type,
                'stripe_status' => $subscription->stripe_status,
                'quantity' => $subscription->quantity,
                'trial_ends_at' => $subscription->trial_ends_at?->toISOString(),
                'ends_at' => $subscription->ends_at?->toISOString(),
            ] : null,
            'seatUsage' => $seatUsage,
        ]);
    }

    public function showRoles(Organization $organization)
    {
        Gate::authorize('view', $organization);

        return Inertia::render('Organizations/Roles', [
            'organization' => $organization->load('owner', 'users'),
            'roles' => $organization->roles->load('permissions', 'users'),
            'permissions' => PermissionModel::all(),
        ]);
    }

    public function showIntegrationSettings(Organization $organization)
    {
        Gate::authorize('viewAny', IntegrationSetting::class);

        $settings = $organization->integration_settings()->get();

        return Inertia::render('Organizations/IntegrationSettings', [
            'organization' => $organization->load('owner', 'users'),
            'integrationSettings' => IntegrationSettingResource::collection($settings),
            'globalIntegrationSettings' => GlobalIntegrationSettingResource::collection(GetAllGlobalIntegrationSettings::run()),
        ]);
    }

    public function showSettings(Organization $organization)
    {
        Gate::authorize('view', $organization);

        return Inertia::render('Organizations/Settings', [
            'organization' => $organization->load('owner', 'users', 'companyLocation'),
        ]);
    }

    public function showDocumentTemplates(Organization $organization)
    {
        Gate::authorize('view', $organization);

        return Inertia::render('Organizations/DocumentTemplates', [
            'organization' => $organization->load('owner', 'users'),
        ]);
    }

    public function showBilling(Organization $organization)
    {
        // Check if billing is enabled
        if (!config('subscriptions.enable_billing')) {
            abort(403, 'Billing is disabled');
        }

        Gate::authorize(Permission::ORGANIZATION_BILLING);

        $subscription = $organization->subscription(SubscriptionType::USER_SEAT->value);

        return Inertia::render('Organizations/Billing', [
            'organization' => $organization->load('owner', 'users'),
            'subscription' => $subscription ? [
                'id' => $subscription->id,
                'type' => $subscription->type,
                'stripe_status' => $subscription->stripe_status,
                'quantity' => $subscription->quantity,
                'trial_ends_at' => $subscription->trial_ends_at?->toISOString(),
                'ends_at' => $subscription->ends_at?->toISOString(),
            ] : null,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization)
    {
        //
    }

    public function removeUser(Organization $organization, User $user)
    {

        $organizationUser = OrganizationUser::where('organization_id', $organization->id)
            ->where('user_id', $user->id)
            ->first();

        Gate::authorize('delete', $organizationUser);

        $organizationUser->delete();

        // Remove them from current org if assigned to this one
        if ($user->current_organization_id === $organization->id) {
            $user->update([
                'current_organization_id' => null,
            ]);
        }

        return redirect()->route('organizations.show', $organization)
            ->with('success', 'User removed');
    }

    public function transferOwnership(Organization $organization, User $user)
    {
        Gate::authorize('update', $organization);

        // Check if the current user is the organization owner
        if (auth()->id() !== $organization->owner_id) {
            return redirect()->route('organizations.show', $organization)
                ->with('error', 'Only the organization owner can transfer ownership');
        }

        // ensure the $user is a member of the org
        $organizationUser = OrganizationUser::where('organization_id', $organization->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$organizationUser) {
            return redirect()->route('organizations.show', $organization)
                ->with('error', 'User is not a member of this organization');
        }

        $organization->update([
            'owner_id' => $user->id,
        ]);

        return redirect()->route('organizations.show', $organization)
            ->with('success', 'Ownership transferred');
    }

    public function switchOrganization(Organization $organization)
    {
        $user = auth()->user();

        // Check if the user is a member of the organization
        $isMember = OrganizationUser::where('organization_id', $organization->id)
            ->where('user_id', $user->id)
            ->exists();
            
        if (!$isMember) {
            return redirect()->back()
                ->with('error', 'You are not a member of this organization');
        }
        $user->update([
            'current_organization_id' => $organization->id,
        ]);

        return redirect()->back();
    }
}
