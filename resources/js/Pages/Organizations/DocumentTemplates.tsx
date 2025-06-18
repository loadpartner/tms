import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Organization } from '@/types/organization';
import { Head } from '@inertiajs/react';
import DocumentTemplatesForm from './Partials/DocumentTemplatesForm';

export default function DocumentTemplates({
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
                    title: 'Document Templates',
                },
            ]}
        >
            <Head title="Document Templates" />

            <h1 className="mx-4 mb-4 text-2xl">
                Document Templates for {organization.name}
            </h1>

            <div className="mx-4 mb-4">
                <DocumentTemplatesForm organization={organization} />
            </div>
        </AuthenticatedLayout>
    );
}
