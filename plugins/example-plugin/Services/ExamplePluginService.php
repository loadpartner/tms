<?php

namespace Plugins\ExamplePlugin\Services;

class ExamplePluginService
{
    public function getPluginName(): string
    {
        return config('example-plugin.name', 'Example Plugin (Default)'); // Example config value
    }

    public function getPluginVersion(): string
    {
        return '1.0.0'; // Hardcoded version or from plugin.json
    }

    // ... other plugin API methods ...
} 