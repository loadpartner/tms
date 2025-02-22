<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'organization-assigned'])->group(function () {
    Route::get('/plugin-route', function () {
        return Inertia::render('MyPlugin::PluginPage'); // Assuming you have a page in resources/js/Pages/PluginPage.tsx in your plugin
    })->name('plugin.route');
}); 