<?php

use App\Actions\Shipments\CreateShipment;
use App\Enums\Subscriptions\SubscriptionType;
use App\Models\Carriers\Carrier;
use App\Models\Facility;
use App\Models\Customers\Customer;
use App\Models\Organizations\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Context;
use Laravel\Cashier\Subscription;

beforeEach(function () {
    $this->setUpOrganization();
    
    $this->customer = Customer::factory()->create();
    $this->carrier = Carrier::factory()->create();
    $this->facility = Facility::factory()->create();
    
    // Create common test data
    $this->shipmentStopData = [
        'facility_id' => $this->facility->id,
        'stop_type' => 'pickup',
        'stop_number' => 1,
        'special_instructions' => 'Handle with care',
        'reference_numbers' => 'REF123',
        'appointment_at' => now()->addDays(2)->toDateTimeString(),
    ];
    
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
});

test('it prevents access to shipments from other organizations', function () {
    // Create shipment in first organization
    $firstOrgId = $this->organization->id;
    $shipment = CreateShipment::run(
        customerIds: [$this->customer->id],
        carrierId: $this->carrier->id,
        stops: [$this->shipmentStopData],
    );

    // Verify first organization access
    $response = $this->actingAs($this->user)
        ->get(route('shipments.show', $shipment));
    expect($response->status())->toBe(200);

    // Set up second organization
    Context::addHidden('current_organization_id', $this->secondOrg->id);
    $response = $this->actingAs($this->secondUser)
        ->get(route('shipments.show', $shipment));
    expect($response->status())->toBe(404);

    // Verify first organization still has access
    Context::addHidden('current_organization_id', $firstOrgId);
    $response = $this->actingAs($this->user)
        ->get(route('shipments.show', $shipment));
    expect($response->status())->toBe(200);
});

test('it prevents updating shipments from other organizations', function () {
    // Create shipment in first organization
    $firstOrgId = $this->organization->id;
    $response = $this->actingAs($this->user)
        ->withHeaders(['Accept' => 'application/json'])
        ->post(route('shipments.store'), [
            'customer_ids' => [$this->customer->id],
            'carrier_id' => $this->carrier->id,
            'stops' => [$this->shipmentStopData],
        ]);
    expect($response->status())->toBe(200);
    
    // Load shipment with relationships
    $shipmentId = $response->json('id');
    $shipment = \App\Models\Shipments\Shipment::with([
        'customers', 'carrier', 'stops', 'trailer_type', 'trailer_size'
    ])->findOrFail($shipmentId);
    $shipment = (new \App\Http\Resources\ShipmentResource($shipment))->resolve();

    // Set up second organization
    Context::addHidden('current_organization_id', $this->secondOrg->id);

    // Test various update endpoints with second organization
    $updateTests = [
        [
            'route' => 'shipments.updateStops',
            'data' => ['stops' => [
                [
                    'id' => $shipment['stops'][0]['id'],
                    'facility_id' => $this->facility->id,
                    'stop_type' => 'delivery',
                    'stop_number' => 1,
                    'special_instructions' => 'Updated instructions',
                    'reference_numbers' => 'REF456',
                    'appointment_at' => now()->addDays(3)->toDateTimeString(),
                ],
            ]
            ],
        ],
        [
            'route' => 'shipments.updateShipmentNumber',
            'data' => ['shipment_number' => 'NEW123'],
        ],
        [
            'route' => 'shipments.updateGeneral',
            'data' => ['weight' => 2000, 'trip_distance' => 500],
        ],
        [
            'route' => 'shipments.updateCarrierDetails',
            'data' => ['carrier_id' => Carrier::factory()->create()->id],
        ],
        [
            'route' => 'shipments.updateCustomers',
            'data' => ['customer_ids' => [Customer::factory()->create()->id]],
        ],
    ];

    // Verify second organization cannot update
    foreach ($updateTests as $test) {
        $response = $this->actingAs($this->secondUser)
            ->patch(route($test['route'], $shipment['id']), $test['data']);
        expect($response->status())->toBe(404);
    }

    // Verify first organization can update
    Context::addHidden('current_organization_id', $firstOrgId);
    foreach ($updateTests as $test) {
        $response = $this->actingAs($this->user)
            ->patch(route($test['route'], $shipment['id']), $test['data']);
        expect($response->status())->toBe(302);
        $response->assertSessionHas('success');
    }
}); 