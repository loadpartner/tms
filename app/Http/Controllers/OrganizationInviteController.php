<?php

namespace App\Http\Controllers;

use App\Actions\Organizations\SendInvite;
use App\Http\Requests\StoreOrganizationInviteRequest;
use App\Http\Requests\UpdateOrganizationInviteRequest;
use App\Mail\Organizations\UserInvite;
use App\Models\Organizations\Organization;
use App\Models\Organizations\OrganizationInvite;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class OrganizationInviteController extends Controller
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrganizationInviteRequest $request, Organization $organization)
    {
        Gate::authorize('create', [OrganizationInvite::class, $organization]);
        try {
            $newInvite = SendInvite::run($request->email, $organization);

            return redirect()->route('organizations.show', $organization)
                ->with('success', 'Invite sent to ' . $newInvite->email);
        } catch (\Symfony\Component\HttpFoundation\Exception\BadRequestException $e) {
            throw ValidationException::withMessages([
                'email' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization, string $invite)
    {
        $orgInvite = OrganizationInvite::allOrganizations()->where('code', $invite)->firstOrFail();

        Gate::authorize('view', $orgInvite);

        return Inertia::render('Organizations/InviteAccept', [
            'organization' => $organization,
            'invite' => $orgInvite,
            'showCreateOption' => auth()->user()->organizations()->count() < 1,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization, OrganizationInvite $invite)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrganizationInviteRequest $request, Organization $organization, OrganizationInvite $invite)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization, OrganizationInvite $invite)
    {
        Gate::authorize('delete', $invite);

        $invite->delete();

        return redirect()->route('organizations.show', $organization)
            ->with('success', 'Invite deleted');
    }

    public function resend(Organization $organization, OrganizationInvite $invite)
    {
        Mail::to($invite->email)->send(new UserInvite($invite));
    }

    /**
     * Accept the organization invite and add current user to the org
     */
    public function accept(Organization $organization, string $invite)
    {
        $orgInvite = OrganizationInvite::allOrganizations()->where('code', $invite)->firstOrFail();

        Gate::authorize('view', $orgInvite);

        $orgInvite->update([
            'accepted_by_id' => auth()->id(),
            'accepted_at' => now(),
        ]);

        $user = auth()->user();
        $user->organizations()->attach($organization->id);
        $user->current_organization_id = $organization->id;
        $user->save();

        return redirect()->route('dashboard', $organization)
            ->with('success', 'You have joined ' . $organization->name);
    }
}
