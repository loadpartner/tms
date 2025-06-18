import ContactList from '@/Components/Contacts/ContactList/ContactList';
import LocationForm from '@/Components/CreateForms/LocationForm';
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
import { Facility } from '@/types';
import { Contactable, Documentable, Notable } from '@/types/enums';
import { router, useForm } from '@inertiajs/react';
import { Check, Pencil, X } from 'lucide-react';
import { useState } from 'react';

export default function FacilityDetails({ facility }: { facility: Facility }) {
    const [isEditing, setIsEditing] = useState(false);
    const [isLocationEditing, setIsLocationEditing] = useState(false);
    const [editedName, setEditedName] = useState(facility?.name || '');
    const { toast } = useToast();

    // Get initial tab from URL or default to 'overview'
    const [tab, setTab] = useState(() => {
        const params = new URLSearchParams(window.location.search);
        return params.get('tab') || 'overview';
    });

    // Update URL when tab changes
    const handleTabChange = (value: string) => {
        setTab(value);
        router.push({
            url: route('facilities.show', {
                facility: facility?.id,
                tab: value,
            }),
        });
    };

    const isMobile = useMediaQuery('(max-width: 768px)');

    const handleSave = () => {
        router.put(
            route('facilities.update', facility?.id),
            {
                name: editedName,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setIsEditing(false);
                    toast({
                        description: 'Facility name updated successfully',
                    });
                },
                onError: () => {
                    toast({
                        description: 'Failed to update facility name',
                    });
                },
            },
        );
    };

    const handleCancel = () => {
        setEditedName(facility?.name || '');
        setIsEditing(false);
    };

    const locationForm = useForm({
        location_id: facility?.location_id || null,
    });

    const handleLocationSave = () => {
        locationForm.put(
            route('facilities.update', { facility: facility?.id }),
            {
                onSuccess: () => {
                    setIsLocationEditing(false);
                    toast({
                        description: 'Location updated successfully',
                    });
                },
                onError: () => {
                    toast({
                        description: 'Failed to update location',
                    });
                },
            },
        );
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
                                {facility?.name}
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
                    <div className="grid grid-cols-2 gap-6">
                        {/* General Information */}
                        <Card>
                            <CardHeader>
                                <CardTitle>General Information</CardTitle>
                            </CardHeader>
                            <CardContent>
                                {facility?.location && (
                                    <div className="space-y-4">
                                        <div>
                                            <Label className="text-sm text-muted-foreground">
                                                Facility Name
                                            </Label>
                                            <div>{facility.name}</div>
                                        </div>
                                    </div>
                                )}
                            </CardContent>
                        </Card>

                        {/* Location */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center justify-between">
                                    Location
                                    {!isLocationEditing ? (
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            onClick={() =>
                                                setIsLocationEditing(true)
                                            }
                                        >
                                            <Pencil className="h-4 w-4" />
                                        </Button>
                                    ) : (
                                        <div className="flex gap-2">
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                onClick={handleLocationSave}
                                                disabled={
                                                    locationForm.processing
                                                }
                                            >
                                                <Check className="h-4 w-4" />
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                onClick={() =>
                                                    setIsLocationEditing(false)
                                                }
                                                disabled={
                                                    locationForm.processing
                                                }
                                            >
                                                <X className="h-4 w-4" />
                                            </Button>
                                        </div>
                                    )}
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                {isLocationEditing ? (
                                    <div className="flex flex-col gap-4">
                                        <Label className="text-sm text-muted-foreground">
                                            Location
                                        </Label>
                                        <div className="space-y-2">
                                            <ResourceSearchSelect
                                                className="w-full"
                                                searchRoute={route(
                                                    'locations.search',
                                                )}
                                                onValueChange={(value) =>
                                                    locationForm.setData(
                                                        'location_id',
                                                        Number(value),
                                                    )
                                                }
                                                allowMultiple={false}
                                                defaultSelectedItems={locationForm.data.location_id?.toString()}
                                                createForm={LocationForm}
                                            />
                                            {locationForm.errors
                                                .location_id && (
                                                <InputError
                                                    message={
                                                        locationForm.errors
                                                            .location_id
                                                    }
                                                />
                                            )}
                                        </div>
                                    </div>
                                ) : facility?.location ? (
                                    <div className="space-y-2">
                                        <div>
                                            {facility.location.address_line_1}
                                        </div>
                                        {facility.location.address_line_2 && (
                                            <div>
                                                {
                                                    facility.location
                                                        .address_line_2
                                                }
                                            </div>
                                        )}
                                        <div>
                                            {facility.location.address_city},{' '}
                                            {facility.location.address_state}{' '}
                                            {facility.location.address_zipcode}
                                        </div>
                                    </div>
                                ) : (
                                    <Skeleton className="h-24 w-full" />
                                )}
                            </CardContent>
                        </Card>

                        {/* Notes Section */}
                        <Card className="col-span-2">
                            <CardHeader>
                                <CardTitle>Notes</CardTitle>
                            </CardHeader>
                            <CardContent>
                                {facility?.id ? (
                                    <Notes
                                        notableType={Notable.Facility}
                                        notableId={facility.id}
                                    />
                                ) : (
                                    <Skeleton className="h-32 w-full" />
                                )}
                            </CardContent>
                        </Card>
                    </div>
                </TabsContent>

                {/* Contacts Tab */}
                <TabsContent value="contacts">
                    <Card>
                        <CardContent className="pt-6">
                            <ContactList
                                contactForId={facility?.id || 0}
                                contactForType={Contactable.Facility}
                            />
                        </CardContent>
                    </Card>
                </TabsContent>

                {/* Documents Tab */}
                <TabsContent value="documents">
                    <Card>
                        <CardContent className="pt-6">
                            <DocumentsList
                                documents={facility.documents ?? []}
                                folders={facility.document_folders ?? []}
                                documentableType={Documentable.Facility}
                                documentableId={facility.id}
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
                                        name: 'stops.facility_id',
                                        value: facility.id.toString() || '',
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
