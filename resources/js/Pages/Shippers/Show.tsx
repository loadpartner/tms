import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs';
import ShipperGeneral from './Components/ShipperGeneral';
import ShipperLocations from './Components/ShipperLocations';
import ShipperContacts from './Components/ShipperContacts';
import ShipperNotes from './Components/ShipperNotes';
import ShipperDocuments from './Components/ShipperDocuments';

export default function ShipperShow({ shipper }) {
    return (
        <AuthenticatedLayout>
            <Head title={shipper.name} />
            <div className="p-6">
                <h1 className="text-2xl font-bold mb-6">{shipper.name}</h1>
                
                <Tabs defaultValue="general" className="w-full">
                    <TabsList>
                        <TabsTrigger value="general">General</TabsTrigger>
                        <TabsTrigger value="locations">Locations</TabsTrigger>
                        <TabsTrigger value="contacts">Contacts</TabsTrigger>
                        <TabsTrigger value="notes">Notes</TabsTrigger>
                        <TabsTrigger value="documents">Documents</TabsTrigger>
                    </TabsList>

                    <TabsContent value="general">
                        <ShipperGeneral shipper={shipper} />
                    </TabsContent>
                    <TabsContent value="locations">
                        <ShipperLocations shipper={shipper} />
                    </TabsContent>
                    <TabsContent value="contacts">
                        <ShipperContacts shipper={shipper} />
                    </TabsContent>
                    <TabsContent value="notes">
                        <ShipperNotes shipper={shipper} />
                    </TabsContent>
                    <TabsContent value="documents">
                        <ShipperDocuments shipper={shipper} />
                    </TabsContent>
                </Tabs>
            </div>
        </AuthenticatedLayout>
    );
} 