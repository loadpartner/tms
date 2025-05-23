import ContactList from '@/Components/Contacts/ContactList/ContactList';
import ContactForm from '@/Components/CreateForms/ContactForm';
import LocationForm from '@/Components/CreateForms/LocationForm';
import CustomerFacilities from '@/Components/Customers/CustomerFacilities';
import DocumentsList from '@/Components/Documents/DocumentsList';
import InputError from '@/Components/InputError';
import Notes from '@/Components/Notes';
import { ResourceSearchSelect } from '@/Components/ResourceSearchSelect';
import ShipmentList from '@/Components/Shipments/ShipmentList/ShipmentList';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import { Skeleton } from '@/Components/ui/skeleton';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs';
import { useMediaQuery } from '@/hooks/useMediaQuery';
import { useToast } from '@/hooks/UseToast';
import { Customer } from '@/types';
import { Contactable, Documentable, Notable } from '@/types/enums';
import { router, useForm } from '@inertiajs/react';
import { Check, Pencil, X } from 'lucide-react';
import { useState } from 'react';

export default function CustomerDetails({ customer }: { customer: Customer }) {
    const [isEditing, setIsEditing] = useState(false);
    const { toast } = useToast();

    // Get initial tab from URL or default to 'overview'
    const [tab, setTab] = useState(() => {
        const params = new URLSearchParams(window.location.search);
        return params.get('tab') || 'overview';
    });

    // Combined form for all customer information
    const form = useForm({
        name: customer?.name || '',
        dba_name: customer?.dba_name || '',
        net_pay_days: customer?.net_pay_days || '',
        billing_location_id: customer?.billing_location?.id || null,
        invoice_number_schema: customer?.invoice_number_schema || '',
        billing_contact_id: customer?.billing_contact?.id || null,
    });

    // Update URL when tab changes
    const handleTabChange = (value: string) => {
        setTab(value);
        router.push({
            url: route('customers.show', {
                customer: customer?.id,
                tab: value,
            }),
        });
    };

    const isMobile = useMediaQuery('(max-width: 768px)');

    const handleSave = () => {
        form.put(route('customers.update', { customer: customer?.id }), {
            onSuccess: () => {
                setIsEditing(false);
                toast({
                    description: 'Customer information updated successfully',
                });
            },
            onError: () => {
                toast({
                    description: 'Failed to update customer information',
                    variant: 'destructive',
                });
            },
        });
    };

    const handleCancel = () => {
        form.setData({
            name: customer?.name || '',
            dba_name: customer?.dba_name || '',
            net_pay_days: customer?.net_pay_days || '',
            billing_location_id: customer?.billing_location?.id || null,
            invoice_number_schema: customer?.invoice_number_schema || '',
            billing_contact_id: customer?.billing_contact?.id || null,
        });
        setIsEditing(false);
    };

    return (
        <div className="flex flex-col gap-6">
            {/* Header Section */}
            <div className="flex items-center justify-between">
                <div className="flex items-center gap-2">
                    <h1 className="text-2xl font-bold">{customer?.name}</h1>
                </div>
                <div className="flex gap-2">
                    {/* Space for future buttons */}
                </div>
            </div>

            {/* Main Content */}
            <Tabs defaultValue={tab} value={tab} className="w-full">
                {isMobile ? (
                    <Select value={tab} onValueChange={handleTabChange}>
                        <SelectTrigger className="w-full">
                            <SelectValue placeholder="Select view" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="overview">Overview</SelectItem>
                            <SelectItem value="facilities">
                                Facilities
                            </SelectItem>
                            <SelectItem value="contacts">Contacts</SelectItem>
                            <SelectItem value="documents">Documents</SelectItem>
                            <SelectItem value="shipments">
                                Shipment History
                            </SelectItem>
                        </SelectContent>
                    </Select>
                ) : (
                    <TabsList>
                        <TabsTrigger
                            value="overview"
                            onClick={() => handleTabChange('overview')}
                        >
                            Overview
                        </TabsTrigger>
                        <TabsTrigger
                            value="facilities"
                            onClick={() => handleTabChange('facilities')}
                        >
                            Facilities
                        </TabsTrigger>
                        <TabsTrigger
                            value="contacts"
                            onClick={() => handleTabChange('contacts')}
                        >
                            Contacts
                        </TabsTrigger>
                        <TabsTrigger
                            value="documents"
                            onClick={() => handleTabChange('documents')}
                        >
                            Documents
                        </TabsTrigger>
                        <TabsTrigger
                            value="shipments"
                            onClick={() => handleTabChange('shipments')}
                        >
                            Shipment History
                        </TabsTrigger>
                    </TabsList>
                )}

                {/* Overview Tab */}
                <TabsContent value="overview">
                    <div className="space-y-6">
                        {/* Customer Information */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center justify-between">
                                    Customer Information
                                    {!isEditing ? (
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            onClick={() => setIsEditing(true)}
                                        >
                                            <Pencil className="h-4 w-4" />
                                        </Button>
                                    ) : (
                                        <div className="flex gap-2">
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                onClick={handleSave}
                                                disabled={form.processing}
                                            >
                                                <Check className="h-4 w-4" />
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                onClick={handleCancel}
                                                disabled={form.processing}
                                            >
                                                <X className="h-4 w-4" />
                                            </Button>
                                        </div>
                                    )}
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    {/* Account Information Section */}
                                    <div className="space-y-4">
                                        <div>
                                            <Label
                                                htmlFor="customer_name"
                                                className="text-sm text-muted-foreground"
                                            >
                                                Name
                                            </Label>
                                            {isEditing ? (
                                                <div className="space-y-2">
                                                    <Input
                                                        id="customer_name"
                                                        value={form.data.name}
                                                        onChange={(e) =>
                                                            form.setData(
                                                                'name',
                                                                e.target.value,
                                                            )
                                                        }
                                                        disabled={
                                                            form.processing
                                                        }
                                                    />
                                                    {form.errors.name && (
                                                        <InputError
                                                            message={
                                                                form.errors.name
                                                            }
                                                        />
                                                    )}
                                                </div>
                                            ) : (
                                                <div className="mt-1">
                                                    {customer?.name || (
                                                        <span>-</span>
                                                    )}
                                                </div>
                                            )}
                                        </div>
                                        <div>
                                            <Label
                                                htmlFor="dba_name"
                                                className="text-sm text-muted-foreground"
                                            >
                                                DBA Name
                                            </Label>
                                            {isEditing ? (
                                                <div className="space-y-2">
                                                    <Input
                                                        id="dba_name"
                                                        value={
                                                            form.data.dba_name
                                                        }
                                                        onChange={(e) =>
                                                            form.setData(
                                                                'dba_name',
                                                                e.target.value,
                                                            )
                                                        }
                                                        disabled={
                                                            form.processing
                                                        }
                                                    />
                                                    {form.errors.dba_name && (
                                                        <InputError
                                                            message={
                                                                form.errors
                                                                    .dba_name
                                                            }
                                                        />
                                                    )}
                                                </div>
                                            ) : (
                                                <div className="mt-1">
                                                    {customer?.dba_name || (
                                                        <span>-</span>
                                                    )}
                                                </div>
                                            )}
                                        </div>
                                    </div>

                                    {/* Billing Information Section */}
                                    <div className="space-y-4">
                                        <div>
                                            <Label
                                                htmlFor="billing_location"
                                                className="text-sm text-muted-foreground"
                                            >
                                                Billing Address
                                            </Label>
                                            {isEditing ? (
                                                <div className="space-y-2">
                                                    <ResourceSearchSelect
                                                        className="w-full"
                                                        searchRoute={route(
                                                            'locations.search',
                                                        )}
                                                        onValueChange={(
                                                            value,
                                                        ) =>
                                                            form.setData(
                                                                'billing_location_id',
                                                                value
                                                                    ? Number(
                                                                          value,
                                                                      )
                                                                    : null,
                                                            )
                                                        }
                                                        allowMultiple={false}
                                                        defaultSelectedItems={form.data.billing_location_id?.toString()}
                                                        createForm={
                                                            LocationForm
                                                        }
                                                    />
                                                    {form.errors
                                                        .billing_location_id && (
                                                        <InputError
                                                            message={
                                                                form.errors
                                                                    .billing_location_id
                                                            }
                                                        />
                                                    )}
                                                </div>
                                            ) : customer?.billing_location ? (
                                                <div className="mt-1 space-y-1">
                                                    <div>
                                                        {
                                                            customer
                                                                .billing_location
                                                                .address_line_1
                                                        }
                                                    </div>
                                                    {customer.billing_location
                                                        .address_line_2 && (
                                                        <div>
                                                            {
                                                                customer
                                                                    .billing_location
                                                                    .address_line_2
                                                            }
                                                        </div>
                                                    )}
                                                    <div>
                                                        {
                                                            customer
                                                                .billing_location
                                                                .address_city
                                                        }
                                                        ,{' '}
                                                        {
                                                            customer
                                                                .billing_location
                                                                .address_state
                                                        }{' '}
                                                        {
                                                            customer
                                                                .billing_location
                                                                .address_zipcode
                                                        }
                                                    </div>
                                                </div>
                                            ) : (
                                                <div className="mt-1">
                                                    <span>-</span>
                                                </div>
                                            )}
                                        </div>
                                        <div>
                                            <Label
                                                htmlFor="billing_contact"
                                                className="text-sm text-muted-foreground"
                                            >
                                                Billing Contact
                                            </Label>
                                            {isEditing ? (
                                                <div className="space-y-2">
                                                    <ResourceSearchSelect
                                                        className="w-full"
                                                        searchRoute={route(
                                                            'contacts.search',
                                                        )}
                                                        onValueChange={(
                                                            value,
                                                        ) =>
                                                            form.setData(
                                                                'billing_contact_id',
                                                                value
                                                                    ? Number(
                                                                          value,
                                                                      )
                                                                    : null,
                                                            )
                                                        }
                                                        allowMultiple={false}
                                                        defaultSelectedItems={form.data.billing_contact_id?.toString()}
                                                        createForm={(props) => (
                                                            <ContactForm
                                                                {...props}
                                                                contactForId={
                                                                    customer?.id ||
                                                                    0
                                                                }
                                                                contactForType={
                                                                    Contactable.Customer
                                                                }
                                                            />
                                                        )}
                                                    />
                                                    {form.errors
                                                        .billing_contact_id && (
                                                        <InputError
                                                            message={
                                                                form.errors
                                                                    .billing_contact_id
                                                            }
                                                        />
                                                    )}
                                                </div>
                                            ) : customer?.billing_contact ? (
                                                <div className="mt-1">
                                                    {
                                                        customer.billing_contact
                                                            .name
                                                    }
                                                    {customer.billing_contact
                                                        .email && (
                                                        <div className="text-sm text-muted-foreground">
                                                            {
                                                                customer
                                                                    .billing_contact
                                                                    .email
                                                            }
                                                        </div>
                                                    )}
                                                </div>
                                            ) : (
                                                <div className="mt-1">
                                                    <span>-</span>
                                                </div>
                                            )}
                                        </div>
                                        <div>
                                            <Label
                                                htmlFor="net_pay_days"
                                                className="text-sm text-muted-foreground"
                                            >
                                                Net Pay Terms
                                            </Label>
                                            {isEditing ? (
                                                <div className="space-y-2">
                                                    <Input
                                                        id="net_pay_days"
                                                        type="number"
                                                        min="0"
                                                        max="365"
                                                        value={
                                                            form.data
                                                                .net_pay_days
                                                        }
                                                        onChange={(e) =>
                                                            form.setData(
                                                                'net_pay_days',
                                                                e.target.value,
                                                            )
                                                        }
                                                        disabled={
                                                            form.processing
                                                        }
                                                    />
                                                    {form.errors
                                                        .net_pay_days && (
                                                        <InputError
                                                            message={
                                                                form.errors
                                                                    .net_pay_days
                                                            }
                                                        />
                                                    )}
                                                </div>
                                            ) : (
                                                <div className="mt-1">
                                                    {customer?.net_pay_days !==
                                                        null &&
                                                    customer?.net_pay_days !==
                                                        undefined ? (
                                                        `${customer.net_pay_days} days`
                                                    ) : (
                                                        <span>-</span>
                                                    )}
                                                </div>
                                            )}
                                        </div>
                                        <div>
                                            <Label
                                                htmlFor="invoice_number_schema"
                                                className="text-sm text-muted-foreground"
                                            >
                                                Invoice Number Schema
                                            </Label>
                                            {isEditing ? (
                                                <div className="space-y-2">
                                                    <Input
                                                        id="invoice_number_schema"
                                                        value={
                                                            form.data
                                                                .invoice_number_schema
                                                        }
                                                        onChange={(e) =>
                                                            form.setData(
                                                                'invoice_number_schema',
                                                                e.target.value,
                                                            )
                                                        }
                                                        disabled={
                                                            form.processing
                                                        }
                                                    />
                                                    {form.errors
                                                        .invoice_number_schema && (
                                                        <InputError
                                                            message={
                                                                form.errors
                                                                    .invoice_number_schema
                                                            }
                                                        />
                                                    )}
                                                </div>
                                            ) : (
                                                <div className="mt-1">
                                                    {customer?.invoice_number_schema || (
                                                        <span>-</span>
                                                    )}
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Notes Section */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Notes</CardTitle>
                            </CardHeader>
                            <CardContent>
                                {customer?.id ? (
                                    <Notes
                                        notableType={Notable.Customer}
                                        notableId={customer.id}
                                    />
                                ) : (
                                    <Skeleton className="h-32 w-full" />
                                )}
                            </CardContent>
                        </Card>
                    </div>
                </TabsContent>

                {/* Facilities Tab */}
                <TabsContent value="facilities">
                    <CustomerFacilities customer={customer} />
                </TabsContent>

                {/* Contacts Tab */}
                <TabsContent value="contacts">
                    <Card>
                        <CardContent className="pt-6">
                            <ContactList
                                contactForId={customer?.id || 0}
                                contactForType={Contactable.Customer}
                            />
                        </CardContent>
                    </Card>
                </TabsContent>

                {/* Documents Tab */}
                <TabsContent value="documents">
                    <Card>
                        <CardContent className="pt-6">
                            <DocumentsList
                                documents={customer.documents ?? []}
                                folders={customer.document_folders ?? []}
                                documentableType={Documentable.Customer}
                                documentableId={customer.id}
                            />
                        </CardContent>
                    </Card>
                </TabsContent>

                {/* Shipment History Tab */}
                <TabsContent value="shipments">
                    <Card>
                        <CardContent className="pt-6">
                            <ShipmentList
                                requiredFilters={[
                                    {
                                        name: 'customers.id',
                                        value: customer?.id?.toString() || '',
                                    },
                                ]}
                            />
                        </CardContent>
                    </Card>
                </TabsContent>
            </Tabs>
        </div>
    );
}
