<?php

use App\Actions\Shipments\CreateShipment;
use App\Actions\Shipments\GetShipmentAuditHistory;
use App\Models\Carriers\Carrier;
use App\Models\Facility;
use App\Models\Customers\Customer;
use App\Models\Shipments\ShipmentStop;

beforeEach(function () {
    $this->setUpOrganization();

    $this->customer = Customer::factory()->create();
    $this->carrier = Carrier::factory()->create();
    $this->pickupFacility = Facility::factory()->create();
    $this->deliveryFacility = Facility::factory()->create();
});

test('it audits stop creation', function () {
    $shipment = CreateShipment::run(
        customerIds: [$this->customer->id],
        carrierId: $this->carrier->id,
        stops: [
            [
                'facility_id' => $this->pickupFacility->id,
                'stop_type' => 'pickup',
                'stop_number' => 1,
                'special_instructions' => 'Handle with care',
                'reference_numbers' => 'REF001',
                'appointment_at' => now()->addDays(1)->toDateTimeString(),
            ],
        ],
    );

    $stop = $shipment->stops->first();

    // Check that audit was created for the stop
    expect($stop->audits()->count())->toBeGreaterThan(0);

    $audit = $stop->audits()->first();
    expect($audit->event)->toBe('created');
    expect($audit->auditable_type)->toBe(ShipmentStop::class);
    expect($audit->auditable_id)->toBe($stop->id);
});

test('it audits stop updates', function () {
    $shipment = CreateShipment::run(
        customerIds: [$this->customer->id],
        carrierId: $this->carrier->id,
        stops: [
            [
                'facility_id' => $this->pickupFacility->id,
                'stop_type' => 'pickup',
                'stop_number' => 1,
                'special_instructions' => 'Original instructions',
                'reference_numbers' => 'REF123',
                'appointment_at' => now()->addDays(1)->toDateTimeString(),
            ],
        ],
    );

    $stop = $shipment->stops->first();
    $initialAuditCount = $stop->audits()->count();

    // Update the stop
    $stop->update([
        'special_instructions' => 'Updated instructions',
        'reference_numbers' => 'REF456',
    ]);

    // Verify new audit was created
    expect($stop->audits()->count())->toBe($initialAuditCount + 1);

    $updateAudit = $stop->audits()->where('event', 'updated')->first();
    expect($updateAudit)->not->toBeNull();
    expect($updateAudit->old_values['special_instructions'])->toBe('Original instructions');
    expect($updateAudit->new_values['special_instructions'])->toBe('Updated instructions');
    expect($updateAudit->old_values['reference_numbers'])->toBe('REF123');
    expect($updateAudit->new_values['reference_numbers'])->toBe('REF456');
});

test('it includes stop audits in shipment audit history', function () {
    $shipment = CreateShipment::run(
        customerIds: [$this->customer->id],
        carrierId: $this->carrier->id,
        stops: [
            [
                'facility_id' => $this->pickupFacility->id,
                'stop_type' => 'pickup',
                'stop_number' => 1,
                'special_instructions' => 'Pickup instructions',
                'reference_numbers' => 'REF001',
                'appointment_at' => now()->addDays(1)->toDateTimeString(),
            ],
            [
                'facility_id' => $this->deliveryFacility->id,
                'stop_type' => 'delivery',
                'stop_number' => 2,
                'special_instructions' => 'Delivery instructions',
                'reference_numbers' => 'REF002',
                'appointment_at' => now()->addDays(2)->toDateTimeString(),
            ],
        ],
    );

    // Update one of the stops
    $stop = $shipment->stops->first();
    $stop->update(['special_instructions' => 'Updated pickup instructions']);

    // Get audit history
    $action = new GetShipmentAuditHistory();
    $audits = $action->handle($shipment);

    // Should have audits for: shipment creation, 2 stop creations, 1 stop update
    expect($audits->count())->toBeGreaterThan(3);

    // Filter to just stop audits
    $stopAudits = $audits->filter(fn($audit) => $audit['entity_type'] === 'Stop');
    expect($stopAudits->count())->toBeGreaterThan(0);

    // Verify stop audit details
    $stopUpdateAudit = $stopAudits->firstWhere('event', 'updated');
    expect($stopUpdateAudit)->not->toBeNull();
    expect($stopUpdateAudit['entity_name'])->toContain('Stop #1');

    // Verify changes are formatted correctly
    $instructionChange = collect($stopUpdateAudit['changes'])->firstWhere('field', 'Special Instructions');
    expect($instructionChange)->not->toBeNull();
    expect($instructionChange['old_value'])->toBe('Pickup instructions');
    expect($instructionChange['new_value'])->toBe('Updated pickup instructions');
});

