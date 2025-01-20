import { buttonVariants } from '@/Components/ui/button';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function Show({ shipment }: { shipment: any }) {
    return (
        <AuthenticatedLayout>
            <Head title="Shipments" />
            <div className="flex px-8">
                Viewing Shipment {shipment.id}
            </div>
        </AuthenticatedLayout>
    );
}
