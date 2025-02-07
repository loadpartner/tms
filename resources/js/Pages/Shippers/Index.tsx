import React, { useState, useEffect } from 'react';
import { Head, Link, usePage, router } from '@inertiajs/react';
import { Shipper } from '@/types'; // Update your types as needed
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Button } from '@/Components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/Components/ui/table"
import { Skeleton } from "@/Components/ui/skeleton";
import Authenticated from '@/Layouts/AuthenticatedLayout';
import { PageProps } from '@/types';
import ShipperLoadsDialog from '@/Pages/Shippers/Partials/ShipperLoadsDialog';
import EditShipperDialog from '@/Pages/Shippers/Partials/EditShipperDialog';
import CreateNoteDialog from '@/Pages/Shipments/Partials/CreateNoteDialog'; // Reusing existing component
import CreateLocationDialog from '@/Pages/Shippers/Partials/CreateLocationDialog';


interface Props extends PageProps {
    shippers: Shipper[];
}

export default function Index({ auth, shippers }: Props) {
    const [selectedShipper, setSelectedShipper] = useState<Shipper | null>(null);
    const [searchTerm, setSearchTerm] = useState('');
    const [loadingShipperDetails, setLoadingShipperDetails] = useState(false);
    const [shipperLoadsDialogOpen, setShipperLoadsDialogOpen] = useState(false);
    const [editShipperDialogOpen, setEditShipperDialogOpen] = useState(false);
    const [createNoteDialogOpen, setCreateNoteDialogOpen] = useState(false);
    const [createLocationDialogOpen, setCreateLocationDialogOpen] = useState(false);

    const filteredShippers = shippers.filter((shipper) =>
        shipper.name.toLowerCase().includes(searchTerm.toLowerCase())
    );

    useEffect(() => {
        if (selectedShipper) {
            setLoadingShipperDetails(true);
            router.visit(route('shippers.show', selectedShipper.id), {
                preserveState: true,
                only: ['shipper'],
                onSuccess: () => {
                    setLoadingShipperDetails(false);
                },
            });
        }
    }, [selectedShipper]);

    const { props } = usePage<Props>();
    const shipper = props.shipper as Shipper | null; // Shipper details from partial reload

    const handleShipperClick = (shipper: Shipper) => {
        if (selectedShipper?.id === shipper.id) {
            setSelectedShipper(null);
        }
        else {
            setSelectedShipper(shipper);
        }
    };

    return (
        <Authenticated auth={auth}>
            <Head title="Shippers" />
            <div className="grid grid-cols-1 gap-4">
                <Card>
                    <CardHeader>
                        <CardTitle>Shippers</CardTitle>
                        <div className="flex items-center space-x-2">
                            <Input
                                placeholder="Search shippers..."
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                            />
                            <Link href={route('shippers.create')}>
                                <Button>Add Shipper</Button>
                            </Link>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Name</TableHead>
                                    <TableHead>City</TableHead>
                                    <TableHead>State</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {filteredShippers.map((shipper) => (
                                    <TableRow
                                        key={shipper.id}
                                        onClick={() => handleShipperClick(shipper)}
                                        className={selectedShipper?.id === shipper.id ? 'bg-gray-100 cursor-pointer' : 'cursor-pointer hover:bg-gray-50'}
                                    >
                                        <TableCell>{shipper.name}</TableCell>
                                        <TableCell>{shipper.city}</TableCell>
                                        <TableCell>{shipper.state}</TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                {/* Shipper Details */}
                {selectedShipper && (
                    <Card>
                        <CardHeader>
                            <CardTitle>
                                {loadingShipperDetails ? (
                                    <Skeleton className="h-6 w-1/4" />
                                ) : (
                                    <span>{shipper?.name}</span>
                                )}
                            </CardTitle>
                            <div className="flex space-x-2">
                                <Button variant="secondary" onClick={() => setShipperLoadsDialogOpen(true)}>
                                    View Loads
                                </Button>
                                <Button variant="secondary" onClick={() => setEditShipperDialogOpen(true)}>
                                    Edit
                                </Button>
                                <Button variant="secondary" onClick={() => setCreateNoteDialogOpen(true)}>
                                    Add Note
                                </Button>
                                <Button variant="secondary" onClick={() => setCreateLocationDialogOpen(true)}>
                                    Add Location
                                </Button>
                            </div>

                        </CardHeader>
                        <CardContent>
                            {loadingShipperDetails ? (
                                <Skeleton className="h-4 w-full" />
                            ) : (
                                <Tabs defaultValue="general">
                                    <TabsList>
                                        <TabsTrigger value="general">General</TabsTrigger>
                                        <TabsTrigger value="accounting">Accounting</TabsTrigger>
                                        <TabsTrigger value="locations">Locations</TabsTrigger>
                                        <TabsTrigger value="shipmentHistory">Shipment History</TabsTrigger>
                                        <TabsTrigger value="contacts">Contacts</TabsTrigger>
                                        <TabsTrigger value="notes">Notes</TabsTrigger>
                                        <TabsTrigger value="documents">Documents</TabsTrigger>
                                    </TabsList>
                                    <TabsContent value="general"><Skeleton className="h-4 w-full" /></TabsContent>
                                    <TabsContent value="accounting"><Skeleton className="h-4 w-full" /></TabsContent>
                                    <TabsContent value="locations"><Skeleton className="h-4 w-full" /></TabsContent>
                                    <TabsContent value="shipmentHistory"><Skeleton className="h-4 w-full" /></TabsContent>
                                    <TabsContent value="contacts"><Skeleton className="h-4 w-full" /></TabsContent>
                                    <TabsContent value="notes"><Skeleton className="h-4 w-full" /></TabsContent>
                                    <TabsContent value="documents"><Skeleton className="h-4 w-full" /></TabsContent>
                                </Tabs>
                            )}
                        </CardContent>
                    </Card>
                )}
            </div>
            <ShipperLoadsDialog open={shipperLoadsDialogOpen} setOpen={setShipperLoadsDialogOpen} shipperId={selectedShipper?.id} />
            <EditShipperDialog open={editShipperDialogOpen} setOpen={setEditShipperDialogOpen} shipper={selectedShipper} />
            {selectedShipper && <CreateNoteDialog open={createNoteDialogOpen} setOpen={setCreateNoteDialogOpen} shipmentId={null} shipperId={selectedShipper.id} />}
            <CreateLocationDialog open={createLocationDialogOpen} setOpen={setCreateLocationDialogOpen} shipperId={selectedShipper?.id} />
        </Authenticated>
    );
} 