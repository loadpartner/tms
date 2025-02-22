<?php

use App\Actions\Carriers\CreateCarrier;
use App\Actions\Carriers\CreateCarrierFromSaferReport;
use App\Actions\Carriers\FmcsaDOTLookup;
use App\Actions\Carriers\FmcsaNameLookup;
use App\Actions\Carriers\UpdateCarrierGeneral;
use App\Actions\Contacts\CreateContact;
use App\Actions\Contacts\DeleteContact;
use App\Actions\Contacts\UpdateContact;
use App\Actions\Customers\CreateCustomer;
use App\Actions\Customers\CreateCustomerFacility;
use App\Actions\Customers\DeleteCustomerFacility;
use App\Actions\Customers\UpdateCustomer;
use App\Actions\Facilities\CreateFacility;
use App\Actions\Locations\CreateLocation;
use App\Actions\Notes\CreateNote;
use App\Actions\Notes\GetNotes;
use App\Actions\Shipments\CreateShipment;
use App\Actions\Shipments\UpdateShipmentCarrierDetails;
use App\Actions\Shipments\UpdateShipmentGeneral;
use App\Actions\Shipments\UpdateShipmentNumber;
use App\Actions\Shipments\UpdateShipmentCustomers;
use App\Actions\Shipments\UpdateShipmentStops;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrganizationInviteController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Shipments\ShipmentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Admin\PluginController;
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

    Route::get('facilities/search', [FacilityController::class, 'search'])->name('facilities.search');
    Route::post('facilities', CreateFacility::class)->name('facilities.store');
    Route::get('facilities', [FacilityController::class, 'index'])->name('facilities.index');

    Route::get('carriers/search', [CarrierController::class, 'search'])->name('carriers.search');
    Route::resource('carriers', CarrierController::class, [
        'except' => ['store', 'update'],
    ]);
    Route::put('carriers/{carrier}', UpdateCarrierGeneral::class)->name('carriers.update');
    Route::post('carriers', CreateCarrier::class)->name('carriers.store');

    Route::get('carriers/fmcsa/name', FmcsaNameLookup::class)->name('carriers.fmcsa.lookup.name');
    Route::post('carriers/fmcsa/{carrierSaferReport}/create', CreateCarrierFromSaferReport::class)->name('carriers.fmcsa.store');
    Route::get('carriers/fmcsa/dot', FmcsaDOTLookup::class)->name('carriers.fmcsa.lookup.dot');

    Route::get('customers/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::resource('customers', CustomerController::class, [
        'except' => ['store', 'update'],
    ]);
    Route::post('customers', CreateCustomer::class)->name('customers.store');
    Route::put('customers/{customer}', UpdateCustomer::class)->name('customers.update');
    Route::post('customers/{customer}/facilities', CreateCustomerFacility::class)->name('customers.facilities.store');
    Route::get('customers/{customer}/facilities', [CustomerController::class, 'facilities'])->name('customers.facilities.index');
    Route::delete('customers/{customer}/facilities/{facility}', DeleteCustomerFacility::class)->name('customers.facilities.destroy');

    Route::get('shipments/search', [ShipmentController::class, 'search'])->name('shipments.search');
    Route::resource('shipments', ShipmentController::class, [
        'except' => ['store'],
    ]);
    Route::post('shipments', CreateShipment::class)->name('shipments.store');
    Route::patch('shipments/{shipment}/shipment-number', UpdateShipmentNumber::class)->name('shipments.updateShipmentNumber');
    Route::patch('shipments/{shipment}/general', UpdateShipmentGeneral::class)->name('shipments.updateGeneral');
    Route::patch('shipments/{shipment}/carrier-details', UpdateShipmentCarrierDetails::class)->name('shipments.updateCarrierDetails');
    Route::patch('shipments/{shipment}/customers', UpdateShipmentCustomers::class)->name('shipments.updateCustomers');
    Route::patch('shipments/{shipment}/stops', UpdateShipmentStops::class)->name('shipments.updateStops');

    Route::delete('notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
    Route::get('notes/{notableType}/{notableId}', GetNotes::class)->name('notes.index');
    Route::post('notes/{notableType}/{notableId}', CreateNote::class)->name('notes.store');

    Route::get('locations/search', [LocationController::class, 'search'])->name('locations.search');
    Route::post('locations', CreateLocation::class)->name('locations.store');

    Route::get('contacts/search', [ContactController::class, 'search'])->name('contacts.search');
    Route::post('contacts', CreateContact::class)->name('contacts.store');
    Route::put('contacts/{contact}', UpdateContact::class)->name('contacts.update');
    Route::delete('contacts/{contact}', DeleteContact::class)->name('contacts.destroy');
});

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/plugins', [PluginController::class, 'index'])->name('plugins.index');
    Route::post('/plugins', [PluginController::class, 'install'])->name('plugins.install');
    Route::post('/plugins/{plugin}/activate', [PluginController::class, 'activate'])->name('plugins.activate');
    Route::post('/plugins/{plugin}/deactivate', [PluginController::class, 'deactivate'])->name('plugins.deactivate');
});

require __DIR__ . '/auth.php';
