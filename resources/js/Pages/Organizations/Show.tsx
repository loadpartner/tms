import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Organization,
    OrganizationInvite,
    Permission,
    Role,
} from '@/types/organization';
import { Head, usePage } from '@inertiajs/react';
import InvitesTable from './Partials/InvitesTable';
import RolesTable from './Partials/RolesTable';
import UsersTable from './Partials/UsersTable';

export default function Show({
    organization,
    invites,
    roles,
    permissions,
}: {
    organization: Organization;
    invites: OrganizationInvite[];
    roles: Role[];
    permissions: Permission[];
}) {
    const userPermissions = usePage().props.auth.permissions;

    const canManageOrganization = userPermissions.ORGANIZATION_MANAGER;

    return (
        <AuthenticatedLayout>
            <Head title="Organization" />

            <h1 className="mx-4 mb-4 text-2xl">
                Organization {organization.name}
            </h1>

            <Tabs defaultValue="users">
                <TabsList className="mx-auto grid w-full max-w-[500px] grid-cols-2">
                    <TabsTrigger value="users">Users</TabsTrigger>
                    <TabsTrigger
                        value="roles"
                        disabled={!canManageOrganization}
                    >
                        Roles
                    </TabsTrigger>
                </TabsList>
                <TabsContent value="users">
                    <div className="mx-4 mb-4">
                        <UsersTable
                            users={organization.users}
                            organization={organization}
                        />
                    </div>

                    <div className="mx-4 mb-4">
                        <h1 className="mb-4 text-xl">Pending Invites</h1>
                        <InvitesTable invites={invites} />
                    </div>
                </TabsContent>
                {canManageOrganization && (
                    <TabsContent value="roles">
                        <div className="mx-4 mb-4">
                            <RolesTable
                                roles={roles}
                                organization={organization}
                                permissions={permissions}
                            />
                        </div>
                    </TabsContent>
                )}
            </Tabs>
        </AuthenticatedLayout>
    );
}
