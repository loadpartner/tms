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
import { Textarea } from '@/Components/ui/textarea';
import { useToast } from '@/hooks/UseToast';
import { router } from '@inertiajs/react';
import { CheckCircle, Send } from 'lucide-react';
import { useState } from 'react';

interface FeedbackModalProps {
    isOpen: boolean;
    onClose: () => void;
}

export default function FeedbackModal({ isOpen, onClose }: FeedbackModalProps) {
    const [email, setEmail] = useState('');
    const [message, setMessage] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [errors, setErrors] = useState<{ email?: string; feedback?: string }>(
        {},
    );
    const { toast } = useToast();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        if (isSubmitting) return;

        // Reset errors
        setErrors({});

        // Basic validation
        const newErrors: { email?: string; feedback?: string } = {};
        if (!email) {
            newErrors.email = 'Email is required';
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            newErrors.email = 'Please enter a valid email address';
        }

        if (!message) {
            newErrors.feedback = 'Message is required';
        } else if (message.length < 10) {
            newErrors.feedback = 'Message must be at least 10 characters';
        }

        if (Object.keys(newErrors).length > 0) {
            setErrors(newErrors);
            return;
        }

        setIsSubmitting(true);

        // Add "Enterprise Inquiry" prefix to the message
        const enterpriseMessage = `Enterprise Inquiry: ${message}`;

        router.post(
            route('feedback.submit'),
            {
                email: email,
                feedback: enterpriseMessage,
            },
            {
                onSuccess: () => {
                    setEmail('');
                    setMessage('');
                    setErrors({});
                    onClose();

                    // Show success toast
                    toast({
                        title: 'Thank you for your inquiry!',
                        description: (
                            <>
                                <CheckCircle
                                    className="mr-2 inline h-4 w-4"
                                    color="green"
                                />
                                We've received your enterprise inquiry and will
                                get back to you within 24 hours.
                            </>
                        ),
                    });
                },
                onError: (errors) => {
                    setErrors(errors);
                    toast({
                        title: 'Submission failed',
                        description:
                            'There was an error submitting your inquiry. Please try again.',
                        variant: 'destructive',
                    });
                },
                onFinish: () => {
                    setIsSubmitting(false);
                },
            },
        );
    };

    const handleClose = () => {
        if (isSubmitting) return;
        setEmail('');
        setMessage('');
        setErrors({});
        onClose();
    };

    return (
        <Dialog open={isOpen} onOpenChange={handleClose}>
            <DialogContent className="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Enterprise Inquiry</DialogTitle>
                    <DialogDescription>
                        Tell us about your enterprise needs and we'll get back
                        to you shortly.
                    </DialogDescription>
                </DialogHeader>

                <form onSubmit={handleSubmit}>
                    <div className="grid gap-4 py-4">
                        <div className="grid gap-2">
                            <Label htmlFor="email">Email Address</Label>
                            <Input
                                id="email"
                                type="email"
                                placeholder="your@company.com"
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                disabled={isSubmitting}
                                className={errors.email ? 'border-red-500' : ''}
                            />
                            {errors.email && (
                                <p className="text-sm text-red-500">
                                    {errors.email}
                                </p>
                            )}
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="message">Message</Label>
                            <Textarea
                                id="message"
                                placeholder="Tell us about your enterprise requirements, team size, specific features needed, or any questions you have..."
                                value={message}
                                onChange={(e) => setMessage(e.target.value)}
                                disabled={isSubmitting}
                                rows={4}
                                className={
                                    errors.feedback ? 'border-red-500' : ''
                                }
                            />
                            {errors.feedback && (
                                <p className="text-sm text-red-500">
                                    {errors.feedback}
                                </p>
                            )}
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
                        <Button type="submit" disabled={isSubmitting}>
                            {isSubmitting ? (
                                <>
                                    <div className="mr-2 h-4 w-4 animate-spin rounded-full border-2 border-background border-t-transparent" />
                                    Sending...
                                </>
                            ) : (
                                <>
                                    <Send className="mr-2 h-4 w-4" />
                                    Send Inquiry
                                </>
                            )}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
