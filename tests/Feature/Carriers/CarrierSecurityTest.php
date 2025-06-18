<?php

namespace Tests\Feature\Carriers;

use App\Actions\Carriers\CreateCarrier;
use App\Enums\Subscriptions\SubscriptionType;
use App\Models\Location;
use App\Models\Organizations\Organization;
use App\Models\User;
use App\Models\Carriers\Carrier;
use Illuminate\Support\Facades\Context;
use Inertia\Testing\AssertableInertia;
use Laravel\Cashier\Subscription;

beforeEach(function () {
    $this->setUpOrganization();
    
    $this->location = Location::factory()->create();
    
    // Set up second organization and user
    $this->secondUser = User::factory()->create();
    $this->secondOrg = Organization::create([
        'name' => 'Second Test Organization',
        'owner_id' => $this->secondUser->id,
    ]);
    $this->secondUser->organizations()->attach($this->secondOrg);
    $this->secondUser->current_organization_id = $this->secondOrg->id;
    $this->secondUser->save();

    // Create subscriptions for both organizations to satisfy middleware
    Subscription::create([
        'organization_id' => $this->organization->id,
        'type' => SubscriptionType::USER_SEAT->value,
        'stripe_id' => 'sub_test_first_' . $this->organization->id,
        'stripe_status' => 'active',
        'quantity' => 5,
    ]);

    Subscription::create([
        'organization_id' => $this->secondOrg->id,
        'type' => SubscriptionType::USER_SEAT->value,
        'stripe_id' => 'sub_test_second_' . $this->secondOrg->id,
        'stripe_status' => 'active',
        'quantity' => 5,
    ]);

    // Store organization IDs and initial active user
    $this->firstOrgId = $this->organization->id;
    $this->secondOrgId = $this->secondOrg->id;
    $this->activeUser = $this->user;

    // Helper functions to make tests more readable
    $this->switchToFirstOrg = function () {
        Context::addHidden('current_organization_id', $this->firstOrgId);
        $this->activeUser = $this->user;
    };

    $this->switchToSecondOrg = function () {
        Context::addHidden('current_organization_id', $this->secondOrgId);
        $this->activeUser = $this->secondUser;
    };

    $this->createTestCarrier = function ($name = 'Test Carrier') {
        return CreateCarrier::run(name: $name);
    };
});

test('it prevents access to carriers from other organizations', function () {
    $carrier = ($this->createTestCarrier)();

    // Verify first organization access
    $response = $this->actingAs($this->activeUser)->get(route('carriers.show', $carrier));
    expect($response->status())->toBe(200);

    // Verify second organization cannot access
    ($this->switchToSecondOrg)();
    $response = $this->actingAs($this->activeUser)->get(route('carriers.show', $carrier));
    expect($response->status())->toBe(404);

    // Verify first organization still has access
    ($this->switchToFirstOrg)();
    $response = $this->actingAs($this->activeUser)->get(route('carriers.show', $carrier));
    expect($response->status())->toBe(200);
});

test('it prevents updating carriers from other organizations', function () {
    // Create carrier in first organization
    $response = $this->actingAs($this->activeUser)
        ->withHeaders(['Accept' => 'application/json'])
        ->post(route('carriers.store'), [
            'name' => 'Test Carrier',
        ]);
    expect($response->status())->toBe(201);
    
    $carrier = Carrier::findOrFail($response->json('id'));
    $updateData = [
        'name' => 'Updated Name',
        'mc_number' => 'MC123456',
        'dot_number' => 'DOT789012',
        'physical_location_id' => $this->location->id,
        'contact_email' => 'contact@carrier.com',
        'contact_phone' => '(513) 731-4567',
    ];

    // Test update with second organization
    ($this->switchToSecondOrg)();
    $response = $this->actingAs($this->activeUser)->put(route('carriers.update', $carrier), $updateData);
    expect($response->status())->toBe(404);

    // Verify first organization can still update
    ($this->switchToFirstOrg)();
    $response = $this->actingAs($this->activeUser)->put(route('carriers.update', $carrier), $updateData);
    expect($response->status())->toBe(200);
});

test('it prevents deleting carriers from other organizations', function () {
    $carrier = ($this->createTestCarrier)();

    // Attempt delete from second organization
    ($this->switchToSecondOrg)();
    $response = $this->actingAs($this->activeUser)->delete(route('carriers.destroy', $carrier));
    expect($response->status())->toBe(404);

    // Verify carrier still exists
    ($this->switchToFirstOrg)();
    expect(Carrier::find($carrier->id))->not->toBeNull();

    // Verify first organization can delete
    $response = $this->actingAs($this->activeUser)->delete(route('carriers.destroy', $carrier));
    expect($response->status())->toBe(200);
});

test('it prevents listing carriers from other organizations', function () {
    // Create carriers in first organization
    ($this->createTestCarrier)('First Org Carrier 1');
    ($this->createTestCarrier)('First Org Carrier 2');

    // Create carrier in second organization
    ($this->switchToSecondOrg)();
    ($this->createTestCarrier)('Second Org Carrier');
    
    // Verify second organization only sees its carrier
    $response = $this->actingAs($this->activeUser)->get(route('carriers.search'));
    $carriers = $response->json();
    expect(count($carriers))->toBe(1);
    expect($carriers[0]['name'])->toBe('Second Org Carrier');
    
    // Verify first organization only sees its carriers
    ($this->switchToFirstOrg)();
    $response = $this->actingAs($this->activeUser)->get(route('carriers.search'));
    $carriers = $response->json();
    expect(count($carriers))->toBe(2);
    $names = array_column($carriers, 'name');
    expect($names)->toContain('First Org Carrier 1');
    expect($names)->toContain('First Org Carrier 2');
});