test('it audits stop deletion', function () {
    $shipment = CreateShipment::run(
        customerIds: [$this->customer->id],
        carrierId: $this->carrier->id,
        stops: [
            [
                'facility_id' => $this->pickupFacility->id,
                'stop_type' => 'pickup',
                'stop_number' => 1,
                'special_instructions' => 'To be deleted',
                'reference_numbers' => 'REF001',
                'appointment_at' => now()->addDays(1)->toDateTimeString(),
            ],
        ],
    );

    $stop = $shipment->stops->first();
    $stopId = $stop->id;
    $stopNumber = $stop->stop_number;

    // Delete the stop
    $stop->delete();

    // Get audit history
    $action = new GetShipmentAuditHistory();
    $audits = $action->handle($shipment);

    // Filter to stop audits
    $stopAudits = $audits->filter(fn($audit) => $audit['entity_type'] === 'Stop');

    // Should have created and deleted events
    $deleteAudit = $stopAudits->firstWhere('event', 'deleted');
    expect($deleteAudit)->not->toBeNull();
    expect($deleteAudit['entity_name'])->toContain('Stop #' . $stopNumber);
});

test('it includes stop field mappings in audit', function () {
    $shipment = CreateShipment::run(
        customerIds: [$this->customer->id],
        carrierId: $this->carrier->id,
        stops: [
            [
                'facility_id' => $this->pickupFacility->id,
                'stop_type' => 'pickup',
                'stop_number' => 1,
                'special_instructions' => 'Test instructions',
                'reference_numbers' => 'REF001',
                'eta' => now()->addDays(1)->toDateTimeString(),
                'appointment_at' => now()->addDays(1)->toDateTimeString(),
            ],
        ],
    );

    $stop = $shipment->stops->first();

    // Update multiple fields
    $stop->update([
        'eta' => now()->addDays(2)->toDateTimeString(),
        'appointment_at' => now()->addDays(2)->toDateTimeString(),
        'stop_type' => 'delivery',
    ]);

    // Get audit history
    $action = new GetShipmentAuditHistory();
    $audits = $action->handle($shipment);

    // Get the update audit
    $stopUpdateAudit = $audits->firstWhere(function ($audit) {
        return $audit['entity_type'] === 'Stop' && $audit['event'] === 'updated';
    });

    expect($stopUpdateAudit)->not->toBeNull();

    // Verify field names are properly mapped
    $changeFields = collect($stopUpdateAudit['changes'])->pluck('field')->toArray();
    expect($changeFields)->toContain('ETA');
    expect($changeFields)->toContain('Appointment Start');
    expect($changeFields)->toContain('Stop Type');
});

test('it shows facility name instead of id in audits', function () {
    $shipment = CreateShipment::run(
        customerIds: [$this->customer->id],
        carrierId: $this->carrier->id,
        stops: [
            [
                'facility_id' => $this->pickupFacility->id,
                'stop_type' => 'pickup',
                'stop_number' => 1,
                'special_instructions' => 'Test instructions',
                'reference_numbers' => 'REF001',
                'appointment_at' => now()->addDays(1)->toDateTimeString(),
            ],
        ],
    );

    $stop = $shipment->stops->first();

    // Update the facility
    $stop->update([
        'facility_id' => $this->deliveryFacility->id,
    ]);

    // Get the audit
    $updateAudit = $stop->audits()->where('event', 'updated')->first();
    expect($updateAudit)->not->toBeNull();

    // Verify facility names are shown instead of IDs
    expect($updateAudit->old_values['facility_id'])->toBe($this->pickupFacility->name);
    expect($updateAudit->new_values['facility_id'])->toBe($this->deliveryFacility->name);

    // Also verify in the formatted audit history
    $action = new GetShipmentAuditHistory();
    $audits = $action->handle($shipment);

    $stopUpdateAudit = $audits->firstWhere(function ($audit) {
        return $audit['entity_type'] === 'Stop' && $audit['event'] === 'updated';
    });

    $facilityChange = collect($stopUpdateAudit['changes'])->firstWhere('field', 'Facility');
    expect($facilityChange)->not->toBeNull();
    expect($facilityChange['old_value'])->toBe($this->pickupFacility->name);
    expect($facilityChange['new_value'])->toBe($this->deliveryFacility->name);
});
