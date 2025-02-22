<?php

namespace App\Console\Commands;

use App\Models\Plugin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ActivatePlugin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plugin:activate {plugin : The slug of the plugin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate a plugin and run its migrations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /** @var \App\Models\Plugin $plugin */
        $plugin = $this->argument('plugin');

        $pluginSlug = $plugin->slug;

        if ($plugin->is_active) {
            $this->info("Plugin '{$pluginSlug}' is already active.");
            return 0;
        }

        $pluginPath = base_path("plugins/{$pluginSlug}");
        $manifestPath = $pluginPath . '/plugin.json';

        if (!file_exists($manifestPath)) {
            $this->error("Plugin manifest not found for '{$pluginSlug}'.");
            return 1;
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);

        if (isset($manifest['migrations'])) {
            $migrationsPath = $pluginPath . '/' . $manifest['migrations'];
            if (is_dir($migrationsPath)) {
                $this->info("Running migrations for plugin '{$pluginSlug}'...");
                // We need to specify the path to plugin's migrations
                Artisan::call('migrate', [
                    '--path' => $migrationsPath,
                    '--force' => true, // Add --force to run in production if needed
                ]);
                $this->info(Artisan::output()); // Display migration output
            } else {
                $this->info("No migrations found for plugin '{$pluginSlug}'.");
            }
        }

        $plugin->is_active = true;
        $plugin->save();
        $this->info("Plugin '{$pluginSlug}' activated successfully.");

        // Clear config cache in case plugin configuration is involved
        Artisan::call('config:clear');
        $this->info("Configuration cache cleared.");

        return 0;
    }
} 