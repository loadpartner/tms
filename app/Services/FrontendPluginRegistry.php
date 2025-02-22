<?php

namespace App\Services;

class FrontendPluginRegistry
{
    protected array $sidebarMenuItems = [];

    public function registerSidebarMenuItem(string $componentName, array $props = []): void
    {
        $this->sidebarMenuItems[] = ['component' => $componentName, 'props' => $props];
    }

    public function getSidebarMenuItems(): array
    {
        return $this->sidebarMenuItems;
    }
} 