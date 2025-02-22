<?php

namespace App\Services;

use Illuminate\Foundation\Application;
use App\Providers\PluginServiceProvider; // Assuming your PluginServiceProvider is in this namespace
use Illuminate\Support\Facades\File;

class PluginManager
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function loadPlugins()
    {
        $pluginPath = base_path('plugins'); // Define your plugin directory

        if (File::isDirectory($pluginPath)) {
            $pluginDirectories = File::directories($pluginPath);

            foreach ($pluginDirectories as $dir) {
                $packageName = basename($dir);
                $manifestPath = $dir . '/plugin.json'; // Or plugin.php, etc.

                if (File::exists($manifestPath)) {
                    $manifest = $this->loadManifest($manifestPath);
                    if ($manifest) {
                        $manifest['package_name'] = $packageName; // Ensure package_name is in manifest
                        $this->registerPluginServiceProvider($manifest);
                    }
                }
            }
        }
    }

    protected function loadManifest($manifestPath)
    {
        // Example: Loading from JSON
        try {
            $manifestContent = File::get($manifestPath);
            return json_decode($manifestContent, true);
        } catch (\Exception $e) {
            logger()->error('Error loading plugin manifest: ' . $manifestPath, ['error' => $e->getMessage()]);
            return null;
        }
    }


    protected function registerPluginServiceProvider(array $manifest)
    {
        try {
            $providerClass = $manifest['providers'][0];

            if (class_exists($providerClass)) {
                // Bind using the manifest's slug
                $this->app->instance("plugin.manifest.{$manifest['slug']}", $manifest);
                
                // Then register the provider
                $this->app->register($providerClass);
                
                logger()->info('Plugin Service Provider registered: ' . $providerClass, ['manifest' => $manifest]);
            } else {
                logger()->warning('Plugin Service Provider class not found: ' . $providerClass);
            }

        } catch (\Exception $e) {
            logger()->error('Error registering plugin service provider', ['error' => $e->getMessage(), 'manifest' => $manifest]);
        }
    }
} 