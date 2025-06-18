<?php

namespace App\Providers;

use App\Models\Organizations\Organization;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Cashier::useCustomerModel(Organization::class);

        Vite::prefetch(concurrency: 3);

        Gate::guessPolicyNamesUsing(function (string $modelClass) {
            return str_replace('Models', 'Policies', $modelClass) . 'Policy';
        });

        Gate::before(function ($user, $ability) {
            if (current_organization()?->owner_id === $user->id) {
                return true;
            }
            return null;    // check if user has permission via normal flow
        });

        /** @var Kernel $kernel */
        $kernel = app()->make(Kernel::class);

        JsonResource::withoutWrapping();
    }
}
