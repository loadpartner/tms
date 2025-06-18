import InputError from '@/Components/InputError';
import { ResourceSearchSelect } from '@/Components/ResourceSearchSelect';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
} from '@/Components/ui/dialog';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import { Textarea } from '@/Components/ui/textarea';
import { useToast } from '@/hooks/UseToast';
import { Shipment } from '@/types';
import { useForm } from '@inertiajs/react';
import {
    Check,
    CheckCircle2,
    ExternalLink,
    Ghost,
    Pencil,
    Truck,
    X,
} from 'lucide-react';
import { useEffect, useState } from 'react';

export default function CarrierDetails({ shipment }: { shipment: Shipment }) {
    const [editMode, setEditMode] = useState(false);

    const { toast } = useToast();

    const [bounceModalOpen, setBounceModalOpen] = useState(false);

    const { patch, setData, data } = useForm({
        carrier_id: shipment.carrier?.id,
        driver_id: shipment.driver?.id,
    });

    const updateShipment = () => {
        patch(
            route('shipments.updateCarrierDetails', {
                shipment: shipment.id,
            }),
            {
                onSuccess: () => {
                    setEditMode(false);
                    toast({
                        description: (
                            <>
                                <CheckCircle2
                                    className="mr-2 inline h-4 w-4"
                                    color="green"
                                />
                                Carrier details updated
                            </>
                        ),
                    });
                },
            },
        );
    };

    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex items-center justify-between gap-2">
                    <div className="flex items-center gap-2">
                        <Truck className="h-5 w-5" />
                        Carrier
                    </div>
                    {editMode ? (
                        <>
                            <div className="flex gap-2">
                                <Button
                                    variant="ghost"
                                    onClick={updateShipment}
                                >
                                    <Check />
                                </Button>
                                <Button
                                    variant="ghost"
                                    onClick={() => {
                                        setEditMode(false);
                                        setData({
                                            carrier_id: shipment.carrier?.id,
                                            driver_id: shipment.driver?.id,
                                        });
                                    }}
                                >
                                    <X />
                                </Button>
                            </div>
                        </>
                    ) : (
                        <Button
                            variant="ghost"
                            onClick={() => setEditMode(true)}
                        >
                            <Pencil />
                        </Button>
                    )}
                </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
                <div className="flex flex-col gap-2">
                    <label className="text-sm font-medium">Carrier</label>
                    {editMode ? (
                        <ResourceSearchSelect
                            className="w-full"
                            searchRoute={route('carriers.search')}
                            onValueChange={(value) => {
                                setData('carrier_id', Number(value));
                                setData('driver_id', undefined);
                            }}
                            allowMultiple={false}
                            defaultSelectedItems={data.carrier_id}
                            allowUnselect={false}
                        />
                    ) : (
                        <>
                            {shipment.carrier?.id ? (
                                <a
                                    target="external"
                                    href={route('carriers.show', {
                                        carrier: shipment.carrier.id,
                                    })}
                                >
                                    {shipment.carrier?.name}
                                    <ExternalLink className="ml-1 inline h-4 w-4" />
                                </a>
                            ) : (
                                <p className="text-muted-foreground">-</p>
                            )}
                        </>
                    )}
                </div>
                <div className="flex flex-col gap-2">
                    <label className="text-sm font-medium">Driver</label>
                    {editMode ? (
                        <ResourceSearchSelect
                            className="w-full"
                            searchRoute={route('contacts.search')}
                            onValueChange={(value) =>
                                setData(
                                    'driver_id',
                                    value ? Number(value) : undefined,
                                )
                            }
                            allowMultiple={false}
                            defaultSelectedItems={data.driver_id}
                            allowUnselect={true}
                            requiredFilters={[
                                {
                                    name: 'contact_for_type',
                                    value: 'carrier',
                                },
                                {
                                    name: 'contact_for_id',
                                    value: data.carrier_id?.toString() ?? '',
                                },
                            ]}
                            autoLoadOptions={true}
                        />
                    ) : (
                        <p>{shipment.driver?.name ?? '-'}</p>
                    )}
                </div>
                <div className="flex">
                    {!editMode && shipment.carrier?.id && (
                        <Button
                            variant="ghost"
                            onClick={() => {
                                setBounceModalOpen(true);
                            }}
                            className="text-destructive"
                        >
                            <Ghost className="h-4 w-4" />
                            Bounce
                        </Button>
                    )}
                    <BounceCarrierModal
                        shipment={shipment}
                        open={bounceModalOpen}
                        onOpenChange={setBounceModalOpen}
                        onBounce={() => {
                            setEditMode(false);
                            setData('carrier_id', 0);
                            setData('driver_id', 0);
                        }}
                    />
                </div>
            </CardContent>
        </Card>
    );
}

function BounceCarrierModal({
    shipment,
    open,
    onOpenChange,
    onBounce,
}: {
    shipment: Shipment;
    open: boolean;
    onOpenChange: (open: boolean) => void;
    onBounce: () => void;
}) {
    const { toast } = useToast();

    const [bounceReasons, setBounceReasons] = useState<string[]>([]);

    const { post, setData, data, errors, reset, clearErrors } = useForm({
        bounce_cause: '',
        reason: '',
    });

    useEffect(() => {
        fetch(route('bounce-reasons')).then((response) => {
            response.json().then((data) => {
                setBounceReasons(data);
            });
        });
    }, []);

    useEffect(() => {
        reset();
        clearErrors();

        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [open]);

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent>
                <DialogHeader>Bounce Carrier</DialogHeader>

                <div className="space-y-2">
                    <div className="flex flex-col gap-2">
                        <label className="text-sm font-medium">
                            Bounce Cause
                        </label>
                        <Select
                            value={data.bounce_cause}
                            onValueChange={(value) =>
                                setData('bounce_cause', value)
                            }
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Select a bounce cause" />
                            </SelectTrigger>
                            <SelectContent>
                                {bounceReasons.length > 0 &&
                                    bounceReasons.map((reason) => (
                                        <SelectItem key={reason} value={reason}>
                                            {reason}
                                        </SelectItem>
                                    ))}
                            </SelectContent>
                        </Select>
                        <InputError message={errors.bounce_cause} />
                    </div>
                    <div className="flex flex-col gap-2">
                        <label className="text-sm font-medium">
                            Reason (optional)
                        </label>
                        <Textarea
                            value={data.reason}
                            onChange={(e) => setData('reason', e.target.value)}
                        />
                        <InputError message={errors.reason} />
                    </div>
                </div>

                <DialogFooter>
                    <Button
                        variant="outline"
                        onClick={() => {
                            onOpenChange(false);
                        }}
                    >
                        Cancel
                    </Button>
                    <Button
                        variant="destructive"
                        onClick={() => {
                            post(
                                route('shipments.bounce', {
                                    shipment: shipment.id,
                                }),
                                {
                                    onSuccess: () => {
                                        onOpenChange(false);
                                        toast({
                                            description:
                                                'Carrier bounced successfully',
                                        });
                                        onBounce();
                                    },
                                },
                            );
                        }}
                    >
                        Bounce
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
