<?php

use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrganizationInviteController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Shipments\ShipmentController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('organizations', OrganizationController::class);

    Route::post('organizations/{organization}/invites/{invite:code}/accept', [OrganizationInviteController::class, 'accept'])->name('organizations.invites.accept');
    Route::resource('organizations.invites', OrganizationInviteController::class)->scoped([
        'invite' => 'code',
    ])->only(['show']);
});


Route::middleware(['auth', 'verified', 'organization-assigned'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('organizations.invites', OrganizationInviteController::class)->scoped([
        'invite' => 'code',
    ])->except(['show']);

    Route::delete('organizations/{organization}/users/{user}', [OrganizationController::class, 'removeUser'])->name('organizations.users.destroy');
    Route::post('organizations/{organization}/users/{user}/transfer', [OrganizationController::class, 'transferOwnership'])->name('organizations.users.transfer');

    Route::post('organizations/{organization}/invites/{invite:code}/resend', [OrganizationInviteController::class, 'resend'])->name('organizations.invites.resend');

    Route::post('organizations/{organization}/switch', [OrganizationController::class, 'switchOrganization'])->name('organizations.switch');


    Route::prefix('organizations/{organization}/permissions')->group(function () {
        Route::post('role', [PermissionController::class, 'storeRole'])->name('organizations.permissions.role.store');
        Route::delete('role/{role}', [PermissionController::class, 'destroyRole'])->name('organizations.permissions.role.destroy');
        Route::patch('role/{role}', [PermissionController::class, 'updateRole'])->name('organizations.permissions.role.update');
    });

    Route::resource('shipments', ShipmentController::class);
});

require __DIR__ . '/auth.php';
