import InputError from '@/Components/InputError';
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
import { useToast } from '@/hooks/use-toast';
import { OrganizationInvite } from '@/types/organization';
import { useForm, usePage } from '@inertiajs/react';
import { MoreHorizontal } from 'lucide-react';
import { FormEventHandler, useState } from 'react';

export default function InvitesTable({
    invites,
}: {
    invites: OrganizationInvite[];
}) {
    const organizationId = usePage().props.auth.user.current_organization_id;

    const { toast } = useToast();

    const [open, setOpen] = useState(false);

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
                onError: () => {
                    //setError('email', 'Invalid email address.');
                },
            },
        );
    };

    return (
        <Table>
            <TableCaption>A list of pending organization invites.</TableCaption>
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
                        <Button onClick={() => setOpen(true)}>
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
    );
}
