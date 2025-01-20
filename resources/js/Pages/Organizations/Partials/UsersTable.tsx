import { Button } from '@/Components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import { toast } from '@/hooks/use-toast';
import { User } from '@/types';
import { Organization } from '@/types/organization';
import { useForm, usePage } from '@inertiajs/react';
import { DropdownMenuSeparator } from '@radix-ui/react-dropdown-menu';
import { Crown, MoreHorizontal } from 'lucide-react';

export default function UsersTable({
    users,
    organization,
}: {
    users: User[];
    organization: Organization;
}) {
    const { delete: destroy, post } = useForm();

    const userPermissions = usePage().props.auth.permissions;

    const canManageOrganization = userPermissions.ORGANIZATION_MANAGER;

    return (
        <Table>
            <TableCaption>A list of organization users.</TableCaption>
            <TableHeader>
                <TableRow>
                    <TableHead>Name</TableHead>
                    <TableHead>Email</TableHead>
                    <TableHead className="text-right">Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                {users.map((user) => (
                    <TableRow key={user.id}>
                        <TableCell>
                            {user.id == organization.owner_id && (
                                <Crown className="inline h-4 text-yellow-600" />
                            )}{' '}
                            {user.name}
                        </TableCell>
                        <TableCell>{user.email}</TableCell>

                        <TableCell className="text-right">
                            {canManageOrganization && (
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
                                            disabled={
                                                user.id ===
                                                organization.owner_id
                                            }
                                            onClick={() =>
                                                destroy(
                                                    route(
                                                        'organizations.users.destroy',
                                                        {
                                                            organization:
                                                                organization.id,
                                                            user: user.id,
                                                        },
                                                    ),
                                                    {
                                                        onSuccess: () => {
                                                            toast({
                                                                description:
                                                                    'User removed',
                                                            });
                                                        },
                                                    },
                                                )
                                            }
                                        >
                                            Remove User
                                        </DropdownMenuItem>
                                        <DropdownMenuLabel></DropdownMenuLabel>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem
                                            disabled={
                                                user.id ===
                                                organization.owner_id
                                            }
                                            onClick={() =>
                                                post(
                                                    route(
                                                        'organizations.users.transfer',
                                                        {
                                                            organization:
                                                                organization.id,
                                                            user: user.id,
                                                        },
                                                    ),
                                                    {
                                                        onSuccess: () => {
                                                            toast({
                                                                description:
                                                                    'Organization ownership transferred',
                                                            });
                                                        },
                                                    },
                                                )
                                            }
                                        >
                                            Transfer Ownership
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            )}
                        </TableCell>
                    </TableRow>
                ))}
            </TableBody>
        </Table>
    );
}
