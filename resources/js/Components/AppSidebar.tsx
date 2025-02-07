import { Building, GalleryVerticalEnd, Home, Truck, Users } from 'lucide-react';
import * as React from 'react';

import { NavUser } from '@/Components/NavUser';
import { TeamSwitcher } from '@/Components/TeamSwitcher';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarHeader,
    SidebarMenuButton,
    SidebarRail,
} from '@/Components/ui/sidebar';
import { usePage } from '@inertiajs/react';

export function AppSidebar({ ...props }: React.ComponentProps<typeof Sidebar>) {
    const user = usePage().props.auth.user;
    const permissions = usePage().props.auth.permissions;

    // Placeholder for future "user teams"
    const data = {
        teams: [
            {
                name: 'Default Team',
                logo: GalleryVerticalEnd,
                plan: user.organizations.find(
                    (org) => org.id === user.current_organization_id,
                )?.name,
            },
        ],
    };

    const userData = {
        name: user.name,
        email: user.email,
        avatar: '',
    };

    return (
        <Sidebar collapsible="icon" {...props}>
            <SidebarHeader>
                <TeamSwitcher teams={data.teams} />
            </SidebarHeader>
            <SidebarContent>
                <SidebarGroup>
                    <SidebarMenuButton
                        asChild
                        isActive={route().current('dashboard')}
                    >
                        <a href={route('dashboard')}>
                            <Home />
                            <span>Dashboard</span>
                        </a>
                    </SidebarMenuButton>
                    <SidebarMenuButton
                        asChild
                        isActive={route().current('shipments.index')}
                    >
                        <a href={route('shipments.index')}>
                            <Truck />
                            <span>Shipments</span>
                        </a>
                    </SidebarMenuButton>
                    <SidebarMenuButton
                        asChild
                        isActive={route().current('shippers.*')}
                    >
                        <a href={route('shippers.index')}>
                            <Users />
                            <span>Shippers</span>
                        </a>
                    </SidebarMenuButton>
                    {(permissions.ORGANIZATION_MANAGER ||
                        permissions.ORGANIZATION_MANAGE_USERS) && (
                        <SidebarMenuButton
                            asChild
                            isActive={route().current('organizations.show', [
                                user.current_organization_id,
                            ])}
                        >
                            <a
                                href={route('organizations.show', [
                                    user.current_organization_id,
                                ])}
                            >
                                <Building />
                                <span>Organization</span>
                            </a>
                        </SidebarMenuButton>
                    )}
                </SidebarGroup>
            </SidebarContent>
            <SidebarFooter>
                <NavUser user={userData} />
            </SidebarFooter>
            <SidebarRail />
        </Sidebar>
    );
}
