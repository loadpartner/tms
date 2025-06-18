<?php

namespace App\Actions\IntegrationSettings;

use Exception;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class GetIntegrationSetting
{
    use AsAction;

    protected int $cacheDuration = 3600;

    static function getCacheKey(string $key) : string 
    {
        $organization = current_organization();
        if (!$organization) {
            throw new Exception("A current organization is required to get integration settings");
        }

        return "org_{$organization->id}_integration_{$key}";
    }

    public function handle(string $key)
    {
        $organization = current_organization();
        if (!$organization) {
            throw new Exception("A current organization is required to get integration settings");
        }

        $cacheKey = self::getCacheKey($key);
        
        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($organization, $key) {
            $setting = $organization->integration_settings()
                ->where('key', $key)
                ->first();
            
            return $setting ? $setting->value : config(sprintf('globalintegrationsettings.%s.value', $key));
        });
    }
}