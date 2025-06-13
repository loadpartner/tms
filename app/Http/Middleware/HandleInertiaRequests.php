<?php

namespace App\Http\Middleware;

use App\Actions\IntegrationSettings\GetFrontendIntegrationSettings;
use App\Enums\Permission;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? UserResource::make($request->user()->load('organizations')) : null,
                'permissions' => Permission::getPermissionsForUser($request->user()),
            ],
            'app' => [
                'name' => config('app.name'),
            ], 
            'integration_settings' => GetFrontendIntegrationSettings::run(),
            'config' => [
                'enable_billing' => config('subscriptions.enable_billing'),
            ],
            'translations' => [
                'shipments' => trans('shipments'),
            ],
        ];
    }
}
