import LocationForm from '@/Components/CreateForms/LocationForm';
import InputError from '@/Components/InputError';
import { ResourceSearchSelect } from '@/Components/ResourceSearchSelect';
import { Button } from '@/Components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Organization } from '@/types/organization';
import { Link, useForm, usePage } from '@inertiajs/react';

export default function SettingsForm({
    organization,
}: {
    organization: Organization;
}) {
    const config = usePage().props.config;
    const { data, setData, put, processing, errors } = useForm({
        name: organization.name || '',
        company_name: organization.company_name || '',
        company_location_id: organization.company_location?.id || null,
        company_phone: organization.company_phone || '',
        company_email: organization.company_email || '',
        accounting_contact_email: organization.accounting_contact_email || '',
        accounting_contact_phone: organization.accounting_contact_phone || '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('organizations.settings.update', organization.id));
    };

    return (
        <div className="space-y-6">
            <form onSubmit={submit} className="space-y-6">
                {/* General Settings */}
                <Card>
                    <CardHeader>
                        <CardTitle>General Settings</CardTitle>
                        <CardDescription>
                            Basic organization information
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div>
                            <Label htmlFor="name">Organization Name</Label>
                            <Input
                                id="name"
                                type="text"
                                value={data.name}
                                onChange={(e) =>
                                    setData('name', e.target.value)
                                }
                                required
                            />
                            {errors.name && (
                                <InputError message={errors.name} />
                            )}
                        </div>
                    </CardContent>
                </Card>

                {/* Company Information */}
                <Card>
                    <CardHeader>
                        <CardTitle>Company Information</CardTitle>
                        <CardDescription>
                            Company details used on invoices and documents
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div>
                            <Label htmlFor="company_name">Company Name</Label>
                            <Input
                                id="company_name"
                                type="text"
                                value={data.company_name}
                                onChange={(e) =>
                                    setData('company_name', e.target.value)
                                }
                            />
                            {errors.company_name && (
                                <InputError message={errors.company_name} />
                            )}
                        </div>

                        <div>
                            <Label htmlFor="company_location">
                                Company Address
                            </Label>
                            <ResourceSearchSelect
                                className="w-full"
                                searchRoute={route('locations.search')}
                                onValueChange={(value) =>
                                    setData(
                                        'company_location_id',
                                        value ? Number(value) : null,
                                    )
                                }
                                allowMultiple={false}
                                defaultSelectedItems={data.company_location_id?.toString()}
                                createForm={LocationForm}
                            />
                            {errors.company_location_id && (
                                <InputError
                                    message={errors.company_location_id}
                                />
                            )}
                            {organization.company_location && (
                                <div className="mt-2 text-sm text-muted-foreground">
                                    Current:{' '}
                                    {
                                        organization.company_location
                                            .address_line_1
                                    }
                                    {organization.company_location
                                        .address_line_2 && (
                                        <span>
                                            ,{' '}
                                            {
                                                organization.company_location
                                                    .address_line_2
                                            }
                                        </span>
                                    )}
                                    <span>
                                        ,{' '}
                                        {
                                            organization.company_location
                                                .address_city
                                        }
                                        ,{' '}
                                        {
                                            organization.company_location
                                                .address_state
                                        }{' '}
                                        {
                                            organization.company_location
                                                .address_zipcode
                                        }
                                    </span>
                                </div>
                            )}
                        </div>

                        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <Label htmlFor="company_phone">Phone</Label>
                                <Input
                                    id="company_phone"
                                    type="tel"
                                    value={data.company_phone}
                                    onChange={(e) =>
                                        setData('company_phone', e.target.value)
                                    }
                                />
                                {errors.company_phone && (
                                    <InputError
                                        message={errors.company_phone}
                                    />
                                )}
                            </div>

                            <div>
                                <Label htmlFor="company_email">Email</Label>
                                <Input
                                    id="company_email"
                                    type="email"
                                    value={data.company_email}
                                    onChange={(e) =>
                                        setData('company_email', e.target.value)
                                    }
                                />
                                {errors.company_email && (
                                    <InputError
                                        message={errors.company_email}
                                    />
                                )}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Accounting Contact */}
                <Card>
                    <CardHeader>
                        <CardTitle>Accounting Contact</CardTitle>
                        <CardDescription>
                            Dedicated accounting contact information
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <Label htmlFor="accounting_contact_email">
                                    Accounting Email
                                </Label>
                                <Input
                                    id="accounting_contact_email"
                                    type="email"
                                    value={data.accounting_contact_email}
                                    onChange={(e) =>
                                        setData(
                                            'accounting_contact_email',
                                            e.target.value,
                                        )
                                    }
                                />
                                {errors.accounting_contact_email && (
                                    <InputError
                                        message={
                                            errors.accounting_contact_email
                                        }
                                    />
                                )}
                            </div>

                            <div>
                                <Label htmlFor="accounting_contact_phone">
                                    Accounting Phone
                                </Label>
                                <Input
                                    id="accounting_contact_phone"
                                    type="tel"
                                    value={data.accounting_contact_phone}
                                    onChange={(e) =>
                                        setData(
                                            'accounting_contact_phone',
                                            e.target.value,
                                        )
                                    }
                                />
                                {errors.accounting_contact_phone && (
                                    <InputError
                                        message={
                                            errors.accounting_contact_phone
                                        }
                                    />
                                )}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div className="flex justify-end">
                    <Button type="submit" disabled={processing}>
                        {processing ? 'Saving...' : 'Save Settings'}
                    </Button>
                </div>
            </form>

            {/* Billing Section */}
            {config?.enable_billing && (
                <Card>
                    <CardHeader>
                        <CardTitle>Billing & Subscriptions</CardTitle>
                        <CardDescription>
                            Manage your subscription, update seats, and access
                            billing portal
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Link
                            href={route(
                                'organizations.billing',
                                organization.id,
                            )}
                            className="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90"
                        >
                            Manage Billing
                        </Link>
                    </CardContent>
                </Card>
            )}
        </div>
    );
}
