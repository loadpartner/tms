<?php

use App\Actions\Accounting\GetRateTypes;
use App\Actions\Accounting\SavePayables;
use App\Actions\Accounting\SaveReceivables;
use App\Actions\Carriers\BounceCarrier;
use App\Actions\Carriers\CreateCarrier;
use App\Actions\Carriers\CreateCarrierFromSaferReport;
use App\Actions\Carriers\FmcsaDOTLookup;
use App\Actions\Carriers\FmcsaNameLookup;
use App\Actions\Carriers\UpdateCarrierGeneral;
use App\Actions\Contacts\CreateContact;
use App\Actions\Contacts\DeleteContact;
use App\Actions\Contacts\GetContactTypes;
use App\Actions\Contacts\UpdateContact;
use App\Actions\Customers\CreateCustomer;
use App\Actions\Customers\CreateCustomerFacility;
use App\Actions\Customers\DeleteCustomerFacility;
use App\Actions\Customers\UpdateCustomer;
use App\Actions\Dashboard\RecentCarriersCard;
use App\Actions\Dashboard\RecentShipmentsCard;
use App\Actions\Documents\CreateDocument;
use App\Actions\Documents\DeleteDocument;
use App\Actions\Documents\Generators\GenerateCustomerInvoice;
use App\Actions\Documents\Generators\GenerateRateConfirmation;
use App\Actions\Documents\GetDocument;
use App\Actions\Documents\GetDocumentsWithFolders;
use App\Actions\Documents\UpdateDocument;
use App\Actions\Facilities\CreateFacility;
use App\Actions\IntegrationSettings\DeleteIntegrationSetting;
use App\Actions\IntegrationSettings\SetIntegrationSetting;
use App\Actions\Locations\CreateLocation;
use App\Actions\Notes\CreateNote;
use App\Actions\Notes\DeleteNote;
use App\Actions\Notes\GetNotes;
use App\Actions\Organizations\UpdateOrganization;
use App\Actions\Shipments\CancelShipment;
use App\Actions\Shipments\CreateShipment;
use App\Actions\Shipments\DispatchShipment;
use App\Actions\Shipments\GetShipmentAccounting;
use App\Actions\Shipments\UncancelShipment;
use App\Actions\Shipments\UpdateShipmentCarrierDetails;
use App\Actions\Shipments\UpdateShipmentGeneral;
use App\Actions\Shipments\UpdateShipmentNumber;
use App\Actions\Shipments\UpdateShipmentCustomers;
use App\Actions\Shipments\UpdateShipmentStops;
use App\Actions\SubmitFeedback;
use App\Actions\Subscriptions\NewUserSeatsSubscription;
use App\Actions\Subscriptions\RedirectToBillingPortal;
use App\Actions\Subscriptions\UpdateUserSeatsSubscription;
use App\Actions\ZipToTimezone;
use App\Enums\Subscriptions\SubscriptionType;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrganizationInviteController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Shipments\ShipmentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TimezoneController;
use App\Http\Controllers\LanguageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::post('/feedback', SubmitFeedback::class)->name('feedback.submit');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::get('/products', function () {
    // Check if billing is enabled
    if (!config('subscriptions.enable_billing')) {
        abort(403, 'Billing is disabled');
    }

    return Inertia::render('Subscriptions/Products',
        [
            'hasSubscription' => current_organization()?->subscribed(SubscriptionType::USER_SEAT->value)
        ]
    );
})->name('products-list');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/language', [ProfileController::class, 'updateLanguage'])->name('profile.language');
    Route::get('/languages', [LanguageController::class, 'index'])->name('languages.index');

    Route::resource('organizations', OrganizationController::class);

    Route::post('organizations/{organization}/invites/{invite:code}/accept', [OrganizationInviteController::class, 'accept'])->name('organizations.invites.accept');
    Route::resource('organizations.invites', OrganizationInviteController::class)->scoped([
        'invite' => 'code',
    ])->only(['show']);
});

Route::middleware(['auth', 'organization-assigned'])->group(function() {
    Route::get('subscriptions/new', NewUserSeatsSubscription::class)->name('subscriptions.new');
    Route::get('subscriptions/manage', RedirectToBillingPortal::class)->name('subscriptions.manage');
});

