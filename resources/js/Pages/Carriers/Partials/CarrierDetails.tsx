import ContactList from '@/Components/Contacts/ContactList/ContactList';
import DocumentsList from '@/Components/Documents/DocumentsList';
import ShipmentList from '@/Components/Shipments/ShipmentList/ShipmentList';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { ComingSoon } from '@/Components/ui/coming-soon';
import { Input } from '@/Components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import { Switch } from '@/Components/ui/switch';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs';
import { useMediaQuery } from '@/hooks/useMediaQuery';
import { cn } from '@/lib/utils';
import { Carrier, CarrierBounce } from '@/types';
import { Contactable, Documentable } from '@/types/enums';
import { Link, router } from '@inertiajs/react';
import {
    Check,
    CheckCircle2,
    ExternalLink,
    Pencil,
    X,
    XCircle,
} from 'lucide-react';
import { useEffect, useState } from 'react';
import CarrierDetailsGeneral from './CarrierDetailsGeneral';

export default function CarrierDetails({ carrier }: { carrier: Carrier }) {
    const [isEditing, setIsEditing] = useState(false);
    const [editedName, setEditedName] = useState(carrier?.name || '');

    const [showBounces, setShowBounces] = useState(false);

    // Get initial tab from URL or default to 'overview'
    const [tab, setTab] = useState(() => {
        const params = new URLSearchParams(window.location.search);
        return params.get('tab') || 'overview';
    });

    // Update URL when tab changes
    const handleTabChange = (value: string) => {
        setTab(value);
        router.push({
            url: route('carriers.show', {
                carrier: carrier?.id,
                tab: value,
            }),
        });
    };

    const isMobile = useMediaQuery('(max-width: 768px)');

    const handleSave = () => {
        router.put(
            route('carriers.update', carrier?.id),
            {
                name: editedName,
            },
            {
                preserveScroll: true,
                onSuccess: () => setIsEditing(false),
            },
        );
    };

    const handleCancel = () => {
        setEditedName(carrier?.name || '');
        setIsEditing(false);
    };

    return (
        <div className="flex flex-col gap-6">
            {/* Header Section */}
            <div className="flex items-center justify-between">
                <div className="flex items-center gap-2">
                    {isEditing ? (
                        <>
                            <Input
                                value={editedName}
                                onChange={(e) => setEditedName(e.target.value)}
                                className="h-auto py-0 font-bold md:text-2xl"
                                autoFocus
                            />
                            <button
                                onClick={handleSave}
                                className="p-1 text-success hover:text-success/75"
                            >
                                <Check className="h-5 w-5" />
                            </button>
                            <button
                                onClick={handleCancel}
                                className="p-1 text-destructive hover:text-destructive/75"
                            >
                                <X className="h-5 w-5" />
                            </button>
                        </>
                    ) : (
                        <>
                            <h1 className="text-2xl font-bold">
                                {carrier?.name}
                            </h1>
                            <button
                                onClick={() => setIsEditing(true)}
                                className="p-1 text-gray-400 hover:text-gray-600"
                            >
                                <Pencil className="h-4 w-4" />
                            </button>
                        </>
                    )}
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
                            <SelectItem value="accounting">
                                Accounting
                            </SelectItem>
                            <SelectItem value="contacts">Contacts</SelectItem>
                            <SelectItem value="documents">Documents</SelectItem>
                            <SelectItem value="shipments">
                                Shipment History
                            </SelectItem>
                            <SelectItem value="safer">SAFER</SelectItem>
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
                            value="accounting"
                            onClick={() => handleTabChange('accounting')}
                        >
                            Accounting
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
                        <TabsTrigger
                            value="safer"
                            onClick={() => handleTabChange('safer')}
                        >
                            SAFER
                        </TabsTrigger>
                    </TabsList>
                )}

                {/* Overview Tab */}
                <TabsContent value="overview">
                    <CarrierDetailsGeneral carrier={carrier} />
                </TabsContent>

                {/* Accounting Tab */}
                <TabsContent value="accounting">
                    <Card>
                        <CardHeader>
                            <CardTitle>Billing Information</CardTitle>
                        </CardHeader>
                        <CardContent>
                            {carrier?.billing_location ? (
                                <div className="space-y-4">
                                    <div>
                                        <div className="text-sm text-gray-500">
                                            Billing Email
                                        </div>
                                        <div>{carrier.billing_email}</div>
                                    </div>
                                    <div>
                                        <div className="text-sm text-gray-500">
                                            Billing Phone
                                        </div>
                                        <div>{carrier.billing_phone}</div>
                                    </div>
                                    <div>
                                        <div className="text-sm text-gray-500">
                                            Billing Address
                                        </div>
                                        <div className="space-y-2">
                                            <div>
                                                {
                                                    carrier.billing_location
                                                        .address_line_1
                                                }
                                            </div>
                                            {carrier.billing_location
                                                .address_line_2 && (
                                                <div>
                                                    {
                                                        carrier.billing_location
                                                            .address_line_2
                                                    }
                                                </div>
                                            )}
                                            <div>
                                                {
                                                    carrier.billing_location
                                                        .address_city
                                                }
                                                ,{' '}
                                                {
                                                    carrier.billing_location
                                                        .address_state
                                                }{' '}
                                                {
                                                    carrier.billing_location
                                                        .address_zipcode
                                                }
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ) : (
                                <ComingSoon
                                    variant="outline"
                                    className="mx-auto"
                                />
                            )}
                        </CardContent>
                    </Card>
                </TabsContent>

                {/* Contacts Tab */}
                <TabsContent value="contacts">
                    <Card>
                        <CardContent className="pt-6">
                            <ContactList
                                contactForId={carrier?.id || 0}
                                contactForType={Contactable.Carrier}
                            />
                        </CardContent>
                    </Card>
                </TabsContent>

                {/* Documents Tab */}
                <TabsContent value="documents">
                    <Card>
                        <CardContent className="pt-6">
                            <DocumentsList
                                documents={carrier.documents ?? []}
                                folders={carrier.document_folders ?? []}
                                documentableType={Documentable.Carrier}
                                documentableId={carrier.id}
                            />
                        </CardContent>
                    </Card>
                </TabsContent>

                {/* SAFER Tab */}
                <TabsContent value="safer">
                    <div className="grid gap-6">
                        {/* Company Information */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Company Information</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <div className="text-sm text-muted-foreground">
                                            Legal Name
                                        </div>
                                        <div>
                                            {carrier?.safer_report?.report
                                                ?.general?.carrier?.legalName ||
                                                '-'}
                                        </div>
                                    </div>
                                    <div>
                                        <div className="text-sm text-muted-foreground">
                                            DBA Name
                                        </div>
                                        <div>
                                            {carrier?.safer_report?.report
                                                ?.general?.carrier?.dbaName ||
                                                '-'}
                                        </div>
                                    </div>
                                    <div>
                                        <div className="text-sm text-muted-foreground">
                                            DOT Number
                                        </div>
                                        <div>
                                            {carrier?.safer_report?.report
                                                ?.general?.carrier?.dotNumber ||
                                                '-'}
                                        </div>
                                    </div>
                                    <div>
                                        <div className="text-sm text-muted-foreground">
                                            Status
                                        </div>
                                        <div className="flex items-center gap-2">
                                            {carrier?.safer_report?.report
                                                ?.general?.carrier
                                                ?.statusCode === 'A' ? (
                                                <>
                                                    <CheckCircle2 className="h-4 w-4 text-success" />
                                                    <span>Active</span>
                                                </>
                                            ) : (
                                                <>
                                                    <XCircle className="h-4 w-4 text-destructive" />
                                                    <span>Inactive</span>
                                                </>
                                            )}
                                        </div>
                                    </div>
                                    <div>
                                        <div className="text-sm text-muted-foreground">
                                            Report Date
                                        </div>
                                        <div>
                                            {carrier?.safer_report?.created_at
                                                ? new Date(
                                                      carrier.safer_report.created_at,
                                                  ).toLocaleDateString(
                                                      'en-US',
                                                      {
                                                          year: 'numeric',
                                                          month: 'long',
                                                          day: 'numeric',
                                                      },
                                                  )
                                                : '-'}
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Safety Information */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Safety Information</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <div className="text-sm text-muted-foreground">
                                            Safety Rating
                                        </div>
                                        <div>
                                            {carrier?.safer_report?.report
                                                ?.general?.carrier
                                                ?.safetyRating || 'Not Rated'}
                                        </div>
                                    </div>
                                    <div>
                                        <div className="text-sm text-muted-foreground">
                                            Total Power Units
                                        </div>
                                        <div>
                                            {carrier?.safer_report?.report
                                                ?.general?.carrier
                                                ?.totalPowerUnits || '0'}
                                        </div>
                                    </div>
                                    <div>
                                        <div className="text-sm text-muted-foreground">
                                            Total Drivers
                                        </div>
                                        <div>
                                            {carrier?.safer_report?.report
                                                ?.general?.carrier
                                                ?.totalDrivers || '0'}
                                        </div>
                                    </div>
                                    <div>
                                        <div className="text-sm text-muted-foreground">
                                            MCS-150 Form Date
                                        </div>
                                        <div>
                                            {carrier?.safer_report?.report
                                                ?.general?.carrier
                                                ?.mcs150Outdated === 'Y'
                                                ? 'Outdated'
                                                : 'Current'}
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Crash Statistics */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Crash Statistics</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="grid gap-4 md:grid-cols-3">
                                    <div>
                                        <div className="text-sm text-muted-foreground">
                                            Total Crashes
                                        </div>
                                        <div>
                                            {carrier?.safer_report?.report
                                                ?.general?.carrier
                                                ?.crashTotal || '0'}
                                        </div>
                                    </div>
                                    <div>
                                        <div className="text-sm text-muted-foreground">
                                            Fatal Crashes
                                        </div>
                                        <div>
                                            {carrier?.safer_report?.report
                                                ?.general?.carrier
                                                ?.fatalCrash || '0'}
                                        </div>
                                    </div>
                                    <div>
                                        <div className="text-sm text-muted-foreground">
                                            Injury Crashes
                                        </div>
                                        <div>
                                            {carrier?.safer_report?.report
                                                ?.general?.carrier?.injCrash ||
                                                '0'}
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Inspection Summary */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Inspection Summary</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <div className="text-sm text-muted-foreground">
                                            Driver Inspections
                                        </div>
                                        <div className="space-y-1">
                                            <div>
                                                Total:{' '}
                                                {carrier?.safer_report?.report
                                                    ?.general?.carrier
                                                    ?.driverInsp || '0'}
                                            </div>
                                            <div>
                                                Out of Service:{' '}
                                                {carrier?.safer_report?.report
                                                    ?.general?.carrier
                                                    ?.driverOosInsp || '0'}
                                            </div>
                                            <div>
                                                Out of Service Rate:{' '}
                                                {carrier?.safer_report?.report
                                                    ?.general?.carrier
                                                    ?.driverOosRate || '0'}
                                                %
                                            </div>
                                            <div className="text-sm text-muted-foreground">
                                                National Average:{' '}
                                                {carrier?.safer_report?.report
                                                    ?.general?.carrier
                                                    ?.driverOosRateNationalAverage ||
                                                    '-'}
                                                %
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div className="text-sm text-muted-foreground">
                                            Vehicle Inspections
                                        </div>
                                        <div className="space-y-1">
                                            <div>
                                                Total:{' '}
                                                {carrier?.safer_report?.report
                                                    ?.general?.carrier
                                                    ?.vehicleInsp || '0'}
                                            </div>
                                            <div>
                                                Out of Service:{' '}
                                                {carrier?.safer_report?.report
                                                    ?.general?.carrier
                                                    ?.vehicleOosInsp || '0'}
                                            </div>
                                            <div>
                                                Out of Service Rate:{' '}
                                                {carrier?.safer_report?.report
                                                    ?.general?.carrier
                                                    ?.vehicleOosRate || '0'}
                                                %
                                            </div>
                                            <div className="text-sm text-muted-foreground">
                                                National Average:{' '}
                                                {carrier?.safer_report?.report
                                                    ?.general?.carrier
                                                    ?.vehicleOosRateNationalAverage ||
                                                    '-'}
                                                %
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </TabsContent>

                {/* Shipment History Tab */}
                <TabsContent value="shipments">
                    <Card>
                        <CardContent className="pt-6">
                            <div className="flex w-full items-center justify-end gap-2">
                                <Switch
                                    checked={showBounces}
                                    onCheckedChange={() =>
                                        setShowBounces(!showBounces)
                                    }
                                />
                                <label
                                    htmlFor="showBounces"
                                    className={cn(
                                        !showBounces && 'text-muted-foreground',
                                        'text-sm',
                                    )}
                                >
                                    Bounced Shipments
                                </label>
                            </div>
                            {showBounces ? (
                                <BouncedShipmentsList carrier={carrier} />
                            ) : (
                                <ShipmentList
                                    requiredFilters={[
                                        {
                                            name: 'carrier_id',
                                            value:
                                                carrier?.id?.toString() || '',
                                        },
                                    ]}
                                />
                            )}
                        </CardContent>
                    </Card>
                </TabsContent>
            </Tabs>
        </div>
    );
}

