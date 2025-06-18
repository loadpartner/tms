<?php

namespace App\Actions\IntegrationSettings;

use App\Models\Organizations\IntegrationSetting;
use App\Models\Organizations\Organization;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteIntegrationSetting
{
    use AsAction;

    /**
     * Delete an integration setting by key
     */
    public function handle(string $key) : bool
    {
        $organization = current_organization();

        if (!$organization) {
            throw new Exception("A current organization is required to delete integration settings");
        }

        $setting = $organization->integration_settings()
            ->where('key', $key)
            ->first();
        
        $result = $setting->delete();
        
        // Clear the cache for this key
        ClearIntegrationCaches::run($key, $setting->provider);
        
        return $result > 0;
    }

    public function asController(
        ActionRequest $request,
        Organization $organization,
        IntegrationSetting $setting
    )
    {
        return $this->handle($setting->key);
    }

    public function htmlResponse(bool $result)
    {
        if ($result) {
            return redirect()->back()->with('success', 'Integration setting deleted');
        }

        return redirect()->back()->with('error', 'Failed to delete integration setting');
    }

    public function rules()
    {
        return [

        ];
    }

    public function authorize(ActionRequest $request)
    {
        $setting = $request->route('setting');
        return Gate::allows('delete', $setting);
    }
} 