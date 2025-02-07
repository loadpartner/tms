import { Button } from '@/Components/ui/button';
import { Shipper } from '@/types';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs';
import { Skeleton } from '@/Components/ui/skeleton';
import { useState } from 'react';
import Notes from '@/Components/Notes';
import { Notable } from '@/types/enums';
import ShipperLoadsDialog from './ShipperLoadsDialog';
import EditShipperDialog from './EditShipperDialog';
import CreateLocationDialog from './CreateLocationDialog';

interface Props {
    shipper: Shipper;
}

export default function ShipperDetails({ shipper }: Props) {
    const [loadingDetails, setLoadingDetails] = useState(false);
    const [shipperLoadsDialogOpen, setShipperLoadsDialogOpen] = useState(false);
    const [editShipperDialogOpen, setEditShipperDialogOpen] = useState(false);
    const [createLocationDialogOpen, setCreateLocationDialogOpen] = useState(false);

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <h2 className="text-2xl font-bold">{shipper.name}</h2>
                <div className="flex gap-2">
                    <Button
                        variant="secondary"
                        onClick={() => setShipperLoadsDialogOpen(true)}
                    >
                        View Loads
                    </Button>
                    <Button
                        variant="secondary"
                        onClick={() => setEditShipperDialogOpen(true)}
                    >
                        Edit
                    </Button>
                    <Button
                        variant="secondary"
                        onClick={() => setCreateLocationDialogOpen(true)}
                    >
                        Add Location
                    </Button>
                </div>
            </div>

            <Tabs defaultValue="general" className="w-full">
                <TabsList className="w-full justify-start">
                    <TabsTrigger value="general">General</TabsTrigger>
                    <TabsTrigger value="accounting">Accounting</TabsTrigger>
                    <TabsTrigger value="locations">Locations</TabsTrigger>
                    <TabsTrigger value="shipmentHistory">
                        Shipment History
                    </TabsTrigger>
                    <TabsTrigger value="contacts">Contacts</TabsTrigger>
                    <TabsTrigger value="notes">Notes</TabsTrigger>
                    <TabsTrigger value="documents">Documents</TabsTrigger>
                </TabsList>

                <TabsContent value="general">
                    {loadingDetails ? (
                        <Skeleton className="h-48 w-full" />
                    ) : (
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <h3 className="font-semibold mb-2">
                                    Basic Information
                                </h3>
                                <div className="space-y-2">
                                    <p>
                                        <span className="text-muted-foreground">
                                            Name:
                                        </span>{' '}
                                        {shipper.name}
                                    </p>
                                    <p>
                                        <span className="text-muted-foreground">
                                            Address:
                                        </span>{' '}
                                        {shipper.address}
                                    </p>
                                    <p>
                                        <span className="text-muted-foreground">
                                            City:
                                        </span>{' '}
                                        {shipper.city}
                                    </p>
                                    <p>
                                        <span className="text-muted-foreground">
                                            State:
                                        </span>{' '}
                                        {shipper.state}
                                    </p>
                                    <p>
                                        <span className="text-muted-foreground">
                                            ZIP:
                                        </span>{' '}
                                        {shipper.zip}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <h3 className="font-semibold mb-2">
                                    Contact Information
                                </h3>
                                <div className="space-y-2">
                                    <p>
                                        <span className="text-muted-foreground">
                                            Contact Name:
                                        </span>{' '}
                                        {shipper.contact_name}
                                    </p>
                                    <p>
                                        <span className="text-muted-foreground">
                                            Phone:
                                        </span>{' '}
                                        {shipper.phone}
                                    </p>
                                    <p>
                                        <span className="text-muted-foreground">
                                            Email:
                                        </span>{' '}
                                        {shipper.email}
                                    </p>
                                </div>
                            </div>
                        </div>
                    )}
                </TabsContent>

                <TabsContent value="accounting">
                    <Skeleton className="h-48 w-full" />
                </TabsContent>

                <TabsContent value="locations">
                    <Skeleton className="h-48 w-full" />
                </TabsContent>

                <TabsContent value="shipmentHistory">
                    <Skeleton className="h-48 w-full" />
                </TabsContent>

                <TabsContent value="contacts">
                    <Skeleton className="h-48 w-full" />
                </TabsContent>

                <TabsContent value="notes">
                    <Notes
                        notableType={Notable.Shipper}
                        notableId={shipper.id}
                    />
                </TabsContent>

                <TabsContent value="documents">
                    <Skeleton className="h-48 w-full" />
                </TabsContent>
            </Tabs>

            <ShipperLoadsDialog
                open={shipperLoadsDialogOpen}
                setOpen={setShipperLoadsDialogOpen}
                shipperId={shipper.id}
            />
            <EditShipperDialog
                open={editShipperDialogOpen}
                setOpen={setEditShipperDialogOpen}
                shipper={shipper}
            />
            <CreateLocationDialog
                open={createLocationDialogOpen}
                setOpen={setCreateLocationDialogOpen}
                shipperId={shipper.id}
            />
        </div>
    );
} 