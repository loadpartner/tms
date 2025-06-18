import { useIntegrationSettings } from '@/hooks/useIntegrationSettings';
import { Shipment } from '@/types';
import { GoogleMap, Marker, useLoadScript } from '@react-google-maps/api';
import { useEffect, useState } from 'react';

// Set default map container style
const mapContainerStyle = {
    width: '100%',
    height: '400px',
};

// Default center (will be overridden by actual data)
const defaultCenter = {
    lat: 39.8283, // Default US center
    lng: -98.5795,
};

export default function LocationMap({ shipment }: { shipment: Shipment }) {
    const [mapCenter, setMapCenter] = useState(defaultCenter);
    const [markers, setMarkers] = useState<
        Array<{
            lat: number;
            lng: number;
            label: string;
            stopType: string;
        }>
    >([]);

    const { getGoogleMapsApiKey } = useIntegrationSettings();
    const googleMapsApiKey = getGoogleMapsApiKey();

    // Load Google Maps script
    const { isLoaded, loadError } = useLoadScript({
        googleMapsApiKey: googleMapsApiKey || '',
    });

    useEffect(() => {
        if (shipment && shipment.stops && shipment.stops.length > 0) {
            // Process stops with valid facilities and locations
            const validStops = shipment.stops.filter(
                (stop) => stop.facility && stop.facility.location,
            );

            if (validStops.length > 0) {
                // Process addresses into geocoded points
                const geocodeAddresses = async () => {
                    const newMarkers = await Promise.all(
                        validStops.map(async (stop, index) => {
                            const location = stop.facility?.location;
                            if (!location) return null;

                            try {
                                // Use actual latitude and longitude if available
                                if (location.latitude && location.longitude) {
                                    return {
                                        lat: location.latitude,
                                        lng: location.longitude,
                                        label: `${index + 1}`,
                                        stopType: stop.stop_type,
                                    };
                                }

                                // Fallback to address-based calculation if coordinates aren't available
                                // In a production environment, you would use a geocoding service here
                                console.warn(
                                    `Location ${location.id} missing latitude/longitude coordinates. skippin.`,
                                );

                                return null;
                            } catch (error) {
                                console.error('Geocoding error:', error);
                                return null;
                            }
                        }),
                    );

                    // Filter out null values
                    const filteredMarkers = newMarkers.filter(
                        (marker) => marker !== null,
                    ) as Array<{
                        lat: number;
                        lng: number;
                        label: string;
                        stopType: string;
                    }>;

                    // Update markers
                    setMarkers(filteredMarkers);

                    // Set center to first marker if available
                    if (filteredMarkers.length > 0) {
                        setMapCenter({
                            lat: filteredMarkers[0].lat,
                            lng: filteredMarkers[0].lng,
                        });
                    }
                };

                geocodeAddresses();
            }
        }
    }, [shipment]);

    if (loadError) {
        return <div>Error loading maps</div>;
    }

    if (!isLoaded) {
        return <div>Loading maps...</div>;
    }

    if (!googleMapsApiKey) {
        return (
            <div className="rounded-md border border-amber-200 bg-amber-50 p-4 text-amber-800 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-200">
                <div className="flex items-start">
                    <div className="mr-3 flex-shrink-0">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            strokeWidth="2"
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            className="h-5 w-5"
                        >
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                    <div>
                        <h3 className="text-sm font-medium">
                            Google Maps API Key Required
                        </h3>
                        <p className="mt-1 text-sm">
                            A Google Maps API key is required to display the
                            location map. Please add your API key to the
                            environment variables.
                        </p>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div>
            {isLoaded && (
                <GoogleMap
                    mapContainerStyle={mapContainerStyle}
                    zoom={5}
                    center={mapCenter}
                >
                    {markers.map((marker, index) => (
                        <Marker
                            key={index}
                            position={{ lat: marker.lat, lng: marker.lng }}
                            label={{ text: marker.label, color: 'white' }}
                        />
                    ))}
                </GoogleMap>
            )}

            <div className="mt-4">
                <div className="flex items-center space-x-6 text-sm">
                    <div className="flex items-center">
                        <div className="mr-2 h-4 w-4 rounded-full bg-red-500"></div>
                        <span>Pickup Locations</span>
                    </div>
                    <div className="flex items-center">
                        <div className="mr-2 h-4 w-4 rounded-full bg-blue-500"></div>
                        <span>Delivery Locations</span>
                    </div>
                </div>
            </div>
        </div>
    );
}