Route::middleware(['auth', 'verified', 'organization-assigned', 'active-subscription'])->group(function () {

    Route::prefix('accounting')->name('accounting.')->group(function() {
        Route::get('rate-types', GetRateTypes::class)->name('rate-types.index');
    });

    Route::get('timezones/search', [TimezoneController::class, 'search'])->name('timezones.search');
    Route::get('timezones/zipcode', ZipToTimezone::class)->name('timezones.zipcode');

    Route::resource('organizations.invites', OrganizationInviteController::class)->scoped([
        'invite' => 'code',
    ])->except(['show']);

    Route::delete('organizations/{organization}/users/{user}', [OrganizationController::class, 'removeUser'])->name('organizations.users.destroy');
    Route::post('organizations/{organization}/users/{user}/transfer', [OrganizationController::class, 'transferOwnership'])->name('organizations.users.transfer');

    Route::post('organizations/{organization}/invites/{invite:code}/resend', [OrganizationInviteController::class, 'resend'])->name('organizations.invites.resend');

    Route::post('organizations/{organization}/switch', [OrganizationController::class, 'switchOrganization'])->name('organizations.switch');

    Route::get('organizations/{organization}/users', [OrganizationController::class, 'showUsers'])->name('organizations.users');
    Route::get('organizations/{organization}/roles', [OrganizationController::class, 'showRoles'])->name('organizations.roles');
    Route::get('organizations/{organization}/settings', [OrganizationController::class, 'showSettings'])->name('organizations.settings');
    Route::put('organizations/{organization}/settings', UpdateOrganization::class)->name('organizations.settings.update');
    Route::get('organizations/{organization}/billing', [OrganizationController::class, 'showBilling'])->name('organizations.billing');
    Route::put('organizations/{organization}/billing/update-seats', UpdateUserSeatsSubscription::class)->name('organizations.billing.update-seats');
    
    Route::get('organizations/{organization}/document-templates-page', [OrganizationController::class, 'showDocumentTemplates'])->name('organizations.document-templates-page');
    Route::get('organizations/{organization}/integration-settings', [OrganizationController::class, 'showIntegrationSettings'])->name('organizations.integration-settings');
    Route::post('organizations/{organization}/integration-settings', SetIntegrationSetting::class)->name('organizations.integration-settings.store');
    Route::delete('organizations/{organization}/integration-settings/{setting}', DeleteIntegrationSetting::class)->name('organizations.integration-settings.destroy');

    // Document Templates routes
    Route::get('organizations/{organization}/document-templates', \App\Actions\DocumentTemplates\GetDocumentTemplates::class)->name('organizations.document-templates.index');
    Route::get('organizations/{organization}/document-templates/{documentTemplate}', \App\Actions\DocumentTemplates\GetDocumentTemplate::class)->name('organizations.document-templates.show');
    Route::post('organizations/{organization}/document-templates', \App\Actions\DocumentTemplates\SaveDocumentTemplate::class)->name('organizations.document-templates.store');
    Route::post('document-templates/validate', \App\Actions\DocumentTemplates\ValidateTemplate::class)->name('document-templates.validate');
    Route::get('document-templates/default/{templateType}', \App\Actions\DocumentTemplates\GetDefaultTemplate::class)->name('document-templates.default');

    Route::prefix('organizations/{organization}/permissions')->group(function () {
        Route::post('role', [PermissionController::class, 'storeRole'])->name('organizations.permissions.role.store');
        Route::delete('role/{role}', [PermissionController::class, 'destroyRole'])->name('organizations.permissions.role.destroy');
        Route::patch('role/{role}', [PermissionController::class, 'updateRole'])->name('organizations.permissions.role.update');
    });

    Route::get('facilities/search', [FacilityController::class, 'search'])->name('facilities.search');
    Route::resource('facilities', FacilityController::class, [
        'except' => ['store', 'update'],
    ]);
    Route::post('facilities', CreateFacility::class)->name('facilities.store');
    Route::put('facilities/{facility}', \App\Actions\Facilities\UpdateFacility::class)->name('facilities.update');

    Route::get('carriers/search', [CarrierController::class, 'search'])->name('carriers.search');
    Route::resource('carriers', CarrierController::class, [
        'except' => ['store', 'update'],
    ]);
    Route::put('carriers/{carrier}', UpdateCarrierGeneral::class)->name('carriers.update');
    Route::post('carriers', CreateCarrier::class)->name('carriers.store');

    Route::get('carriers/{carrier}/bounced-loads', [CarrierController::class, 'bouncedLoads'])->name('carriers.bounced-loads');

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

    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::name('dashboard.cards.')->prefix('dashboard/cards')->group(function() {
        Route::get('/recent-shipments', RecentShipmentsCard::class)->name('recent-shipments');
        Route::get('/recent-carriers', RecentCarriersCard::class)->name('recent-carriers');
    });

    Route::name('documents.')->prefix('documents')->group(function () {
        Route::post('/', CreateDocument::class)->name('store');
        Route::put('/{document}', UpdateDocument::class)->name('update');
        Route::delete('/{document}', DeleteDocument::class)->name('destroy');
        Route::get('/{document}', GetDocument::class)->name('show');

        Route::get('/{documentableType}/{documentableId}', GetDocumentsWithFolders::class)->name('index');
    });


    
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
    Route::patch('shipments/{shipment}/dispatch', DispatchShipment::class)->name('shipments.dispatch');
    Route::patch('shipments/{shipment}/cancel', CancelShipment::class)->name('shipments.cancel');
    Route::patch('shipments/{shipment}/uncancel', UncancelShipment::class)->name('shipments.uncancel');
    Route::post('shipments/{shipment}/bounce', BounceCarrier::class)->name('shipments.bounce');

    Route::get('shipments/{shipment}/accounting', GetShipmentAccounting::class)->name('shipments.accounting');
    Route::post('shipments/{shipment}/accounting/payables', SavePayables::class)->name('shipments.accounting.payables');
    Route::post('shipments/{shipment}/accounting/receivables', SaveReceivables::class)->name('shipments.accounting.receivables');

    Route::post('shipments/{shipment}/documents/generate-rate-con', GenerateRateConfirmation::class)->name('shipments.documents.generate-rate-confirmation');
    Route::post('shipments/{shipment}/documents/generate-customer-invoice/{customer}', GenerateCustomerInvoice::class)->name('shipments.documents.generate-customer-invoice');

    Route::get('bounce-reasons', [CarrierController::class, 'bounceReasons'])->name('bounce-reasons');

    Route::delete('notes/{note}', DeleteNote::class)->name('notes.destroy');
    Route::get('notes/{notableType}/{notableId}', GetNotes::class)->name('notes.index');
    Route::post('notes/{notableType}/{notableId}', CreateNote::class)->name('notes.store');

    // Check Calls routes
    Route::get('shipments/{shipment}/check-calls', [\App\Http\Controllers\CheckCalls\CheckCallController::class, 'index'])->name('shipments.check-calls.index');
    Route::delete('shipments/{shipment}/check-calls/{checkcall}', [\App\Http\Controllers\CheckCalls\CheckCallController::class, 'destroy'])->name('shipments.check-calls.destroy');
    Route::post('shipments/{shipment}/check-calls', \App\Actions\CheckCalls\CreateCheckCall::class)->name('shipments.check-calls.store');

    Route::get('locations/search', [LocationController::class, 'search'])->name('locations.search');
    Route::post('locations', CreateLocation::class)->name('locations.store');

    Route::get('contacts/search', [ContactController::class, 'search'])->name('contacts.search');
    Route::post('contacts', CreateContact::class)->name('contacts.store');
    Route::put('contacts/{contact}', UpdateContact::class)->name('contacts.update');
    Route::delete('contacts/{contact}', DeleteContact::class)->name('contacts.destroy');

    Route::get('contacts/types/{contactable}', GetContactTypes::class)->name('contacts.types');

    
});

require __DIR__ . '/auth.php';
