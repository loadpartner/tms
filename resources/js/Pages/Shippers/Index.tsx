import { Inertia, usePage } from '@inertiajs/inertia-react';
import { useState } from 'react';
import Skeleton from '@/components/Skeleton';
import { TabList, TabPanel, Tabs } from '@/components/Tabs';
import { Button, Input } from '@/components/ui';

interface Shipper {
  id: number;
  name: string;
  // other fields as needed
}

interface ShipperDetails {
  id: number;
  general: any;
  accounting: any;
  locations: any[];
  shipmentHistory: any[];
  contacts: any[];
  notes: any[];
  documents: any[];
}

const ShipperListPage = () => {
  const { shippers: initialShippers, shipper: initialShipperDetails } = usePage().props as {
    shippers: Shipper[];
    shipper?: ShipperDetails;
  };

  const [searchTerm, setSearchTerm] = useState('');
  const [selectedShipper, setSelectedShipper] = useState<Shipper | null>(null);
  const [shipperDetails, setShipperDetails] = useState<ShipperDetails | null>(initialShipperDetails || null);
  const [loadingDetails, setLoadingDetails] = useState(false);

  const filteredShippers = initialShippers.filter((shipper) =>
    shipper.name.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const handleRowClick = (shipper: Shipper) => {
    setSelectedShipper(shipper);
    setLoadingDetails(true);
    // Use Inertia partial reload to load detailed info about the selected shipper
    Inertia.get(
      route('shippers.details', shipper.id),
      {},
      {
        replace: true,
        preserveState: true,
        only: ['shipper'],
        onSuccess: (page: any) => {
          setShipperDetails(page.props.shipper);
          setLoadingDetails(false);
        },
        onError: () => {
          setLoadingDetails(false);
        }
      }
    );
  };

  return (
    <div className="flex h-full flex-col">
      <div className="p-4">
        <div className="mb-4 flex items-center justify-between">
          <h1 className="text-2xl font-bold">Shippers</h1>
          <Button onClick={() => Inertia.visit(route('shippers.create'))}>
            Add New Shipper
          </Button>
        </div>
        <Input
          type="text"
          placeholder="Search Shippers..."
          value={searchTerm}
          onChange={(e: React.ChangeEvent<HTMLInputElement>) => setSearchTerm(e.target.value)}
        />
      </div>
      <div className="flex flex-col flex-1">
        <div className="overflow-auto" style={{ maxHeight: '50%' }}>
          <table className="min-w-full">
            <thead>
              <tr>
                <th className="px-4 py-2">ID</th>
                <th className="px-4 py-2">Name</th>
                {/* Additional columns as needed */}
              </tr>
            </thead>
            <tbody>
              {filteredShippers.map((shipper) => (
                <tr
                  key={shipper.id}
                  onClick={() => handleRowClick(shipper)}
                  className={`cursor-pointer ${selectedShipper?.id === shipper.id ? 'bg-blue-100' : ''}`}
                >
                  <td className="border px-4 py-2">{shipper.id}</td>
                  <td className="border px-4 py-2">{shipper.name}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
        <div className="flex-1 overflow-auto border-t p-4">
          {loadingDetails ? (
            <Skeleton className="h-full" />
          ) : selectedShipper && shipperDetails ? (
            <div>
              <div className="mb-4 flex justify-between">
                <h2 className="text-xl font-semibold">{selectedShipper.name}</h2>
                <div className="space-x-2">
                  <Button onClick={() => Inertia.visit(route('shippers.loads', selectedShipper.id))}>
                    See Loads
                  </Button>
                  <Button onClick={() => Inertia.visit(route('shippers.edit', selectedShipper.id))}>
                    Edit
                  </Button>
                  <Button
                    onClick={() => {
                      // Trigger add note modal or action
                    }}
                  >
                    Add Note
                  </Button>
                  <Button
                    onClick={() => {
                      // Trigger add location modal or action
                    }}
                  >
                    Add Location
                  </Button>
                </div>
              </div>
              <Tabs>
                <TabList>
                  <li>General</li>
                  <li>Accounting</li>
                  <li>Locations</li>
                  <li>Shipment History</li>
                  <li>Contacts</li>
                  <li>Notes</li>
                  <li>Documents</li>
                </TabList>
                <TabPanel>
                  {shipperDetails.general ? shipperDetails.general : <Skeleton className="h-20" />}
                </TabPanel>
                <TabPanel>
                  {shipperDetails.accounting ? shipperDetails.accounting : <Skeleton className="h-20" />}
                </TabPanel>
                <TabPanel>
                  {shipperDetails.locations.length ? (
                    <ul>
                      {shipperDetails.locations.map((location, idx) => (
                        <li key={idx}>{location.address || 'Location'}</li>
                      ))}
                    </ul>
                  ) : (
                    <Skeleton className="h-20" />
                  )}
                </TabPanel>
                <TabPanel>
                  {shipperDetails.shipmentHistory.length ? (
                    <ul>
                      {shipperDetails.shipmentHistory.map((shipment, idx) => (
                        <li key={idx}>Shipment {shipment.id}</li>
                      ))}
                    </ul>
                  ) : (
                    <Skeleton className="h-20" />
                  )}
                </TabPanel>
                <TabPanel>
                  {shipperDetails.contacts.length ? (
                    <ul>
                      {shipperDetails.contacts.map((contact, idx) => (
                        <li key={idx}>{contact.name}</li>
                      ))}
                    </ul>
                  ) : (
                    <Skeleton className="h-20" />
                  )}
                </TabPanel>
                <TabPanel>
                  {shipperDetails.notes.length ? (
                    <ul>
                      {shipperDetails.notes.map((note, idx) => (
                        <li key={idx}>{note.content}</li>
                      ))}
                    </ul>
                  ) : (
                    <Skeleton className="h-20" />
                  )}
                </TabPanel>
                <TabPanel>
                  {shipperDetails.documents.length ? (
                    <ul>
                      {shipperDetails.documents.map((doc, idx) => (
                        <li key={idx}>{doc.filename}</li>
                      ))}
                    </ul>
                  ) : (
                    <Skeleton className="h-20" />
                  )}
                </TabPanel>
              </Tabs>
            </div>
          ) : (
            <div className="text-gray-500">
              Select a shipper to see details.
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default ShipperListPage; 