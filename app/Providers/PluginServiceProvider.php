<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{
    /**
     * Plugin manifest data.
     *
     * @var array
     */
    protected $manifest;

    public function __construct($app)
    {
        parent::__construct($app);
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        // Get the manifest using the slug
        $this->manifest = $this->app->get("plugin.manifest.MyPlugin");
        
        // Register plugin specific services here if needed
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../plugins/'.$this->manifest['package_name'].'/routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../../plugins/'.$this->manifest['package_name'].'/database/migrations');
        $this->loadViewsFrom(__DIR__.'/../../plugins/'.$this->manifest['package_name'].'/resources/views', $this->manifest['package_name']);
        // Register plugin assets for frontend if needed (e.g., using Vite)
    }

    protected function isPluginActive(string $pluginSlug): bool
    {
        $plugin = \App\Models\Plugin::where('slug', $pluginSlug)->first();
        return $plugin ? $plugin->is_active : false;
    }
} 