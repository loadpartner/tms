'use client';

import { BadgeCheck, Building, ChevronRight, LogOut } from 'lucide-react';

import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';
import {
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from '@/Components/ui/sidebar';
import { ThemeToggle } from '@/Components/ui/theme-toggle';
import { Organization } from '@/types/organization';
import { router, usePage } from '@inertiajs/react';

export function NavUser({
    user,
}: {
    user: {
        name: string;
        email: string;
        avatar: string;
    };
}) {
    const { isMobile } = useSidebar();

    const pageUser = usePage().props.auth.user;
    const userOrganizations = pageUser.organizations;
    const currentOrgId = pageUser.current_organization_id;

    function setActiveOrganization(organization: Organization) {
        router.post(route('organizations.switch', organization.id), undefined, {
            replace: true,
            preserveState: false,
            preserveScroll: false,
            onSuccess: () => {
                window.location.reload();
            },
        });
    }

    return (
        <SidebarMenu>
            <SidebarMenuItem>
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <SidebarMenuButton
                            size="lg"
                            className="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                        >
                            <Avatar className="h-8 w-8 rounded-lg">
                                <AvatarImage
                                    src={user.avatar}
                                    alt={user.name}
                                />
                                <AvatarFallback className="rounded-lg">
                                    {user.name
                                        .split(' ')
                                        .map((n) => n[0].toUpperCase())
                                        .join('')}
                                </AvatarFallback>
                            </Avatar>
                            <div className="grid flex-1 text-left text-sm leading-tight">
                                <span className="truncate font-semibold">
                                    {user.name}
                                </span>
                                <span className="truncate text-xs">
                                    {user.email}
                                </span>
                            </div>
                            <ChevronRight className="ml-auto size-4" />
                        </SidebarMenuButton>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent
                        className="w-[--radix-dropdown-menu-trigger-width] min-w-56 rounded-lg"
                        side={isMobile ? 'bottom' : 'right'}
                        align="end"
                        sideOffset={4}
                    >
                        <DropdownMenuGroup>
                            <DropdownMenuItem>
                                <DropdownMenu>
                                    <DropdownMenuTrigger asChild>
                                        <button className="flex w-full items-center gap-2 outline-none">
                                            <Building className="size-4" />
                                            Organizations
                                            <ChevronRight className="ml-auto size-4 text-muted-foreground" />
                                        </button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent
                                        className="w-[--radix-dropdown-menu-trigger-width] min-w-56 rounded-lg"
                                        align="start"
                                        side={isMobile ? 'bottom' : 'right'}
                                        sideOffset={4}
                                    >
                                        <DropdownMenuLabel className="text-xs text-muted-foreground">
                                            Organizations
                                        </DropdownMenuLabel>
                                        {userOrganizations.map((org) => (
                                            <DropdownMenuItem
                                                key={org.name}
                                                onClick={() =>
                                                    setActiveOrganization(org)
                                                }
                                                className="gap-2 p-2"
                                                disabled={
                                                    org.id === currentOrgId
                                                }
                                            >
                                                {org.name}
                                                {org.id === currentOrgId && (
                                                    <BadgeCheck className="ml-auto h-4 w-4 text-primary" />
                                                )}
                                            </DropdownMenuItem>
                                        ))}
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </DropdownMenuItem>
                            <DropdownMenuItem
                                onSelect={() => {
                                    router.visit(route('profile.edit'));
                                }}
                            >
                                <BadgeCheck className="size-4" />
                                Account
                            </DropdownMenuItem>
                            <DropdownMenuItem
                                onSelect={(e) => {
                                    e.preventDefault();
                                    e.stopPropagation();
                                }}
                            >
                                <ThemeToggle />
                            </DropdownMenuItem>
                        </DropdownMenuGroup>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem
                            onSelect={() => {
                                router.visit(route('logout'), {
                                    method: 'post',
                                });
                            }}
                        >
                            <LogOut className="size-4" />
                            Log out
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </SidebarMenuItem>
        </SidebarMenu>
    );
}
