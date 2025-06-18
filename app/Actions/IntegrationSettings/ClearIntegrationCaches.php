<?php

namespace App\Actions\IntegrationSettings;

use Exception;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class ClearIntegrationCaches
{
    use AsAction;

    /**
     * Set an integration setting value
     */
    public function handle(string $key, ?string $provider = null)
    {

        $organization = current_organization();

        if (!$organization) {
            throw new Exception("A current organization is required to clear integration caches");
        }
        // Clear the cache for this key
        Cache::forget(GetIntegrationSetting::getCacheKey($key));
        Cache::forget(GetFrontendIntegrationSettings::getCacheKey());

        if ($provider) {
            Cache::forget(GetProviderSettings::getCacheKey($provider));
        }

        return true;
    }
}