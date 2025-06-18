import { DateTimePicker } from '@/Components/DatetimePicker';
import { Button } from '@/Components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/Components/ui/dialog';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import { useEventBus } from '@/hooks/useEventBus';
import { useToast } from '@/hooks/UseToast';
import { convertDateForTimezone } from '@/lib/timezone';
import { Contact, Shipment, ShipmentStop } from '@/types';
import { ContactMethodType, Notable, ShipmentState } from '@/types/enums';
import { useForm } from '@inertiajs/react';
import { ClipboardCheck } from 'lucide-react';
import { useCallback, useEffect, useState } from 'react';
import { Textarea } from '../ui/textarea';

type NewCheckCallButtonProps = {
    shipment: Shipment;
    carrierContacts?: Contact[];
    carrierId?: number;
    buttonText?: string;
    buttonVariant?:
        | 'default'
        | 'destructive'
        | 'outline'
        | 'secondary'
        | 'ghost'
        | 'link';
    buttonIcon?: React.ReactNode;
};

// Function to determine if a field should be shown based on shipment state and context
const shouldShowField = (
    shipment: Shipment,
    stop: ShipmentStop | undefined,
    fieldName: string,
): boolean => {
    const state = shipment.state as ShipmentState;

    switch (fieldName) {
        case 'stop_id':
            return true;

        case 'eta':
            return ![
                ShipmentState.AtDelivery,
                ShipmentState.Delivered,
                ShipmentState.Canceled,
            ].includes(state);

        case 'reported_trailer_temp':
            // Show for temperature-sensitive shipments in active states
            return shipment.trailer_temperature_range;

        case 'contact_name':
        case 'contact_method':
        case 'contact_method_detail':
        case 'note':
            // Always show contact info and notes
            return true;

        case 'is_late':
            // Show is_late until the shipment is delivered or canceled
            return ![ShipmentState.Delivered, ShipmentState.Canceled].includes(
                state,
            );

        case 'is_truck_empty':
            // Show is_truck_empty only in these specific states
            return [
                ShipmentState.Pending,
                ShipmentState.Dispatched,
                ShipmentState.Booked,
                ShipmentState.Delivered,
            ].includes(state);

        case 'arrived_at':
            // Show arrived_at for in-transit states
            return [ShipmentState.InTransit, ShipmentState.Dispatched].includes(
                state,
            );

        case 'left_at':
            return [ShipmentState.AtPickup, ShipmentState.AtDelivery].includes(
                state,
            );
        case 'loaded_unloaded_at':
            // Show these fields for active stops where loading/unloading happens
            return [ShipmentState.AtPickup, ShipmentState.AtDelivery].includes(
                state,
            );

        default:
            return false;
    }
};

