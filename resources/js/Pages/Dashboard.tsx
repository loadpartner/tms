import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Skeleton } from '@/Components/ui/skeleton';
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

            <div className="py-12">
                <div className="mx-auto flex max-w-7xl flex-col gap-4 sm:px-6 lg:px-8">
                    <Card>
                        <CardHeader>
                            <CardTitle>{getGreeting()}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Skeleton className="h-48 w-full" />
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader>
                            <CardTitle>Recent Activity</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Skeleton className="h-48 w-full" />
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader>
                            <CardTitle>Upcoming Loads</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Skeleton className="h-48 w-full" />
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
