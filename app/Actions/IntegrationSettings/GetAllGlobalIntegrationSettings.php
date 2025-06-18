<?php

namespace App\Actions\IntegrationSettings;

use App\Models\Organizations\IntegrationSetting;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class GetAllGlobalIntegrationSettings
{
    use AsAction;
    
    /**
     * Gets all possible integration settings for the current organization
     * based on the config/globalintegrationsettings.php file
     */
    public function handle(): Collection
    {
        return collect(config('globalintegrationsettings'))->map(function ($setting, $key) {
            return [
                'key' => $key,
                ...$setting
            ];
        })->values();
    }
}