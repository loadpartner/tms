import LocationForm from '@/Components/CreateForms/LocationForm';
import InputError from '@/Components/InputError';
import Notes from '@/Components/Notes';
import { ResourceSearchSelect } from '@/Components/ResourceSearchSelect';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Skeleton } from '@/Components/ui/skeleton';
import { useToast } from '@/hooks/UseToast';
import { Carrier } from '@/types';
import { Notable } from '@/types/enums';
import { useForm } from '@inertiajs/react';
import { Check, ExternalLink, Pencil, X } from 'lucide-react';
import { useState } from 'react';

interface CarrierDetailsGeneralProps {
    carrier?: Carrier;
}

export default function CarrierDetailsGeneral({
    carrier,
}: CarrierDetailsGeneralProps) {
    const [isEditing, setIsEditing] = useState(false);
    const { toast } = useToast();

    const form = useForm({
        mc_number: carrier?.mc_number || '',
        dot_number: carrier?.dot_number || '',
        physical_location_id: carrier?.physical_location_id || null,
        contact_email: carrier?.contact_email || '',
        contact_phone: carrier?.contact_phone || '',
    });

    const handleSave = () => {
        form.put(route('carriers.update', { carrier: carrier?.id }), {
            onSuccess: () => {
                setIsEditing(false);
                toast({
                    description: 'Changes saved successfully',
                });
            },
            onError: () => {
                toast({
                    description: 'Failed to save changes',
                });
            },
        });
    };

    return (
        <div className="grid grid-cols-2 gap-6">
            {/* General Information */}
            <Card className="">
                <CardHeader>
                    <CardTitle className="flex items-center justify-between">
                        General Information
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
                                    onClick={() => setIsEditing(false)}
                                    disabled={form.processing}
                                >
                                    <X className="h-4 w-4" />
                                </Button>
                            </div>
                        )}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div className="grid grid-cols-2 gap-6">
                        <div className="space-y-4">
                            <div>
                                <Label
                                    htmlFor="dot_number"
                                    className="text-sm text-muted-foreground"
                                >
                                    DOT Number
                                </Label>
                                {isEditing ? (
                                    <div className="space-y-2">
                                        <Input
                                            id="dot_number"
                                            value={form.data.dot_number}
                                            onChange={(e) =>
                                                form.setData(
                                                    'dot_number',
                                                    e.target.value,
                                                )
                                            }
                                            disabled={form.processing}
                                        />
                                        {form.errors.dot_number && (
                                            <InputError
                                                message={form.errors.dot_number}
                                            />
                                        )}
                                    </div>
                                ) : (
                                    <div className="mt-1">
                                        {carrier?.dot_number ? (
                                            <a
                                                href={`https://safer.fmcsa.dot.gov/query.asp?searchtype=DOT&query_type=queryCarrierSnapshot&query_param=USDOT&query_string=${carrier.dot_number}`}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="text-primary hover:underline"
                                            >
                                                {carrier.dot_number}{' '}
                                                <ExternalLink className="inline h-4 w-4" />
                                            </a>
                                        ) : (
                                            <span>-</span>
                                        )}
                                    </div>
                                )}
                            </div>
                            <div>
                                <Label
                                    htmlFor="mc_number"
                                    className="text-sm text-muted-foreground"
                                >
                                    MC Number
                                </Label>
                                {isEditing ? (
                                    <div className="space-y-2">
                                        <Input
                                            id="mc_number"
                                            value={form.data.mc_number}
                                            onChange={(e) =>
                                                form.setData(
                                                    'mc_number',
                                                    e.target.value,
                                                )
                                            }
                                            disabled={form.processing}
                                        />
                                        {form.errors.mc_number && (
                                            <InputError
                                                message={form.errors.mc_number}
                                            />
                                        )}
                                    </div>
                                ) : (
                                    <div className="mt-1">
                                        {carrier?.mc_number ? (
                                            carrier?.mc_number
                                        ) : (
                                            <span>-</span>
                                        )}
                                    </div>
                                )}
                            </div>
                        </div>
                        <div className="space-y-4">
                            <div>
                                <Label
                                    htmlFor="contact_email"
                                    className="text-sm text-muted-foreground"
                                >
                                    Contact Email
                                </Label>
                                {isEditing ? (
                                    <div className="space-y-2">
                                        <Input
                                            id="contact_email"
                                            type="email"
                                            value={form.data.contact_email}
                                            onChange={(e) =>
                                                form.setData(
                                                    'contact_email',
                                                    e.target.value,
                                                )
                                            }
                                            disabled={form.processing}
                                        />
                                        {form.errors.contact_email && (
                                            <InputError
                                                message={
                                                    form.errors.contact_email
                                                }
                                            />
                                        )}
                                    </div>
                                ) : (
                                    <div className="mt-1">
                                        {carrier?.contact_email || (
                                            <span>-</span>
                                        )}
                                    </div>
                                )}
                            </div>
                            <div>
                                <Label
                                    htmlFor="contact_phone"
                                    className="text-sm text-muted-foreground"
                                >
                                    Contact Phone
                                </Label>
                                {isEditing ? (
                                    <div className="space-y-2">
                                        <Input
                                            id="contact_phone"
                                            type="tel"
                                            value={form.data.contact_phone}
                                            onChange={(e) =>
                                                form.setData(
                                                    'contact_phone',
                                                    e.target.value,
                                                )
                                            }
                                            disabled={form.processing}
                                        />
                                        {form.errors.contact_phone && (
                                            <InputError
                                                message={
                                                    form.errors.contact_phone
                                                }
                                            />
                                        )}
                                    </div>
                                ) : (
                                    <div className="mt-1">
                                        {carrier?.contact_phone || (
                                            <span>-</span>
                                        )}
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* Physical Address */}
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center justify-between">
                        Primary Address
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
                                    onClick={() => setIsEditing(false)}
                                    disabled={form.processing}
                                >
                                    <X className="h-4 w-4" />
                                </Button>
                            </div>
                        )}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    {isEditing ? (
                        <div className="flex flex-col gap-4">
                            <Label className="text-sm text-muted-foreground">
                                Location
                            </Label>
                            <div className="space-y-2">
                                <ResourceSearchSelect
                                    className="w-full"
                                    searchRoute={route('locations.search')}
                                    onValueChange={(value) =>
                                        form.setData(
                                            'physical_location_id',
                                            Number(value),
                                        )
                                    }
                                    allowMultiple={false}
                                    defaultSelectedItems={form.data.physical_location_id?.toString()}
                                    createForm={LocationForm}
                                />
                                {form.errors.physical_location_id && (
                                    <InputError
                                        message={
                                            form.errors.physical_location_id
                                        }
                                    />
                                )}
                            </div>
                        </div>
                    ) : carrier?.physical_location ? (
                        <div className="space-y-2">
                            <div>
                                {carrier.physical_location.address_line_1}
                            </div>
                            {carrier.physical_location.address_line_2 && (
                                <div>
                                    {carrier.physical_location.address_line_2}
                                </div>
                            )}
                            <div>
                                {carrier.physical_location.address_city},{' '}
                                {carrier.physical_location.address_state}{' '}
                                {carrier.physical_location.address_zipcode}
                            </div>
                        </div>
                    ) : (
                        <span>-</span>
                    )}
                </CardContent>
            </Card>

            {/* Notes Section */}
            <Card className="col-span-2">
                <CardHeader>
                    <CardTitle>Notes</CardTitle>
                </CardHeader>
                <CardContent>
                    {carrier?.id ? (
                        <Notes
                            notableType={Notable.Carrier}
                            notableId={carrier.id}
                        />
                    ) : (
                        <Skeleton className="h-32 w-full" />
                    )}
                </CardContent>
            </Card>
        </div>
    );
}
