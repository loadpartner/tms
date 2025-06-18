import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Organization } from '@/types/organization';
import { useForm } from '@inertiajs/react';
import { CreditCard, Settings } from 'lucide-react';
import { useState } from 'react';

interface Subscription {
    id: number;
    type: string;
    stripe_status: string;
    quantity: number;
    trial_ends_at?: string;
    ends_at?: string;
}

export default function BillingForm({
    organization,
    subscription,
}: {
    organization: Organization;
    subscription?: Subscription;
}) {
    const { data, setData, processing, errors, put } = useForm({
        quantity: subscription?.quantity || 1,
    });

    const [isUpdating, setIsUpdating] = useState(false);

    const handleUpdateSeats = (e: React.FormEvent) => {
        e.preventDefault();
        setIsUpdating(true);

        put(route('organizations.billing.update-seats', organization.id), {
            onFinish: () => setIsUpdating(false),
        });
    };

    const handleManageBilling = () => {
        window.location.replace(route('subscriptions.manage', organization.id));
    };

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'active':
                return <Badge variant="default">Active</Badge>;
            case 'trialing':
                return <Badge variant="secondary">Trial</Badge>;
            case 'past_due':
                return <Badge variant="destructive">Past Due</Badge>;
            case 'canceled':
                return <Badge variant="outline">Canceled</Badge>;
            default:
                return <Badge variant="outline">{status}</Badge>;
        }
    };

    const formatDate = (dateString?: string) => {
        if (!dateString) return null;
        return new Date(dateString).toLocaleDateString();
    };

    const monthlyPrice = 50; // From config

    return (
        <div className="space-y-6">
            {/* Current Subscription Status */}
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <CreditCard className="h-5 w-5" />
                        Current Subscription
                    </CardTitle>
                    <CardDescription>
                        Your current billing plan and usage
                    </CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                    {subscription ? (
                        <>
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div>
                                    <Label className="text-sm font-medium text-muted-foreground">
                                        Plan Type
                                    </Label>
                                    <div className="mt-1">
                                        <span className="text-lg font-medium">
                                            Premium
                                        </span>
                                        <div className="text-sm text-muted-foreground">
                                            User Seat Subscription
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <Label className="text-sm font-medium text-muted-foreground">
                                        Status
                                    </Label>
                                    <div className="mt-1">
                                        {getStatusBadge(
                                            subscription.stripe_status,
                                        )}
                                    </div>
                                </div>

                                <div>
                                    <Label className="text-sm font-medium text-muted-foreground">
                                        Monthly Total
                                    </Label>
                                    <div className="mt-1">
                                        <span className="text-lg font-medium">
                                            $
                                            {(
                                                subscription.quantity *
                                                monthlyPrice
                                            ).toLocaleString()}
                                        </span>
                                        <div className="text-sm text-muted-foreground">
                                            {subscription.quantity} seats × $
                                            {monthlyPrice}/month
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {subscription.trial_ends_at && (
                                <div className="rounded-md border border-blue-200 bg-blue-50 p-3">
                                    <div className="text-sm">
                                        <strong>Trial Period:</strong> Your
                                        trial ends on{' '}
                                        {formatDate(subscription.trial_ends_at)}
                                    </div>
                                </div>
                            )}
                        </>
                    ) : (
                        <div className="py-8 text-center">
                            <div className="text-muted-foreground">
                                No active subscription found
                            </div>
                        </div>
                    )}
                </CardContent>
            </Card>

            {/* Update Seats */}
            {subscription && (
                <Card>
                    <CardHeader>
                        <CardTitle>Update Seats</CardTitle>
                        <CardDescription>
                            Change the number of user seats for your
                            subscription
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form
                            onSubmit={handleUpdateSeats}
                            className="space-y-4"
                        >
                            <div className="max-w-xs">
                                <Label htmlFor="quantity">
                                    Number of Seats
                                </Label>
                                <Input
                                    id="quantity"
                                    type="number"
                                    min="1"
                                    max="1000"
                                    value={data.quantity}
                                    onChange={(e) =>
                                        setData(
                                            'quantity',
                                            parseInt(e.target.value) || 1,
                                        )
                                    }
                                    required
                                />
                                {errors.quantity && (
                                    <div className="mt-1 text-sm text-destructive">
                                        {errors.quantity}
                                    </div>
                                )}
                                <div className="mt-1 text-sm text-muted-foreground">
                                    New monthly total: $
                                    {(
                                        data.quantity * monthlyPrice
                                    ).toLocaleString()}
                                </div>
                            </div>

                            <Button
                                type="submit"
                                disabled={
                                    processing ||
                                    isUpdating ||
                                    data.quantity === subscription.quantity
                                }
                                size="sm"
                            >
                                {processing || isUpdating
                                    ? 'Updating...'
                                    : 'Update Seats'}
                            </Button>
                        </form>
                    </CardContent>
                </Card>
            )}

            {/* Billing Management */}
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <Settings className="h-5 w-5" />
                        Billing Management
                    </CardTitle>
                    <CardDescription>
                        Manage your payment methods, billing history, and
                        invoices
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Button
                        onClick={handleManageBilling}
                        variant="outline"
                        className="w-full sm:w-auto"
                    >
                        Open Billing Portal
                    </Button>
                    <div className="mt-2 text-sm text-muted-foreground">
                        You'll be redirected to Stripe's secure billing portal
                        where you can update payment methods, download invoices,
                        and view billing history.
                    </div>
                </CardContent>
            </Card>
        </div>
    );
}
