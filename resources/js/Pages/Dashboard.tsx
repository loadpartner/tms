import DashboardCard from '@/Components/Dashboard/DashboardCard';
import RecentCarriersCard from '@/Components/Dashboard/RecentCarriersCard';
import RecentShipmentsCard from '@/Components/Dashboard/RecentShipmentsCard';
import { ComingSoon } from '@/Components/ui/coming-soon';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard() {
    const getGreeting = () => {
        const hour = new Date().getHours();
        const day = new Date().toLocaleDateString('en-US', { weekday: 'long' });

        let timeGreeting;
        if (hour < 12) timeGreeting = 'Good morning';
        else if (hour < 17) timeGreeting = 'Good afternoon';
        else timeGreeting = 'Good evening';

        return `${timeGreeting}! Happy ${day}!`;
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {
                    title: 'Dashboard',
                },
            ]}
        >
            <Head title="Dashboard" />

            <div className="mx-auto flex max-w-7xl flex-wrap gap-4 py-12 sm:px-6 md:grid md:grid-cols-3 lg:px-8">
                <span className="bold col-span-3 px-2 text-lg">
                    {getGreeting()}
                </span>
                <DashboardCard title="Last 7 days">
                    <ComingSoon variant="outline" />
                </DashboardCard>
                <DashboardCard title="Last 30 days">
                    <ComingSoon variant="outline" />
                </DashboardCard>
                <DashboardCard title="Year-to-date">
                    <ComingSoon variant="outline" />
                </DashboardCard>

                <RecentShipmentsCard />
                <RecentCarriersCard />
            </div>
        </AuthenticatedLayout>
    );
}
