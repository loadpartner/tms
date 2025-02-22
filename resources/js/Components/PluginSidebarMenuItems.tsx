import React from 'react';

interface PluginSidebarMenuItemsProps {
    menuItems: any[]; // Define a more specific type if you know the structure of menu items
}

const PluginSidebarMenuItems: React.FC<PluginSidebarMenuItemsProps> = ({ menuItems }) => {
    return (
        <>
            {menuItems && menuItems.map((item, index) => (
                <li key={index}>
                    <a href={item.href} className="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600">
                        {item.label}
                    </a>
                </li>
            ))}
        </>
    );
};

export { PluginSidebarMenuItems }; 