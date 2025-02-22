<?php

namespace App\Console\Commands;

use App\Models\Plugin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DeactivatePlugin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plugin:deactivate {slug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate a plugin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pluginSlug = $this->argument('slug');
        $plugin = Plugin::where('slug', $pluginSlug)->first();

        if (!$plugin) {
            $this->error("Plugin with slug '{$pluginSlug}' not found.");
            return 1;
        }

        if (!$plugin->is_active) {
            $this->info("Plugin '{$pluginSlug}' is already inactive.");
            return 0;
        }

        $plugin->is_active = false;
        $plugin->save();
        $this->info("Plugin '{$pluginSlug}' deactivated successfully.");

        // Clear config cache in case plugin configuration is involved
        Artisan::call('config:clear');
        $this->info("Configuration cache cleared.");


        return 0;
    }
} 