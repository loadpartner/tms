import { Button } from '@/Components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { ArrowRight } from 'lucide-react';
import { useState } from 'react';

interface SeatSelectionModalProps {
    isOpen: boolean;
    onClose: () => void;
}

export default function SeatSelectionModal({
    isOpen,
    onClose,
}: SeatSelectionModalProps) {
    const [seatCount, setSeatCount] = useState(1);
    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setIsSubmitting(true);

        // Build the URL with seat_count parameter
        const url = new URL(route('subscriptions.new'), window.location.origin);
        url.searchParams.set('seat_count', seatCount.toString());

        // Redirect to the subscription page
        window.location.href = url.toString();
    };

    const handleClose = () => {
        if (!isSubmitting) {
            onClose();
            setSeatCount(1); // Reset seat count when closing
        }
    };

    return (
        <Dialog open={isOpen} onOpenChange={handleClose}>
            <DialogContent className="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Select Number of Seats</DialogTitle>
                    <DialogDescription>
                        How many user seats would you like to include in your
                        Managed plan subscription?
                    </DialogDescription>
                </DialogHeader>

                <form onSubmit={handleSubmit}>
                    <div className="grid gap-4 py-4">
                        <div className="grid grid-cols-4 items-center gap-4">
                            <Label htmlFor="seatCount" className="text-right">
                                Seats
                            </Label>
                            <div className="col-span-3">
                                <Input
                                    id="seatCount"
                                    type="number"
                                    min="1"
                                    max="100"
                                    value={seatCount}
                                    onChange={(e) =>
                                        setSeatCount(
                                            parseInt(e.target.value) || 1,
                                        )
                                    }
                                    className="w-full"
                                    disabled={isSubmitting}
                                />
                            </div>
                        </div>

                        <div className="text-sm text-muted-foreground">
                            <p>Each seat costs $50/month.</p>
                            <p className="mt-1 font-semibold">
                                Total: ${(seatCount * 50).toLocaleString()}
                                /month
                            </p>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            onClick={handleClose}
                            disabled={isSubmitting}
                        >
                            Cancel
                        </Button>
                        <Button
                            type="submit"
                            disabled={isSubmitting || seatCount < 1}
                        >
                            {isSubmitting ? (
                                'Processing...'
                            ) : (
                                <>
                                    <ArrowRight className="h-4 w-4" />
                                    Continue to Checkout
                                </>
                            )}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