function BouncedShipmentsList({ carrier }: { carrier?: Carrier }) {
    const [bouncedShipments, setBouncedShipments] = useState<CarrierBounce[]>(
        [],
    );

    useEffect(() => {
        fetch(route('carriers.bounced-loads', carrier?.id)).then((response) => {
            response.json().then((data) => {
                setBouncedShipments(data);
            });
        });
    }, [carrier?.id]);

    return (
        <div className="mt-1 md:mt-4">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Bounce Date</TableHead>
                        <TableHead>Shipment</TableHead>
                        <TableHead>Driver</TableHead>
                        <TableHead>Bounce Cause</TableHead>
                        <TableHead>Reason</TableHead>
                        <TableHead>Bounced By</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {bouncedShipments.map((bounce) => (
                        <TableRow key={bounce.id}>
                            <TableCell>
                                {new Date(bounce.created_at).toLocaleDateString(
                                    'en-US',
                                    {
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric',
                                    },
                                )}
                            </TableCell>
                            <TableCell>
                                <Link
                                    href={route(
                                        'shipments.show',
                                        bounce.shipment?.id,
                                    )}
                                >
                                    {bounce.shipment?.shipment_number}{' '}
                                    <ExternalLink className="inline h-4 w-4" />
                                </Link>
                            </TableCell>
                            <TableCell>{bounce.driver?.name}</TableCell>
                            <TableCell>{bounce.bounce_cause}</TableCell>
                            <TableCell>{bounce.reason}</TableCell>
                            <TableCell>
                                {bounce.bounced_by_user?.name}
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </div>
    );
}
