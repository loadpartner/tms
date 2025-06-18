import NewCheckCallButton from '@/Components/CheckCalls/NewCheckCallButton';
import DocumentPreviewDialog from '@/Components/DocumentPreviewDialog';
import { Button } from '@/Components/ui/button';
import { ConfirmDropdownMenuItem } from '@/Components/ui/confirm-dropdown-menu-item';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/Components/ui/radio-group';
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
    Receipt,
    Undo2,
    X,
} from 'lucide-react';
import { useState } from 'react';

export default function ShipmentHeader({ shipment }: { shipment: Shipment }) {
    const [editMode, setEditMode] = useState(false);
    const [showRateConDialog, setShowRateConDialog] = useState(false);
    const [showInvoiceDialog, setShowInvoiceDialog] = useState(false);
    const [selectedCustomerId, setSelectedCustomerId] = useState<string | null>(
        null,
    );

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

    const uncancelShipment = () => {
        patch(route('shipments.uncancel', { shipment: shipment.id }));
    };

    const viewRateCon = () => {
        setShowRateConDialog(true);
    };

    const downloadDocument = (document: Document) => {
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

    const openInvoiceDialog = () => {
        setShowInvoiceDialog(true);
    };

    const regenerateInvoice = () => {
        if (!selectedCustomerId) {
            toast({
                description: (
                    <>
                        <AlertCircle
                            className="mr-2 inline h-4 w-4"
                            color="red"
                        />
                        Please select a customer
                    </>
                ),
            });
            return;
        }

        post(
            route('shipments.documents.generate-customer-invoice', {
                shipment: shipment.id,
                customer: selectedCustomerId,
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
                                Invoice generated successfully!
                            </>
                        ),
                    });
                    setShowInvoiceDialog(false);
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
                                Failed to regenerate invoice! Please contact
                                support.
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
                    buttonVariant="default"
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
                    <Button onClick={viewRateCon} variant={'outline'}>
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
                        <DropdownMenuItem onClick={openInvoiceDialog}>
                            <Receipt className="mr-2 h-4 w-4" />
                            Regenerate Invoice
                        </DropdownMenuItem>
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
                            <ConfirmDropdownMenuItem
                                onConfirm={() => {
                                    uncancelShipment();
                                }}
                            >
                                <Undo2 className="mr-2 h-4 w-4" />
                                Un-Cancel Shipment
                            </ConfirmDropdownMenuItem>
                        )}
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>

            {/* Document Preview Dialog */}
            <DocumentPreviewDialog
                document={shipment.latest_rate_confirmation}
                open={showRateConDialog}
                onOpenChange={setShowRateConDialog}
                onDownload={downloadDocument}
            />

            {/* Customer Selection Dialog */}
            <Dialog
                open={showInvoiceDialog}
                onOpenChange={setShowInvoiceDialog}
            >
                <DialogContent className="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle>Regenerate Invoice</DialogTitle>
                    </DialogHeader>
                    <div className="py-4">
                        <p className="mb-4">
                            Select a customer to generate an invoice:
                        </p>
                        <RadioGroup
                            value={selectedCustomerId}
                            onValueChange={(value: string) =>
                                setSelectedCustomerId(value)
                            }
                        >
                            {shipment.customers &&
                            shipment.customers.length > 0 ? (
                                shipment.customers.map((customer) => (
                                    <div
                                        key={customer.id}
                                        className="mb-2 flex items-center space-x-2"
                                    >
                                        <RadioGroupItem
                                            value={customer.id.toString()}
                                            id={`customer-${customer.id}`}
                                        />
                                        <Label
                                            htmlFor={`customer-${customer.id}`}
                                        >
                                            {customer.name}
                                        </Label>
                                    </div>
                                ))
                            ) : (
                                <p className="text-muted-foreground">
                                    No customers available for this shipment.
                                </p>
                            )}
                        </RadioGroup>
                    </div>
                    <DialogFooter>
                        <Button
                            onClick={() => setShowInvoiceDialog(false)}
                            variant="outline"
                        >
                            Cancel
                        </Button>
                        <Button onClick={regenerateInvoice}>Regenerate</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    );
}
