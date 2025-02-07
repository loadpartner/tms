import { Head } from '@inertiajs/react';
import { PageProps, Shipper } from '@/types';
import { useState } from 'react';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs';
import { Skeleton } from '@/Components/ui/skeleton';
import ShipperList from './Partials/ShipperList';
import ShipperDetails from './Partials/ShipperDetails';

export default function Index({ auth }: PageProps) {
    const [selectedShipper, setSelectedShipper] = useState<Shipper | null>(null);

    return (
        <>
            <Head title="Shippers" />
            <div className="container mx-auto py-6 space-y-6">
                <div className="flex justify-between items-center">
                    <h1 className="text-3xl font-bold">Shippers</h1>
                </div>

                <div className="grid grid-rows-[1fr,1fr] gap-6 h-[calc(100vh-12rem)]">
                    <div className="bg-white rounded-lg shadow">
                        <ShipperList onShipperSelect={setSelectedShipper} selectedShipper={selectedShipper} />
                    </div>

                    <div className="bg-white rounded-lg shadow p-6">
                        {selectedShipper ? (
                            <ShipperDetails shipper={selectedShipper} />
                        ) : (
                            <div className="flex items-center justify-center h-full text-gray-500">
                                Select a shipper to view details
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
} 