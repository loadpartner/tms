import { useIntegrationSettings } from '@/hooks/useIntegrationSettings';
import { Location } from '@/types';
import GooglePlacesAutocomplete, {
    geocodeByAddress,
} from 'react-google-places-autocomplete';

export default function AddressSearch({
    onAddressSelect,
}: {
    onAddressSelect: (location: Location) => void;
}) {
    const findFullDetails = (address: string) => {
        geocodeByAddress(address)
            .then((results) => {
                if (results && results.length > 0) {
                    const result = results[0];
                    const addressComponents = result.address_components;

                    let street_number = '';
                    let route = '';
                    let city = '';
                    let state = '';
                    let zipcode = '';
                    let locationName = address;
                    const lat = result.geometry.location.lat();
                    const lng = result.geometry.location.lng();

                    // Extract address components
                    addressComponents.forEach((component) => {
                        const types = component.types;

                        if (component.long_name) {
                            locationName = locationName.replace(
                                component.long_name,
                                '',
                            );
                        }
                        if (component.short_name) {
                            locationName = locationName.replace(
                                component.short_name,
                                '',
                            );
                        }

                        if (types.includes('street_number')) {
                            street_number = component.long_name;
                        }

                        if (types.includes('route')) {
                            route = component.long_name;
                        }

                        if (types.includes('locality')) {
                            city = component.long_name;
                        }

                        if (types.includes('administrative_area_level_1')) {
                            state = component.long_name;
                        }

                        if (types.includes('postal_code')) {
                            zipcode = component.long_name;
                        }
                    });

                    locationName = address.split(',')[0].trim();

                    // Create Location object
                    const location: Location = {
                        id: -1,
                        name: locationName,
                        address_line_1: street_number
                            ? `${street_number} ${route}`
                            : route,
                        address_line_2: '',
                        address_city: city,
                        address_state: state,
                        address_zipcode: zipcode,
                        latitude: lat,
                        longitude: lng,
                        selectable_label: result.formatted_address,
                    };

                    // Call the onAddressSelect prop with the location
                    onAddressSelect(location);
                }
            })
            .catch((error) => console.error(error));
    };

    const { getGoogleMapsApiKey } = useIntegrationSettings();
    const googleMapsApiKey = getGoogleMapsApiKey();

    return (
        <GooglePlacesAutocomplete
            apiKey={googleMapsApiKey}
            selectProps={{
                // eslint-disable-next-line @typescript-eslint/no-explicit-any
                onChange: (result: any) => {
                    findFullDetails(result?.label);
                },
            }}
        />
    );
}