export default function NewCheckCallButton({
    shipment,
    carrierContacts = [],
    carrierId,
    buttonText = 'New Check Call',
    buttonVariant = 'default',
    buttonIcon = <ClipboardCheck className="mr-2 h-4 w-4" />,
}: NewCheckCallButtonProps) {
    const shipmentId = shipment.id;

    const [open, setOpen] = useState(false);
    const { toast } = useToast();
    const { emit, emitNoteChanged } = useEventBus();
    const [isLoadedUnloadedRequired, setIsLoadedUnloadedRequired] =
        useState(false);

    // Helper function to convert date for timezone display
    const convertForTimezone = useCallback(
        (stop: ShipmentStop | undefined, date: string) => {
            const timezone = stop?.facility?.location?.timezone?.identifier;
            return convertDateForTimezone(date, timezone);
        },
        [],
    );

    const { data, setData, post, processing, errors, reset } = useForm<{
        eta: string | null;
        reported_trailer_temp: string | null;
        contact_name: string | null;
        contact_method: string | null;
        contact_method_detail: string | null;
        is_late: boolean | null;
        is_truck_empty: boolean | null;
        note: string | null;
        arrived_at: string | null;
        left_at: string | null;
        loaded_unloaded_at: string | null;
    }>({
        eta: null,
        reported_trailer_temp: '',
        contact_name: '',
        contact_method: '',
        contact_method_detail: '',
        is_late: null,
        is_truck_empty: null,
        note: null,
        arrived_at: null,
        left_at: null,
        loaded_unloaded_at: null,
    });

    // Check if loaded_unloaded_at should be required
    useEffect(() => {
        // If left_at has a value and the selected stop doesn't have loaded_unloaded, make loaded_unloaded_at required
        const hasLeftAtValue = !!data.left_at;
        const stopMissingLoadedUnloaded =
            !shipment.current_stop?.loaded_unloaded_at;

        setIsLoadedUnloadedRequired(
            hasLeftAtValue && stopMissingLoadedUnloaded,
        );
    }, [data.left_at, shipment.current_stop]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        // Additional validation
        if (isLoadedUnloadedRequired && !data.loaded_unloaded_at) {
            toast({
                title: 'Validation Error',
                description:
                    'Loaded/Unloaded time is required when setting left at time',
                variant: 'destructive',
            });
            return;
        }

        post(route('shipments.check-calls.store', { shipment: shipmentId }), {
            onSuccess: () => {
                emit('check-call-added-' + shipmentId);
                if (data.note) {
                    emitNoteChanged(Notable.Shipment, shipmentId);
                }

                toast({
                    description: 'Check call created successfully',
                });
                reset();
                setOpen(false);
            },
            onError: () => {
                toast({
                    title: 'Error',
                    description: 'Failed to create check call',
                    variant: 'destructive',
                });
            },
        });
    };

    if (!carrierId) {
        return <></>;
    }

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button variant={buttonVariant}>
                    {buttonIcon}
                    {buttonText}
                </Button>
            </DialogTrigger>
            <DialogContent className="max-h-[90vh] overflow-y-auto sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>New Check Call</DialogTitle>
                    <DialogDescription>
                        Record a check call with the carrier
                    </DialogDescription>
                </DialogHeader>
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div className="space-y-2">
                        <div className="flex items-center justify-between">
                            <Label htmlFor="contact_name">Contact Name *</Label>
                        </div>
                        <Input
                            id="contact_name"
                            value={data.contact_name || ''}
                            onChange={(e) =>
                                setData('contact_name', e.target.value)
                            }
                            autoComplete="off"
                            placeholder="Who you spoke with"
                            list="carrier-contacts-list"
                            required={true}
                        />
                        <datalist id="carrier-contacts-list">
                            {carrierContacts.map((contact) => (
                                <option key={contact.id} value={contact.name} />
                            ))}
                        </datalist>
                        {errors.contact_name && (
                            <p className="text-sm text-red-500">
                                {errors.contact_name}
                            </p>
                        )}
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                        <div className="space-y-2">
                            <Label htmlFor="contact_method">
                                Contact Method
                            </Label>
                            <Select
                                value={data.contact_method || ''}
                                onValueChange={(value) =>
                                    setData('contact_method', value)
                                }
                            >
                                <SelectTrigger>
                                    <SelectValue placeholder="Select method" />
                                </SelectTrigger>
                                <SelectContent>
                                    {Object.values(ContactMethodType).map(
                                        (method) => (
                                            <SelectItem
                                                key={method}
                                                value={method}
                                            >
                                                {method
                                                    .charAt(0)
                                                    .toUpperCase() +
                                                    method.slice(1)}
                                            </SelectItem>
                                        ),
                                    )}
                                </SelectContent>
                            </Select>
                            {errors.contact_method && (
                                <p className="text-sm text-red-500">
                                    {errors.contact_method}
                                </p>
                            )}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="contact_method_detail">
                                Contact Detail
                            </Label>
                            <Input
                                id="contact_method_detail"
                                type={
                                    data.contact_method === 'email'
                                        ? 'email'
                                        : data.contact_method === 'phone'
                                          ? 'tel'
                                          : 'text'
                                }
                                value={data.contact_method_detail || ''}
                                onChange={(e) =>
                                    setData(
                                        'contact_method_detail',
                                        e.target.value,
                                    )
                                }
                                placeholder={
                                    data.contact_method === 'email'
                                        ? 'Email address'
                                        : data.contact_method === 'phone'
                                          ? 'Phone number'
                                          : 'Phone number, email, etc.'
                                }
                            />
                            {errors.contact_method_detail && (
                                <p className="text-sm text-red-500">
                                    {errors.contact_method_detail}
                                </p>
                            )}
                        </div>
                    </div>

                    {shouldShowField(shipment, shipment.next_stop, 'eta') && (
                        <div className="space-y-2">
                            <Label htmlFor="eta" className="flex items-center">
                                <span className="font-bold">ETA</span>
                                <span className="ml-2 text-xs font-normal text-muted-foreground">
                                    ({shipment.next_stop?.facility?.name})
                                </span>
                            </Label>
                            <DateTimePicker
                                clearable={true}
                                value={
                                    data.eta
                                        ? new Date(
                                              convertForTimezone(
                                                  shipment.next_stop,
                                                  data.eta,
                                              ),
                                          )
                                        : undefined
                                }
                                timezone={
                                    shipment.next_stop?.facility?.location
                                        ?.timezone?.identifier
                                }
                                onChange={(e: Date | undefined) => {
                                    setData('eta', e?.toISOString() || null);
                                }}
                            />
                            {errors.eta && (
                                <p className="text-sm text-red-500">
                                    {errors.eta}
                                </p>
                            )}
                        </div>
                    )}

                    {shouldShowField(
                        shipment,
                        shipment.current_stop,
                        'reported_trailer_temp',
                    ) && (
                        <div className="space-y-2">
                            <Label htmlFor="reported_trailer_temp">
                                Reported Trailer Temp
                            </Label>
                            <Input
                                id="reported_trailer_temp"
                                type="number"
                                step="0.1"
                                value={data.reported_trailer_temp || ''}
                                onChange={(e) =>
                                    setData(
                                        'reported_trailer_temp',
                                        e.target.value,
                                    )
                                }
                                placeholder="Enter temperature"
                            />
                            {errors.reported_trailer_temp && (
                                <p className="text-sm text-red-500">
                                    {errors.reported_trailer_temp}
                                </p>
                            )}
                        </div>
                    )}

                    {shouldShowField(
                        shipment,
                        shipment.next_stop,
                        'arrived_at',
                    ) && (
                        <div className="space-y-2">
                            <Label
                                htmlFor="arrived_at"
                                className="flex items-center"
                            >
                                <span className="font-bold">Arrived At</span>
                                <span className="ml-2 text-xs font-normal text-muted-foreground">
                                    ({shipment.next_stop?.facility?.name})
                                </span>
                            </Label>
                            <DateTimePicker
                                clearable={true}
                                value={
                                    data.arrived_at
                                        ? new Date(
                                              convertForTimezone(
                                                  shipment.next_stop,
                                                  data.arrived_at,
                                              ),
                                          )
                                        : undefined
                                }
                                timezone={
                                    shipment.next_stop?.facility?.location
                                        ?.timezone?.identifier
                                }
                                onChange={(e: Date | undefined) => {
                                    setData(
                                        'arrived_at',
                                        e?.toISOString() || null,
                                    );
                                }}
                            />
                            {errors.arrived_at && (
                                <p className="text-sm text-red-500">
                                    {errors.arrived_at}
                                </p>
                            )}
                        </div>
                    )}

                    {shouldShowField(
                        shipment,
                        shipment.current_stop,
                        'loaded_unloaded_at',
                    ) && (
                        <div className="space-y-2">
                            <Label
                                htmlFor="loaded_unloaded_at"
                                className="flex items-center"
                            >
                                <span className="font-bold">
                                    {shipment.current_stop?.stop_type ===
                                    'pickup'
                                        ? 'Loaded At'
                                        : 'Unloaded At'}
                                </span>
                                {isLoadedUnloadedRequired && (
                                    <span className="ml-1 text-red-500">*</span>
                                )}
                                <span className="ml-2 text-xs font-normal text-muted-foreground">
                                    ({shipment.current_stop?.facility?.name})
                                </span>
                            </Label>
                            <DateTimePicker
                                clearable={true}
                                value={
                                    data.loaded_unloaded_at
                                        ? new Date(
                                              convertForTimezone(
                                                  shipment.current_stop,
                                                  data.loaded_unloaded_at,
                                              ),
                                          )
                                        : undefined
                                }
                                timezone={
                                    shipment.current_stop?.facility?.location
                                        ?.timezone?.identifier
                                }
                                onChange={(e: Date | undefined) => {
                                    setData(
                                        'loaded_unloaded_at',
                                        e?.toISOString() || null,
                                    );
                                }}
                            />
                            {errors.loaded_unloaded_at && (
                                <p className="text-sm text-red-500">
                                    {errors.loaded_unloaded_at}
                                </p>
                            )}
                            {isLoadedUnloadedRequired &&
                                !data.loaded_unloaded_at && (
                                    <p className="text-sm text-red-500">
                                        Required when setting departure time
                                    </p>
                                )}
                        </div>
                    )}

                    {shouldShowField(
                        shipment,
                        shipment.current_stop,
                        'left_at',
                    ) && (
                        <div className="space-y-2">
                            <Label
                                htmlFor="left_at"
                                className="flex items-center"
                            >
                                <span className="font-bold">Left At</span>
                                <span className="ml-2 text-xs font-normal text-muted-foreground">
                                    ({shipment.current_stop?.facility?.name})
                                </span>
                            </Label>
                            <DateTimePicker
                                clearable={true}
                                value={
                                    data.left_at
                                        ? new Date(
                                              convertForTimezone(
                                                  shipment.current_stop,
                                                  data.left_at,
                                              ),
                                          )
                                        : undefined
                                }
                                timezone={
                                    shipment.current_stop?.facility?.location
                                        ?.timezone?.identifier
                                }
                                onChange={(e: Date | undefined) => {
                                    setData(
                                        'left_at',
                                        e?.toISOString() || null,
                                    );
                                }}
                            />
                            {errors.left_at && (
                                <p className="text-sm text-red-500">
                                    {errors.left_at}
                                </p>
                            )}
                        </div>
                    )}

                    <div className="space-y-2">
                        <Label htmlFor="note">Note</Label>
                        <Textarea
                            id="note"
                            value={data.note || ''}
                            onChange={(e) => setData('note', e.target.value)}
                            placeholder="Add a note"
                            rows={3}
                        />
                        {errors.note && (
                            <p className="text-sm text-red-500">
                                {errors.note}
                            </p>
                        )}
                    </div>

                    {shouldShowField(
                        shipment,
                        shipment.next_stop,
                        'is_truck_empty',
                    ) && (
                        <div className="space-y-2">
                            <div className="flex items-center space-x-2">
                                <input
                                    type="checkbox"
                                    id="is_truck_empty"
                                    checked={data.is_truck_empty || false}
                                    onChange={(e) =>
                                        setData(
                                            'is_truck_empty',
                                            e.target.checked,
                                        )
                                    }
                                    className="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                                />
                                <Label htmlFor="is_truck_empty">
                                    Is Truck Empty?
                                </Label>
                            </div>
                            {errors.is_truck_empty && (
                                <p className="text-sm text-red-500">
                                    {errors.is_truck_empty}
                                </p>
                            )}
                        </div>
                    )}

                    {shouldShowField(
                        shipment,
                        shipment.next_stop,
                        'is_late',
                    ) && (
                        <div className="space-y-2">
                            <div className="flex items-center space-x-2">
                                <input
                                    type="checkbox"
                                    id="is_late"
                                    checked={data.is_late || false}
                                    onChange={(e) =>
                                        setData('is_late', e.target.checked)
                                    }
                                    className="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                                />
                                <Label htmlFor="is_late">Is Late?</Label>
                            </div>
                            {errors.is_late && (
                                <p className="text-sm text-red-500">
                                    {errors.is_late}
                                </p>
                            )}
                        </div>
                    )}

                    <DialogFooter>
                        <Button type="submit" disabled={processing}>
                            Add Check Call
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
