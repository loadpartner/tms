<?php

namespace App\Actions\IntegrationSettings;

use App\Models\Organizations\IntegrationSetting;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * This will get frontend integration settings plus any fallback defaults
 */
class GetFrontendIntegrationSettings
{
    use AsAction;

    protected int $cacheDuration = 3600;

    static function getCacheKey() : string 
    {
        $organization = current_organization();
        if (!$organization) {
            throw new Exception("A current organization is required to get frontend integration settings");
        }

        return "org_{$organization->id}_frontend_integration_settings";
    }

    public function handle() : Collection
    {
        $organization = current_organization();
        if (!$organization) {
            return collect();
        }

        $cacheKey = self::getCacheKey();
        
        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($organization) {

            // get global settings, look for keys that are exposed to frontend
            // and map key=>value
            $settings = collect(config('globalintegrationsettings'))
                ->filter(function ($setting) {
                    return $setting['expose_to_frontend'];
                })->mapWithKeys(function ($setting, $key) {
                    return [$key => $setting['value']];
                })->toArray();

            // Get all organization settings that are exposed to frontend
            $orgSettings = $organization->integration_settings()
                ->where('expose_to_frontend', true)
                ->get()
                ->mapWithKeys(function (IntegrationSetting $setting) {
                    return [$setting->key => $setting->value];
                })
                ->toArray();

            
            // Merge default settings with organization settings
            // Organization settings will override defaults with the same keys
            return collect(array_merge($settings, $orgSettings));
        });
    }
}