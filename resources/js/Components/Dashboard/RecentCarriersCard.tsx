import { Carrier } from '@/types';
import { router } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { columns } from '../Carriers/CarrierList/Columns';
import { DataTable } from '../Carriers/CarrierList/DataTable';
import { Loading } from '../ui/loading';
import DashboardCard from './DashboardCard';

export default function RecentCarriersCard() {
    const [carriers, setCarriers] = useState<Carrier[]>([]);
    const [loading, setLoading] = useState<boolean>(true);

    useEffect(() => {
        const fetchRecentCarriers = async () => {
            try {
                setLoading(true);
                fetch(route('dashboard.cards.recent-carriers'), {
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                    },
                }).then(async (response) => {
                    setCarriers(await response.json());
                    setLoading(false);
                });
            } catch (error) {
                console.error('Error fetching recent carriers:', error);
            } finally {
                setLoading(false);
            }
        };

        fetchRecentCarriers();
    }, [setCarriers]);

    return (
        <DashboardCard
            title="Recent Carriers"
            cols={3}
            className="md:overflow-x-scroll"
        >
            {loading ? (
                <Loading
                    className="mx-auto h-[200px] w-full"
                    text="Loading..."
                />
            ) : (
                <DataTable
                    columns={columns}
                    data={carriers}
                    onSelect={(carrier) =>
                        router.visit(route('carriers.show', { carrier }))
                    }
                />
            )}
        </DashboardCard>
    );
}
