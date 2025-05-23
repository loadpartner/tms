import NewCheckCallButton from '@/Components/CheckCalls/NewCheckCallButton';
import DocumentPreviewDialog from '@/Components/DocumentPreviewDialog';
import { Button } from '@/Components/ui/button';
import { ConfirmDropdownMenuItem } from '@/Components/ui/confirm-dropdown-menu-item';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';
import { Input } from '@/Components/ui/input';
import { useToast } from '@/hooks/UseToast';
import { Document, Shipment } from '@/types';
import { ShipmentState } from '@/types/enums';
import { useForm } from '@inertiajs/react';
import {
    AlertCircle,
    ArrowRight,
    Check,
    CheckCircle2,
    FileText,
    MoreHorizontal,
    Pencil,
    Undo2,
    X,
} from 'lucide-react';
import { useState } from 'react';

export default function ShipmentHeader({ shipment }: { shipment: Shipment }) {
    const [editMode, setEditMode] = useState(false);
    const [showRateConDialog, setShowRateConDialog] = useState(false);

    const { toast } = useToast();

    const { post, patch, setData, data } = useForm({
        shipment_number: shipment.shipment_number,
    });

    const dispatchShipment = () => {
        patch(route('shipments.dispatch', { shipment: shipment.id }));
    };

    const cancelShipment = () => {
        patch(route('shipments.cancel', { shipment: shipment.id }));
    };

    const viewRateCon = () => {
        setShowRateConDialog(true);
    };

    const downloadRateCon = (document: Document) => {
        // Create a download link for the document
        const downloadUrl = route('documents.show', document.id);

        // Create an invisible anchor element
        const link = window.document.createElement('a');
        link.href = downloadUrl;
        link.setAttribute('download', document.name);
        window.document.body.appendChild(link);

        // Trigger the download
        link.click();

        // Clean up - remove the element
        window.document.body.removeChild(link);
    };

    const generateRateCon = () => {
        post(
            route('shipments.documents.generate-rate-confirmation', {
                shipment: shipment.id,
            }),
            {
                onSuccess: () => {
                    toast({
                        description: (
                            <>
                                <CheckCircle2
                                    className="mr-2 inline h-4 w-4"
                                    color="green"
                                />
                                Rate confirmation generated!
                            </>
                        ),
                    });
                },
                onError: (error) => {
                    console.error(error);
                    toast({
                        description: (
                            <>
                                <AlertCircle
                                    className="mr-2 inline h-4 w-4"
                                    color="red"
                                />
                                Rate confirmation failed to generate! Please
                                contact support!
                            </>
                        ),
                    });
                },
            },
        );
    };

    const updateShipmentNumber = () => {
        patch(
            route('shipments.updateShipmentNumber', {
                shipment: shipment.id,
            }),
            {
                onSuccess: () => {
                    toast({
                        description: (
                            <>
                                <CheckCircle2
                                    className="mr-2 inline h-4 w-4"
                                    color="green"
                                />
                                Shipment number updated
                            </>
                        ),
                    });
                },
            },
        );
    };

    return (
        <div className="flex flex-wrap items-center justify-between gap-4">
            <div>
                <div className="flex items-center gap-2">
                    <h1 className="text-2xl font-bold">Shipment</h1>
                    {editMode ? (
                        <Input
                            value={data.shipment_number ?? ''}
                            onKeyDown={(e) => {
                                if (e.key === 'Enter') {
                                    e.preventDefault();
                                    setEditMode(false);
                                    updateShipmentNumber();
                                }
                            }}
                            onChange={(e) =>
                                setData('shipment_number', e.target.value)
                            }
                            placeholder="#####"
                            className="text-2xl font-bold"
                        />
                    ) : (
                        <h1 className="text-2xl font-bold">
                            {data.shipment_number ?? `id:${shipment.id}`}
                        </h1>
                    )}
                    {editMode ? (
                        <>
                            <Button
                                variant="ghost"
                                onClick={() => {
                                    setEditMode(false);
                                    updateShipmentNumber();
                                }}
                            >
                                <Check />
                            </Button>
                            <Button
                                variant="ghost"
                                onClick={() => {
                                    setEditMode(false);
                                    setData(
                                        'shipment_number',
                                        shipment.shipment_number,
                                    );
                                }}
                            >
                                <X />
                            </Button>
                        </>
                    ) : (
                        <Button
                            className="inline"
                            variant="ghost"
                            onClick={() => setEditMode(!editMode)}
                        >
                            <Pencil />
                        </Button>
                    )}
                </div>
                <p className="text-muted-foreground">{shipment.state_label}</p>
            </div>
            <div className="flex flex-wrap gap-2">
                <NewCheckCallButton
                    shipment={shipment}
                    carrierId={shipment.carrier?.id}
                    carrierContacts={shipment.carrier?.contacts || []}
                    buttonVariant="outline"
                    buttonText="Check Call"
                />

                {shipment.state === ShipmentState.Booked && (
                    <Button
                        onClick={() => {
                            dispatchShipment();
                        }}
                    >
                        <ArrowRight className="mr-2 h-4 w-4" />
                        Dispatch
                    </Button>
                )}

                {shipment.latest_rate_confirmation ? (
                    <Button onClick={viewRateCon}>
                        <FileText className="mr-2 h-4 w-4" />
                        View Ratecon
                    </Button>
                ) : (
                    <Button onClick={generateRateCon}>
                        <FileText className="mr-2 h-4 w-4" />
                        Generate Ratecon
                    </Button>
                )}

                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="ghost" className="h-8 w-8 p-0">
                            <span className="sr-only">Open menu</span>
                            <MoreHorizontal className="h-4 w-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent>
                        {shipment.state !== ShipmentState.Canceled && (
                            <ConfirmDropdownMenuItem
                                onConfirm={() => {
                                    cancelShipment();
                                }}
                            >
                                <X className="mr-2 h-4 w-4" />
                                Cancel Shipment
                            </ConfirmDropdownMenuItem>
                        )}
                        {shipment.state === ShipmentState.Canceled && (
                            <DropdownMenuItem disabled={true}>
                                <Undo2 className="mr-2 h-4 w-4" />
                                Un-Cancel Shipment
                            </DropdownMenuItem>
                        )}
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>

            {/* Document Preview Dialog */}
            <DocumentPreviewDialog
                document={shipment.latest_rate_confirmation}
                open={showRateConDialog}
                onOpenChange={setShowRateConDialog}
                onDownload={downloadRateCon}
            />
        </div>
    );
}
