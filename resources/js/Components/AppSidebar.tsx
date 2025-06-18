import {
    Building,
    ChevronRight,
    GalleryVerticalEnd,
    Home,
    Package,
    Truck,
    Users,
    Warehouse,
} from 'lucide-react';
import * as React from 'react';
import { useEffect, useState } from 'react';

import { NavUser } from '@/Components/NavUser';
import { TeamSwitcher } from '@/Components/TeamSwitcher';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarHeader,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
    SidebarRail,
} from '@/Components/ui/sidebar';
import { usePage } from '@inertiajs/react';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from './ui/collapsible';

export function AppSidebar({ ...props }: React.ComponentProps<typeof Sidebar>) {
    const user = usePage().props.auth.user;
    const permissions = usePage().props.auth.permissions;
    const config = usePage().props.config;
    const [isOrgMenuOpen, setIsOrgMenuOpen] = useState(false);

    useEffect(() => {
        const savedState = localStorage.getItem('orgMenuOpen');
        if (savedState !== null) {
            setIsOrgMenuOpen(savedState === 'true');
        }
    }, []);

    const handleOrgMenuChange = (open: boolean) => {
        setIsOrgMenuOpen(open);
        localStorage.setItem('orgMenuOpen', open.toString());
    };

    // Early return if user is not loaded yet
    if (!user) {
        return null;
    }

    // Placeholder for future "user teams"
    const data = {
        teams: [
            {
                name: 'Default Team',
                logo: GalleryVerticalEnd,
                plan: user.organizations?.find(
                    (org) => org.id === user.current_organization_id,
                )?.name,
            },
        ],
    };

    const userData = {
        name: user.name,
        email: user.email,
        avatar: user.profile_photo_url || '',
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
                            <Package />
                            <span>Shipments</span>
                        </a>
                    </SidebarMenuButton>
                    <SidebarMenuButton
                        asChild
                        isActive={route().current('carriers.index')}
                    >
                        <a href={route('carriers.index')}>
                            <Truck />
                            <span>Carriers</span>
                        </a>
                    </SidebarMenuButton>
                    <SidebarMenuButton
                        asChild
                        isActive={route().current('customers.index')}
                    >
                        <a href={route('customers.index')}>
                            <Users />
                            <span>Customers</span>
                        </a>
                    </SidebarMenuButton>
                    <SidebarMenuButton
                        asChild
                        isActive={route().current('facilities.index')}
                    >
                        <a href={route('facilities.index')}>
                            <Warehouse />
                            <span>Facilities</span>
                        </a>
                    </SidebarMenuButton>

                    {(permissions?.ORGANIZATION_MANAGER ||
                        permissions?.ORGANIZATION_MANAGE_USERS) && (
                        <Collapsible
                            open={isOrgMenuOpen}
                            onOpenChange={handleOrgMenuChange}
                            asChild
                            className="group/collapsible"
                        >
                            <SidebarMenuItem className="list-none">
                                <CollapsibleTrigger asChild>
                                    <SidebarMenuButton>
                                        <Building />
                                        <span>Organization</span>
                                        <ChevronRight className="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                                    </SidebarMenuButton>
                                </CollapsibleTrigger>
                                <CollapsibleContent>
                                    <SidebarMenuSub>
                                        <SidebarMenuSubItem>
                                            <SidebarMenuSubButton
                                                isActive={route().current(
                                                    'organizations.users',
                                                    [
                                                        user.current_organization_id,
                                                    ],
                                                )}
                                                href={route(
                                                    'organizations.users',
                                                    [
                                                        user.current_organization_id,
                                                    ],
                                                )}
                                            >
                                                Users
                                            </SidebarMenuSubButton>
                                        </SidebarMenuSubItem>
                                        <SidebarMenuSubItem>
                                            <SidebarMenuSubButton
                                                href={route(
                                                    'organizations.roles',
                                                    [
                                                        user.current_organization_id,
                                                    ],
                                                )}
                                                isActive={route().current(
                                                    'organizations.roles',
                                                    [
                                                        user.current_organization_id,
                                                    ],
                                                )}
                                            >
                                                Roles
                                            </SidebarMenuSubButton>
                                        </SidebarMenuSubItem>
                                        {permissions?.INTEGRATION_SETTINGS_EDIT && (
                                            <SidebarMenuSubItem>
                                                <SidebarMenuSubButton
                                                    href={route(
                                                        'organizations.integration-settings',
                                                        [
                                                            user.current_organization_id,
                                                        ],
                                                    )}
                                                    isActive={route().current(
                                                        'organizations.integration-settings',
                                                        [
                                                            user.current_organization_id,
                                                        ],
                                                    )}
                                                >
                                                    Integration Settings
                                                </SidebarMenuSubButton>
                                            </SidebarMenuSubItem>
                                        )}
                                        {permissions?.ORGANIZATION_MANAGER && (
                                            <SidebarMenuSubItem>
                                                <SidebarMenuSubButton
                                                    href={route(
                                                        'organizations.document-templates-page',
                                                        [
                                                            user.current_organization_id,
                                                        ],
                                                    )}
                                                    isActive={route().current(
                                                        'organizations.document-templates-page',
                                                        [
                                                            user.current_organization_id,
                                                        ],
                                                    )}
                                                >
                                                    Document Templates
                                                </SidebarMenuSubButton>
                                            </SidebarMenuSubItem>
                                        )}
                                        {permissions?.ORGANIZATION_MANAGER && (
                                            <SidebarMenuSubItem>
                                                <SidebarMenuSubButton
                                                    href={route(
                                                        'organizations.settings',
                                                        [
                                                            user.current_organization_id,
                                                        ],
                                                    )}
                                                    isActive={route().current(
                                                        'organizations.settings',
                                                        [
                                                            user.current_organization_id,
                                                        ],
                                                    )}
                                                >
                                                    Settings
                                                </SidebarMenuSubButton>
                                            </SidebarMenuSubItem>
                                        )}
                                        {permissions?.ORGANIZATION_BILLING &&
                                            config?.enable_billing && (
                                                <SidebarMenuSubItem>
                                                    <SidebarMenuSubButton
                                                        href={route(
                                                            'organizations.billing',
                                                            [
                                                                user.current_organization_id,
                                                            ],
                                                        )}
                                                        isActive={route().current(
                                                            'organizations.billing',
                                                            [
                                                                user.current_organization_id,
                                                            ],
                                                        )}
                                                    >
                                                        Billing
                                                    </SidebarMenuSubButton>
                                                </SidebarMenuSubItem>
                                            )}
                                    </SidebarMenuSub>
                                </CollapsibleContent>
                            </SidebarMenuItem>
                        </Collapsible>
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
