import DocumentsList from '@/Components/Documents/DocumentsList';
import LocationMap from '@/Components/Shipments/LocationMap';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { ComingSoon } from '@/Components/ui/coming-soon';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Shipment, ShipmentStop, TrailerSize, TrailerType } from '@/types';
import { Documentable } from '@/types/enums';
import { Head } from '@inertiajs/react';
import { Folder, MapPin } from 'lucide-react';
import CarrierDetails from './Partials/CarrierDetails';
import CustomerDetails from './Partials/CustomerDetails';
import ShipmentAccountingDetails from './Partials/ShipmentAccountingDetails';
import ShipmentCheckCalls from './Partials/ShipmentCheckCalls';
import ShipmentGeneral from './Partials/ShipmentGeneral';
import ShipmentHeader from './Partials/ShipmentHeader';
import ShipmentNotes from './Partials/ShipmentNotes';
import ShipmentStopsList from './Partials/ShipmentStopsList';

export default function Show({
    shipment,
    stops,
    trailerTypes,
    trailerSizes,
}: {
    shipment: Shipment;
    stops: ShipmentStop[];
    trailerTypes: TrailerType[];
    trailerSizes: TrailerSize[];
}) {
    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {
                    title: 'Shipments',
                    href: route('shipments.index'),
                },
                {
                    title: `${shipment.shipment_number ?? `id:${shipment.id}`}`,
                },
            ]}
        >
            <Head title="Load Details" />

            <div className="mx-auto max-w-screen-2xl space-y-4 p-4 md:p-8">
                {/* Header with key load info */}
                <ShipmentHeader shipment={shipment} />

                <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
                    {/* Main Content Area - 2 columns */}
                    <div className="space-y-4 lg:col-span-2">
                        {/* Map View Card */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <MapPin className="h-5 w-5" />
                                    Location Tracking
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <LocationMap shipment={shipment} />
                            </CardContent>
                        </Card>

                        {/* Stops Timeline */}
                        <ShipmentStopsList
                            shipmentId={shipment.id}
                            stops={stops}
                        />

                        {/* Accounting */}
                        <ShipmentAccountingDetails shipment={shipment} />

                        {/* Notes */}
                        <ShipmentNotes shipmentId={shipment.id} />
                    </div>

                    {/* Sidebar - 1 column */}
                    <div className="space-y-4">
                        {/* General */}
                        <ShipmentGeneral
                            shipment={shipment}
                            trailerTypes={trailerTypes}
                            trailerSizes={trailerSizes}
                        />

                        {/* Carrier Details */}
                        <CarrierDetails shipment={shipment} />

                        {/* Customers */}
                        <CustomerDetails shipment={shipment} />

                        {/* Check Calls */}
                        <ShipmentCheckCalls
                            shipment={shipment}
                            stops={stops}
                            carrierContacts={shipment.carrier?.contacts || []}
                        />

                        {/* Activity Feed */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Recent Activity</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-2">
                                <ComingSoon
                                    variant="outline"
                                    className="mx-auto"
                                />
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center justify-between gap-2">
                                    <div className="flex items-center gap-2">
                                        <Folder className="h-5 w-5" />
                                        Documents
                                    </div>
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="">
                                <DocumentsList
                                    documents={shipment.documents ?? []}
                                    folders={shipment.document_folders ?? []}
                                    documentableType={Documentable.Shipment}
                                    documentableId={shipment.id}
                                />
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
