<?php

namespace App\Actions\IntegrationSettings;

use App\Models\Organizations\IntegrationSetting;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class GetProviderSettings
{
    use AsAction;

    protected int $cacheDuration = 3600;

    static function getCacheKey(string $provider) : string 
    {
        $organization = current_organization();
        if (!$organization) {
            throw new Exception("A current organization is required to get integration settings");
        }

        return "org_{$organization->id}_integration_provider_{$provider}";
    }
    /**
     * Get all settings for a specific provider
     * WARNING - this does not include any default settings from config/integrationsettings.php
     */
    public function handle(string $provider): Collection
    {
        $organization = current_organization();
        if (!$organization) {
            throw new Exception("A current organization is required to get integration settings");
        }

        $cacheKey = self::getCacheKey($provider);
        
        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($organization, $provider) {
            return $organization->integration_settings()
                ->where('provider', $provider)
                ->get()
                ->mapWithKeys(function (IntegrationSetting $setting) {
                    return [$setting->key => $setting->value];
                })
                ->toArray();
        });
    }
}