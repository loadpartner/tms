import { Building, Dot, GalleryVerticalEnd, Home, Truck } from 'lucide-react';
import * as React from 'react';

import { NavUser } from '@/Components/nav-user';
import { TeamSwitcher } from '@/Components/team-switcher';
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
        avatar: '/avatars/shadcn.jpg',
    };

    return (
        <Sidebar collapsible="icon" {...props}>
            <SidebarHeader>
                <TeamSwitcher teams={data.teams} />
            </SidebarHeader>
            <SidebarContent>
                <SidebarGroup>
                    <SidebarMenuButton asChild>
                        <a href={route('dashboard')}>
                            <Home />
                            <span>Dashboard</span>
                            {route().current('dashboard') && <Dot />}
                        </a>
                    </SidebarMenuButton>
                    <SidebarMenuButton asChild>
                        <a href={route('shipments.index')}>
                            <Truck />
                            <span>Shipments</span>
                            {route().current('shipments.index') && <Dot />}
                        </a>
                    </SidebarMenuButton>
                    {(permissions.ORGANIZATION_MANAGER ||
                        permissions.ORGANIZATION_MANAGE_USERS) && (
                        <SidebarMenuButton asChild>
                            <a
                                href={route('organizations.show', [
                                    user.current_organization_id,
                                ])}
                            >
                                <Building />
                                <span>Organization</span>
                                {route().current('organizations.show', [
                                    user.current_organization_id,
                                ]) && <Dot />}
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
