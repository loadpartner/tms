import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Organization } from '@/types/organization';
import { Head } from '@inertiajs/react';
import SettingsForm from './Partials/SettingsForm';

export default function Settings({
    organization,
}: {
    organization: Organization;
}) {
    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {
                    title: 'Organization',
                },
                {
                    title: 'Settings',
                },
            ]}
        >
            <Head title="Organization Settings" />

            <h1 className="mx-4 mb-4 text-2xl">
                Organization {organization.name} settings
            </h1>

            <div className="mx-4 mb-4">
                <SettingsForm organization={organization} />
            </div>
        </AuthenticatedLayout>
    );
}
