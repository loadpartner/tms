import { usePage } from '@inertiajs/react';

/**
 * Hook to access integration settings from page props
 */
export function useIntegrationSettings() {
    const { integration_settings } = usePage().props;

    /**
     * Get a specific integration setting by key
     */
    const getIntegrationSetting = (key: string): string => {
        return integration_settings[key];
    };

    /**
     * Get Google Maps API key
     */
    const getGoogleMapsApiKey = (): string => {
        return getIntegrationSetting('google_maps.api_key');
    };

    return {
        getIntegrationSetting,
        getGoogleMapsApiKey,
        settings: integration_settings,
    };
}
