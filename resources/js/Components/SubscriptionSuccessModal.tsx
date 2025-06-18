import { Button } from '@/Components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';
import { CheckCircle, Sparkles } from 'lucide-react';
import { useEffect, useRef } from 'react';

interface SubscriptionSuccessModalProps {
    isOpen: boolean;
    onClose: () => void;
}

interface ConfettiInstance {
    fire: (options: {
        particleCount: number;
        spread: number;
        origin?: { x?: number; y?: number };
        angle?: number;
    }) => Promise<void>;
}

export default function SubscriptionSuccessModal({
    isOpen,
    onClose,
}: SubscriptionSuccessModalProps) {
    const confettiRef = useRef<ConfettiInstance | null>(null);

    useEffect(() => {
        if (isOpen && confettiRef.current) {
            // Fire confetti when modal opens
            const fireConfetti = async () => {
                // Multiple bursts for celebration effect
                if (confettiRef.current) {
                    await confettiRef.current.fire({
                        particleCount: 100,
                        spread: 70,
                        origin: { y: 0.6 },
                    });
                }

                setTimeout(async () => {
                    if (confettiRef.current) {
                        await confettiRef.current.fire({
                            particleCount: 50,
                            angle: 60,
                            spread: 55,
                            origin: { x: 0 },
                        });
                    }
                }, 250);

                setTimeout(async () => {
                    if (confettiRef.current) {
                        await confettiRef.current.fire({
                            particleCount: 50,
                            angle: 120,
                            spread: 55,
                            origin: { x: 1 },
                        });
                    }
                }, 400);
            };

            fireConfetti();
        }
    }, [isOpen]);

    return (
        <>
            <Dialog open={isOpen} onOpenChange={onClose}>
                <DialogContent className="text-center sm:max-w-md">
                    <DialogHeader>
                        <div className="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                            <CheckCircle className="h-8 w-8 text-green-600 dark:text-green-400" />
                        </div>
                        <DialogTitle className="text-2xl font-bold">
                            🎉 Welcome to Premium!
                        </DialogTitle>
                        <DialogDescription className="text-lg">
                            Thank you for subscribing! Your account has been
                            upgraded and you now have access to all premium
                            features.
                        </DialogDescription>
                    </DialogHeader>

                    <div className="py-6">
                        <div className="space-y-3 text-sm text-muted-foreground">
                            <div className="flex items-center justify-center gap-2">
                                <Sparkles className="h-4 w-4 text-yellow-500" />
                                <span>Managed hosting activated</span>
                            </div>
                            <div className="flex items-center justify-center gap-2">
                                <Sparkles className="h-4 w-4 text-yellow-500" />
                                <span>Priority support enabled</span>
                            </div>
                            <div className="flex items-center justify-center gap-2">
                                <Sparkles className="h-4 w-4 text-yellow-500" />
                                <span>Advanced analytics unlocked</span>
                            </div>
                        </div>
                    </div>

                    <DialogFooter className="sm:justify-center">
                        <Button onClick={onClose} className="w-full sm:w-auto">
                            Get Started
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </>
    );
}
