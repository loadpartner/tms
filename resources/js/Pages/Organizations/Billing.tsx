import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Organization } from '@/types/organization';
import { Head } from '@inertiajs/react';
import BillingForm from './Partials/BillingForm';

interface Subscription {
    id: number;
    type: string;
    stripe_status: string;
    quantity: number;
    trial_ends_at?: string;
    ends_at?: string;
}

export default function Billing({
    organization,
    subscription,
}: {
    organization: Organization;
    subscription?: Subscription;
}) {
    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {
                    title: 'Organization',
                },
                {
                    title: 'Billing',
                },
            ]}
        >
            <Head title="Organization Billing" />

            <h1 className="mx-4 mb-4 text-2xl">
                Organization {organization.name} billing
            </h1>

            <div className="mx-4 mb-4">
                <BillingForm
                    organization={organization}
                    subscription={subscription}
                />
            </div>
        </AuthenticatedLayout>
    );
}
