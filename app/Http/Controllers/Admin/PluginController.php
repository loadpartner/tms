<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plugin;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use ZipArchive;

class PluginController extends Controller
{
    public function index(): Response
    {
        $plugins = Plugin::all()->map(function ($plugin) {
            $manifestPath = base_path("plugins/{$plugin->slug}/plugin.json");
            $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : [];
            return [
                'id' => $plugin->id,
                'slug' => $plugin->slug,
                'is_active' => $plugin->is_active,
                'name' => $manifest['name'] ?? $plugin->slug, // Default to slug if name not in manifest
                'description' => $manifest['description'] ?? 'No description',
                'version' => $manifest['version'] ?? 'N/A',
                'author' => $manifest['author'] ?? 'Unknown',
            ];
        });

        return Inertia::render('Admin/Plugins/Index', [
            'plugins' => $plugins,
        ]);
    }

    public function activate(string $slug)
    {
        /** @var \App\Models\Plugin $plugin */
        $plugin = Plugin::where('slug', $slug)->firstOrFail();

        Artisan::call('plugin:activate', ['plugin' => $plugin]);
        return back()->with('success', "Plugin '{$plugin->name}' activated.");
    }

    public function deactivate(string $slug)
    {
        /** @var \App\Models\Plugin $plugin */
        $plugin = Plugin::where('slug', $slug)->firstOrFail();

        Artisan::call('plugin:deactivate', ['slug' => $plugin->slug]);
        return back()->with('success', "Plugin '{$plugin->name}' deactivated.");
    }

    public function install(Request $request)
    {
        $request->validate([
            'plugin_zip' => 'required|file|mimes:zip',
        ]);

        $file = $request->file('plugin_zip');
        $filename = $file->getClientOriginalName();
        $tempPath = storage_path('app/temp_plugins'); // Temporary directory to store uploaded zip
        $pluginSlug = ''; // Will be extracted from manifest

        if (!File::isDirectory($tempPath)) {
            File::makeDirectory($tempPath, 0755, true); // Create temp directory if not exists
        }

        $file->move($tempPath, $filename);
        $zipFilePath = "{$tempPath}/{$filename}";
        $extractPath = base_path('plugins');


        $zip = new ZipArchive;
        if ($zip->open($zipFilePath) === TRUE) {
            // Extract to a temporary directory first to validate manifest before placing in plugins dir
            $tempExtractPath = "{$tempPath}/" . pathinfo($filename, PATHINFO_FILENAME);
            if (!File::isDirectory($tempExtractPath)) {
                File::makeDirectory($tempExtractPath, 0755, true);
            }
            $zip->extractTo($tempExtractPath);
            $zip->close();

            // Validate manifest
            $manifestPath = "{$tempExtractPath}/plugin.json";
            if (!file_exists($manifestPath)) {
                File::deleteDirectory($tempExtractPath); // Clean up temp directory
                File::delete($zipFilePath);
                return back()->withErrors(['plugin_zip' => 'Plugin manifest (plugin.json) not found in the zip file.']);
            }

            $manifest = json_decode(file_get_contents($manifestPath), true);
            if (!$manifest || !isset($manifest['slug'])) {
                File::deleteDirectory($tempExtractPath);
                File::delete($zipFilePath);
                return back()->withErrors(['plugin_zip' => 'Invalid plugin manifest (plugin.json). Slug is missing or invalid JSON.']);
            }
            $pluginSlug = $manifest['slug'];

            // Check if plugin with same slug already exists
            if (Plugin::where('slug', $pluginSlug)->exists()) {
                File::deleteDirectory($tempExtractPath);
                File::delete($zipFilePath);
                return back()->withErrors(['plugin_zip' => "Plugin with slug '{$pluginSlug}' already exists."]);
            }


            // Move plugin directory to plugins path
            $pluginTargetPath = "{$extractPath}/{$pluginSlug}";
            if (File::isDirectory($pluginTargetPath)) {
                 File::deleteDirectory($pluginTargetPath); // Ensure no existing directory
            }


            File::moveDirectory($tempExtractPath, $pluginTargetPath);


            // Create plugin database record (initially inactive)
            Plugin::create(['slug' => $pluginSlug, 'is_active' => false]);


            // Clean up temp files
            File::deleteDirectory($tempExtractPath);
            File::delete($zipFilePath);


            return back()->with('success', "Plugin '".($manifest['name'] ?? $pluginSlug)."' installed successfully. It is inactive by default. Activate it from the plugin list.");

        } else {
            File::delete($zipFilePath);
            return back()->withErrors(['plugin_zip' => 'Could not open zip file.']);
        }
    }
} 