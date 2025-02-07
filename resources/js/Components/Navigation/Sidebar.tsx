import { LibraryIcon } from '@heroicons/react/24/outline';

export default function Sidebar() {
    const navigation = [
        { name: 'Shippers', href: route('shippers.index'), icon: LibraryIcon, current: route().current('shippers.*') },
    ];
} 