import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Organization, OrganizationInvite } from '@/types/organization';
import { Head } from '@inertiajs/react';
import InvitesTable from './Partials/InvitesTable';
import UsersTable from './Partials/UsersTable';

interface Subscription {
    id: number;
    type: string;
    stripe_status: string;
    quantity: number;
    trial_ends_at?: string;
    ends_at?: string;
}

interface SeatUsage {
    current_members: number;
    pending_invites: number;
    total_used: number;
    max_seats: number;
    has_available_seats: boolean;
    has_subscription: boolean;
}

export default function Users({
    organization,
    invites,
    subscription,
    seatUsage,
}: {
    organization: Organization;
    invites: OrganizationInvite[];
    subscription?: Subscription;
    seatUsage?: SeatUsage;
}) {
    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {
                    title: 'Organization',
                },
                {
                    title: 'Users',
                },
            ]}
        >
            <Head title="Organization" />

            <h1 className="mx-4 mb-4 text-2xl">
                Organization {organization.name} users
            </h1>

            <div className="mx-4 mb-4">
                <UsersTable
                    users={organization.users}
                    organization={organization}
                />
            </div>

            <div className="mx-4 mb-4">
                <h1 className="mb-4 text-xl">Pending Invites</h1>
                <InvitesTable
                    invites={invites}
                    organization={organization}
                    subscription={subscription}
                    seatUsage={seatUsage}
                />
            </div>
        </AuthenticatedLayout>
    );
}
