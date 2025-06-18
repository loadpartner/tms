import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Organization, Permission, Role } from '@/types/organization';
import { Head } from '@inertiajs/react';
import RolesTable from './Partials/RolesTable';

export default function Show({
    organization,
    roles,
    permissions,
}: {
    organization: Organization;
    roles: Role[];
    permissions: Permission[];
}) {
    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {
                    title: 'Organization',
                },
                {
                    title: 'Roles',
                },
            ]}
        >
            <Head title="Organization" />

            <h1 className="mx-4 mb-4 text-2xl">
                Organization {organization.name} roles
            </h1>

            <div className="mx-4 mb-4">
                <RolesTable
                    roles={roles}
                    organization={organization}
                    permissions={permissions}
                />
            </div>
        </AuthenticatedLayout>
    );
}
