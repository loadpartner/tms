<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plugin;

class PluginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pluginsPath = base_path('plugins');
        $pluginFolders = scandir($pluginsPath);

        foreach ($pluginFolders as $pluginFolder) {
            if ($pluginFolder === '.' || $pluginFolder === '..') {
                continue;
            }

            $pluginPath = $pluginsPath . '/' . $pluginFolder;

            if (is_dir($pluginPath)) {
                $manifestPath = $pluginPath . '/plugin.json';

                if (file_exists($manifestPath)) {
                    $manifest = json_decode(file_get_contents($manifestPath), true);
                    if ($manifest && isset($manifest['slug'])) {
                        Plugin::firstOrCreate(['slug' => $manifest['slug']]);
                    }
                }
            }
        }
    }
} 