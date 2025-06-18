import InputError from '@/Components/InputError';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/Components/ui/alert-dialog';
import { Button } from '@/Components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableFooter,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import { useToast } from '@/hooks/UseToast';
import { Organization, OrganizationInvite } from '@/types/organization';
import { useForm, usePage } from '@inertiajs/react';
import { MoreHorizontal } from 'lucide-react';
import { FormEventHandler, useState } from 'react';

interface Subscription {
    id: number;
    type: string;
    stripe_status: string;
    quantity: number;
    trial_ends_at?: string;
    ends_at?: string;
}

interface SeatUsage {
    current_members: number;
    pending_invites: number;
    total_used: number;
    max_seats: number;
    has_available_seats: boolean;
    has_subscription: boolean;
}

export default function InvitesTable({
    invites,
    organization,
    subscription,
    seatUsage,
}: {
    invites: OrganizationInvite[];
    organization: Organization;
    subscription?: Subscription;
    seatUsage?: SeatUsage;
}) {
    const organizationId = usePage().props.auth.user.current_organization_id;
    const config = usePage().props.config;

    const { toast } = useToast();

    const [open, setOpen] = useState(false);
    const [showSeatLimitAlert, setShowSeatLimitAlert] = useState(false);

    const {
        data,
        setData,
        post,
        processing,
        errors,
        reset,
        delete: destroy,
    } = useForm({
        email: '',
    });

    // Provide default seat usage if not provided
    const defaultSeatUsage: SeatUsage = {
        current_members: organization.users?.length || 0,
        pending_invites: invites.length,
        total_used: (organization.users?.length || 0) + invites.length,
        max_seats: subscription?.quantity || 0,
        has_available_seats: subscription
            ? (organization.users?.length || 0) + invites.length <
              subscription.quantity
            : false,
        has_subscription: !!subscription,
    };

    const effectiveSeatUsage = seatUsage || defaultSeatUsage;

    const handleNewInviteClick = () => {
        // If billing is disabled, allow invites without subscription checks
        if (!config?.enable_billing) {
            setOpen(true);
            return;
        }

        if (!effectiveSeatUsage.has_subscription) {
            toast({
                title: 'No subscription found',
                description: 'Please set up a subscription to invite users.',
                variant: 'destructive',
            });
            return;
        }

        if (!effectiveSeatUsage.has_available_seats) {
            setShowSeatLimitAlert(true);
            return;
        }

        setOpen(true);
    };

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(
            route('organizations.invites.store', {
                organization: organizationId,
            }),
            {
                onFinish: () => reset('email'),
                onSuccess: () => {
                    setOpen(false);
                    toast({
                        title: 'Invite sent to ' + data.email,
                        description: 'Expires in 7 days.',
                    });
                },
                onError: (errors) => {
                    // Handle seat limit error specifically
                    if (
                        errors.email &&
                        errors.email.includes('Not enough seats')
                    ) {
                        setOpen(false);
                        setShowSeatLimitAlert(true);
                    }
                },
            },
        );
    };

    return (
        <>
            <Table>
                <TableCaption>
                    A list of pending organization invites.
                    {config?.enable_billing &&
                        effectiveSeatUsage.has_subscription && (
                            <div className="mt-2 text-sm text-muted-foreground">
                                Seat usage: {effectiveSeatUsage.total_used} of{' '}
                                {effectiveSeatUsage.max_seats} seats used
                                {!effectiveSeatUsage.has_available_seats && (
                                    <span className="font-medium text-destructive">
                                        {' '}
                                        (At capacity)
                                    </span>
                                )}
                            </div>
                        )}
                </TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead className="w-[100px]">Email</TableHead>
                        <TableHead>Sent</TableHead>
                        <TableHead>Expires</TableHead>
                        <TableHead className="text-right">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {invites.map((invite) => (
                        <TableRow key={invite.id}>
                            <TableCell>{invite.email}</TableCell>
                            <TableCell>
                                {new Date(invite.created_at).toLocaleString()}
                            </TableCell>
                            <TableCell>
                                {new Date(invite.expire_at).toLocaleString()}
                            </TableCell>
                            <TableCell className="text-right">
                                <DropdownMenu>
                                    <DropdownMenuTrigger asChild>
                                        <Button
                                            variant="ghost"
                                            className="h-8 w-8 p-0"
                                        >
                                            <span className="sr-only">
                                                Open menu
                                            </span>
                                            <MoreHorizontal className="h-4 w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuLabel>
                                            Actions
                                        </DropdownMenuLabel>
                                        <DropdownMenuItem
                                            onClick={() =>
                                                post(
                                                    route(
                                                        'organizations.invites.resend',
                                                        {
                                                            organization:
                                                                invite.organization_id,
                                                            invite: invite.code,
                                                        },
                                                    ),
                                                    {
                                                        onSuccess: () => {
                                                            toast({
                                                                description:
                                                                    'Invite resent to ' +
                                                                    invite.email,
                                                            });
                                                        },
                                                    },
                                                )
                                            }
                                        >
                                            Resend
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            onClick={() =>
                                                destroy(
                                                    route(
                                                        'organizations.invites.destroy',
                                                        {
                                                            organization:
                                                                invite.organization_id,
                                                            invite: invite.code,
                                                        },
                                                    ),
                                                    {
                                                        onSuccess: () => {
                                                            toast({
                                                                description:
                                                                    'Invite canceled',
                                                            });
                                                        },
                                                    },
                                                )
                                            }
                                        >
                                            Cancel
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
                <TableFooter>
                    <TableRow>
                        <TableCell colSpan={4} className="text-right">
                            <Button
                                onClick={handleNewInviteClick}
                                disabled={
                                    config?.enable_billing
                                        ? !effectiveSeatUsage.has_subscription
                                        : false
                                }
                            >
                                New Invite
                            </Button>
                            <Dialog open={open} onOpenChange={setOpen}>
                                <DialogContent>
                                    <form onSubmit={submit}>
                                        <DialogHeader>
                                            <DialogTitle>
                                                Invite a new user to your
                                                organization
                                            </DialogTitle>
                                            <DialogDescription>
                                                <div className="my-4">
                                                    <Label htmlFor="invite-email">
                                                        Email
                                                    </Label>
                                                    <Input
                                                        id="invite-email"
                                                        type="email"
                                                        value={data.email}
                                                        placeholder="m@example.com"
                                                        required
                                                        onChange={(e) =>
                                                            setData(
                                                                'email',
                                                                e.target.value,
                                                            )
                                                        }
                                                    />
                                                    <InputError
                                                        message={errors.email}
                                                    />
                                                </div>
                                            </DialogDescription>
                                        </DialogHeader>
                                        <DialogFooter>
                                            <Button
                                                type="submit"
                                                disabled={processing}
                                            >
                                                Send
                                            </Button>
                                        </DialogFooter>
                                    </form>
                                </DialogContent>
                            </Dialog>
                        </TableCell>
                    </TableRow>
                </TableFooter>
            </Table>

            {/* Seat Limit Alert Dialog */}
            <AlertDialog
                open={showSeatLimitAlert}
                onOpenChange={setShowSeatLimitAlert}
            >
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Not Enough Seats</AlertDialogTitle>
                        <AlertDialogDescription>
                            You are currently using{' '}
                            {effectiveSeatUsage.total_used} out of{' '}
                            {effectiveSeatUsage.max_seats} available seats. To
                            invite more users, please upgrade your subscription
                            to add more seats.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogAction
                            onClick={() => setShowSeatLimitAlert(false)}
                        >
                            OK
                        </AlertDialogAction>
                        {config?.enable_billing && (
                            <Button
                                variant="outline"
                                onClick={() => {
                                    setShowSeatLimitAlert(false);
                                    window.location.href = route(
                                        'organizations.billing',
                                        organization.id,
                                    );
                                }}
                            >
                                Manage Billing
                            </Button>
                        )}
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </>
    );
}
