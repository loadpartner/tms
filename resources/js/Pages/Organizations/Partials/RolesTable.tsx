import PrimaryButton from '@/Components/PrimaryButton';
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
import { Organization, Permission, Role } from '@/types/organization';
import { useForm } from '@inertiajs/react';
import { MoreHorizontal } from 'lucide-react';

import InputError from '@/Components/InputError';
import { Checkbox } from '@/Components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { toast, useToast } from '@/hooks/UseToast';
import { CheckedState } from '@radix-ui/react-checkbox';
import { FormEventHandler, useEffect, useState } from 'react';

export default function RolesTable({
    roles,
    organization,
    permissions,
}: {
    roles: Role[];
    organization: Organization;
    permissions: Permission[];
}) {
    const { delete: destroy } = useForm();
    const { toast } = useToast();
    const [open, setOpen] = useState(false);

    const [activeRole, setActiveRole] = useState<Role | null>(null);

    return (
        <>
            <Button
                onClick={() => {
                    setActiveRole(null);
                    setOpen(true);
                }}
            >
                New Role
            </Button>
            <Table>
                <TableCaption>A list of organization roles.</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead>Name</TableHead>
                        <TableHead className="hidden md:table-cell">
                            Users
                        </TableHead>
                        <TableHead className="hidden md:table-cell">
                            Permissions
                        </TableHead>
                        <TableHead className="text-right">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {roles.map((role) => (
                        <TableRow key={role.id}>
                            <TableCell>{role.name}</TableCell>

                            <TableCell className="hidden md:table-cell">
                                {role.users?.map((user) => (
                                    <div key={user.id}>{user.name}</div>
                                ))}
                            </TableCell>

                            <TableCell className="hidden md:table-cell">
                                {role.permissions?.map((permission) => (
                                    <div key={permission.id}>
                                        {permission.name}
                                    </div>
                                ))}
                            </TableCell>

                            <TableCell className="flex justify-end space-x-4">
                                <PrimaryButton
                                    onClick={() => {
                                        setActiveRole(role);
                                        setOpen(true);
                                    }}
                                >
                                    Edit
                                </PrimaryButton>
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
                                            onClick={() => {
                                                destroy(
                                                    route(
                                                        'organizations.permissions.role.destroy',
                                                        {
                                                            organization:
                                                                organization.id,
                                                            role: role.id,
                                                        },
                                                    ),
                                                    {
                                                        onSuccess: () => {
                                                            toast({
                                                                description:
                                                                    'Role deleted',
                                                            });
                                                        },
                                                    },
                                                );
                                            }}
                                        >
                                            Delete Role
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
            <RoleForm
                open={open}
                setOpen={setOpen}
                organization={organization}
                role={activeRole}
                permissions={permissions}
            />
        </>
    );
}

function RoleForm({
    role = null,
    organization,
    open,
    setOpen,
    permissions,
}: {
    role?: Role | null;
    organization: Organization;
    open: boolean;
    setOpen: React.Dispatch<React.SetStateAction<boolean>>;
    permissions: Permission[];
}) {
    const { data, setData, post, patch, processing, errors, reset } = useForm({
        name: role?.name || '',
        permissions: role?.permissions.map((permission) => permission.id) || [],
        users: role?.users.map((user) => user.id) || [],
    });

    useEffect(() => {
        setData({
            name: role?.name || '',
            permissions:
                role?.permissions.map((permission) => permission.id) || [],
            users: role?.users.map((user) => user.id) || [],
        });

        // Unsure how to force form to refresh without this useEffect,
        // but it requires a deps array of setData which is beign set therefore infinite loop
        // only real solution i can find is to disable eslint here
        //
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [role]);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (role) {
            patch(
                route('organizations.permissions.role.update', {
                    organization: organization.id,
                    role: role.id,
                }),
                {
                    onSuccess: () => {
                        reset();
                        setOpen(false);
                        toast({
                            description: 'Role updated!',
                        });
                    },
                },
            );
        } else {
            post(
                route('organizations.permissions.role.store', {
                    organization: organization.id,
                }),
                {
                    onSuccess: () => {
                        reset();
                        setOpen(false);
                        toast({
                            description: 'Role created!',
                        });
                    },
                },
            );
        }
    };

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogContent>
                <form onSubmit={submit}>
                    <DialogHeader>
                        <DialogTitle className="mb-4">
                            {role ? 'Edit' : 'Create'} Role
                        </DialogTitle>
                        <div className="flex flex-col gap-6">
                            <div className="grid gap-2">
                                <Label htmlFor="name">Name</Label>
                                <InputError message={errors.name} />
                                <Input
                                    id="name"
                                    type="text"
                                    placeholder="Admin"
                                    required
                                    value={data.name}
                                    onChange={(e) =>
                                        setData('name', e.target.value)
                                    }
                                    tabIndex={1}
                                    autoComplete="off"
                                />
                                <InputError
                                    message={errors.name}
                                    className="mt-2"
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="permissions">Permissions</Label>
                                <InputError message={errors.permissions} />
                                <div className="grid gap-2">
                                    <div className="mb-2 flex space-x-2 border-b pb-2">
                                        <Checkbox
                                            id="select-all-permissions"
                                            checked={
                                                data.permissions.length ===
                                                permissions.length
                                            }
                                            onCheckedChange={(checked) => {
                                                if (checked) {
                                                    setData(
                                                        'permissions',
                                                        permissions.map(
                                                            (p) => p.id,
                                                        ),
                                                    );
                                                } else {
                                                    setData('permissions', []);
                                                }
                                            }}
                                        />
                                        <Label
                                            htmlFor="select-all-permissions"
                                            className="cursor-pointer font-bold"
                                        >
                                            Select All Permissions
                                        </Label>
                                    </div>
                                    {permissions.map((permission) => (
                                        <div
                                            key={permission.id}
                                            className="flex space-x-2"
                                        >
                                            <Checkbox
                                                name={`permissions[${permission.id}]`}
                                                id={`permissions[${permission.id}]`}
                                                checked={data?.permissions?.includes(
                                                    permission.id,
                                                )}
                                                onCheckedChange={(
                                                    checked: CheckedState,
                                                ) => {
                                                    if (checked) {
                                                        setData('permissions', [
                                                            ...data.permissions,
                                                            permission.id,
                                                        ]);
                                                    } else {
                                                        setData(
                                                            'permissions',
                                                            data.permissions.filter(
                                                                (id) =>
                                                                    id !==
                                                                    permission.id,
                                                            ),
                                                        );
                                                    }
                                                }}
                                            />
                                            <Label
                                                htmlFor={`permissions[${permission.id}]`}
                                                className="cursor-pointer"
                                            >
                                                {permission.name}
                                            </Label>
                                        </div>
                                    ))}
                                </div>
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="users">Users</Label>
                                <InputError message={errors.users} />
                                <div className="grid gap-2">
                                    {organization.users
                                        .sort((a, b) =>
                                            a.name.localeCompare(b.name),
                                        )
                                        .sort((a, b) => {
                                            // if user is in the data.users list
                                            // move them to the top
                                            if (data.users.includes(a.id)) {
                                                return -1;
                                            }
                                            if (data.users.includes(b.id)) {
                                                return 1;
                                            }
                                            return 0;
                                        })
                                        .map((user) => (
                                            <div
                                                key={user.id}
                                                className="flex space-x-2"
                                            >
                                                <Checkbox
                                                    name={`users[${user.id}]`}
                                                    id={`users[${user.id}]`}
                                                    checked={data?.users?.includes(
                                                        user.id,
                                                    )}
                                                    onCheckedChange={(
                                                        checked: CheckedState,
                                                    ) => {
                                                        if (checked) {
                                                            setData('users', [
                                                                ...data.users,
                                                                user.id,
                                                            ]);
                                                        } else {
                                                            setData(
                                                                'users',
                                                                data.users.filter(
                                                                    (id) =>
                                                                        id !==
                                                                        user.id,
                                                                ),
                                                            );
                                                        }
                                                    }}
                                                />
                                                <Label
                                                    htmlFor={`users[${user.id}]`}
                                                    className="cursor-pointer"
                                                >
                                                    {user.name} - {user.email}
                                                </Label>
                                            </div>
                                        ))}
                                </div>
                            </div>
                        </div>
                    </DialogHeader>
                    <DialogFooter>
                        <Button type="submit" disabled={processing}>
                            {role ? 'Update' : 'Create'}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
