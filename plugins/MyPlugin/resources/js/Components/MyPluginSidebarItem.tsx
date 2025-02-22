import { SidebarMenuButton } from '@/Components/ui/sidebar';
import { Package } from 'lucide-react';
import React from 'react';

const MyPluginSidebarItem: React.FC = () => {
    return (
        <SidebarMenuButton asChild isActive={route().current('plugin.route')}>
            <a href={route('plugin.route')}>
                <Package />
                <span>My Plugin Item</span>
            </a>
        </SidebarMenuButton>
    );
};

export default MyPluginSidebarItem; 