'use client';

import { Check, ChevronRight, Laptop, Moon, Sun } from 'lucide-react';

import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';
import { useTheme } from '@/Components/ui/theme-provider';

export function ThemeToggle() {
    const { setTheme, theme } = useTheme();

    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <button className="flex w-full items-center gap-2 outline-none">
                    <div className="relative">
                        <Sun className="size-4 rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0" />
                        <Moon className="absolute left-0 top-0 size-4 rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100" />
                    </div>
                    Theme
                    <ChevronRight className="ml-auto size-4 text-muted-foreground" />
                </button>
            </DropdownMenuTrigger>
            <DropdownMenuContent
                className="w-[--radix-dropdown-menu-trigger-width] min-w-56 rounded-lg"
                align="start"
                side="right"
                sideOffset={4}
            >
                <DropdownMenuItem onClick={() => setTheme('light')}>
                    <Sun className="mr-2 size-4" />
                    <span className="flex-1">Light</span>
                    {theme === 'light' && <Check className="ml-2 size-4" />}
                </DropdownMenuItem>
                <DropdownMenuItem onClick={() => setTheme('dark')}>
                    <Moon className="mr-2 size-4" />
                    <span className="flex-1">Dark</span>
                    {theme === 'dark' && <Check className="ml-2 size-4" />}
                </DropdownMenuItem>
                <DropdownMenuItem onClick={() => setTheme('system')}>
                    <Laptop className="mr-2 size-4" />
                    <span className="flex-1">System</span>
                    {theme === 'system' && <Check className="ml-2 size-4" />}
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
