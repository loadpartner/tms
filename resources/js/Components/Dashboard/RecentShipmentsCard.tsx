import { Shipment } from '@/types';
import { useEffect, useState } from 'react';
import { columns } from '../Shipments/ShipmentList/Columns';
import { DataTable } from '../Shipments/ShipmentList/DataTable';
import { Loading } from '../ui/loading';
import DashboardCard from './DashboardCard';

export default function RecentShipmentsCard() {
    const [shipments, setShipments] = useState<Shipment[]>([]);
    const [loading, setLoading] = useState<boolean>(true);

    useEffect(() => {
        const fetchRecentShipments = async () => {
            try {
                setLoading(true);
                fetch(route('dashboard.cards.recent-shipments'), {
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                    },
                }).then(async (response) => {
                    setShipments(await response.json());
                    setLoading(false);
                });
            } catch (error) {
                console.error('Error fetching recent shipments:', error);
            } finally {
                setLoading(false);
            }
        };

        fetchRecentShipments();
    }, [setShipments]);

    return (
        <DashboardCard
            title="Recent Loads"
            cols={3}
            className="md:overflow-x-scroll"
        >
            {loading ? (
                <Loading
                    className="mx-auto h-[200px] w-full"
                    text="Loading..."
                />
            ) : (
                <DataTable columns={columns} data={shipments} />
            )}
        </DashboardCard>
    );
}
