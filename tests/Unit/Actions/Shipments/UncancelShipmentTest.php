<?php

namespace Tests\Unit\Actions\Shipments;

use App\Actions\Shipments\UncancelShipment;
use App\Enums\Permission;
use App\Enums\StopType;
use App\Models\Carriers\Carrier;
use App\Models\Shipments\Shipment;
use App\Models\Shipments\ShipmentStop;
use App\Models\User;
use App\States\Shipments\AtDelivery;
use App\States\Shipments\AtPickup;
use App\States\Shipments\Booked;
use App\States\Shipments\Canceled;
use App\States\Shipments\Delivered;
use App\States\Shipments\Dispatched;
use App\States\Shipments\InTransit;
use App\States\Shipments\Pending;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lorisleiva\Actions\ActionRequest;
use Mockery;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Tests\Traits\WithOrganization;

class UncancelShipmentTest extends TestCase
{
    use RefreshDatabase;
    use WithOrganization;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupOrganization();
    }

    /** @test */
    public function it_can_uncancel_a_shipment_with_no_carrier_to_pending_state()
    {
        // Create a shipment in Canceled state with no carrier
        $shipment = Shipment::factory()->create([
            'organization_id' => $this->organization->id,
            'carrier_id' => null,
        ]);
        $shipment->state->transitionTo(Canceled::class);
        $this->assertEquals(Canceled::class, get_class($shipment->state));

        // Execute the uncancel action
        $action = new UncancelShipment();
        $result = $action->handle($shipment);

        // Assert the shipment state has changed to Pending
        // (No carrier shipments stay in Pending - no listener recalculation needed)
        $this->assertEquals(Pending::class, get_class($result->state));
        $this->assertEquals('pending', $result->state->getValue());
    }

    /** @test */
    public function it_can_uncancel_a_shipment_with_carrier_but_no_stop_progress_to_booked_state()
    {
        // Create a carrier
        $carrier = Carrier::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        // Create a shipment in Canceled state with a carrier but no stop progress
        $shipment = Shipment::factory()->create([
            'organization_id' => $this->organization->id,
            'carrier_id' => $carrier->id,
        ]);
        
        // Add stops with no progress
        ShipmentStop::factory()->create([
            'shipment_id' => $shipment->id,
            'stop_number' => 1,
            'stop_type' => StopType::Pickup,
            'arrived_at' => null,
            'loaded_unloaded_at' => null,
            'left_at' => null,
        ]);
        
        ShipmentStop::factory()->create([
            'shipment_id' => $shipment->id,
            'stop_number' => 2,
            'stop_type' => StopType::Delivery,
            'arrived_at' => null,
            'loaded_unloaded_at' => null,
            'left_at' => null,
        ]);

        $shipment->state->transitionTo(Canceled::class);
        $this->assertEquals(Canceled::class, get_class($shipment->state));

        // Execute the uncancel action
        $action = new UncancelShipment();
        $result = $action->handle($shipment);

        // Assert the shipment state has changed to Booked
        // (Action transitions to Booked since no stop progress, doesn't auto-dispatch)
        $this->assertEquals(Booked::class, get_class($result->state));
        $this->assertEquals('booked', $result->state->getValue());
    }

    /** @test */
    public function it_can_uncancel_a_shipment_which_listener_recalculates_to_at_pickup()
    {
        // Create a carrier
        $carrier = Carrier::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        // Create a shipment in Canceled state with a carrier and stop progress
        $shipment = Shipment::factory()->create([
            'organization_id' => $this->organization->id,
            'carrier_id' => $carrier->id,
        ]);
        
        // Add stops with pickup progress - currently at pickup (arrived but not left)
        ShipmentStop::factory()->create([
            'shipment_id' => $shipment->id,
            'stop_number' => 1,
            'stop_type' => StopType::Pickup,
            'arrived_at' => now(),
            'loaded_unloaded_at' => null,
            'left_at' => null,
        ]);
        
        ShipmentStop::factory()->create([
            'shipment_id' => $shipment->id,
            'stop_number' => 2,
            'stop_type' => StopType::Delivery,
            'arrived_at' => null,
            'loaded_unloaded_at' => null,
            'left_at' => null,
        ]);

        $shipment->state->transitionTo(Canceled::class);
        $this->assertEquals(Canceled::class, get_class($shipment->state));

        // Execute the uncancel action
        $action = new UncancelShipment();
        $result = $action->handle($shipment);

        // Assert the shipment state has changed to AtPickup
        // (Action transitions to Booked, then state service recalculates to AtPickup based on stop progress)
        $this->assertEquals(AtPickup::class, get_class($result->state));
        $this->assertEquals('at_pickup', $result->state->getValue());
    }

    /** @test */
    public function it_can_uncancel_a_shipment_which_listener_recalculates_to_in_transit()
    {
        // Create a carrier
        $carrier = Carrier::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        // Create a shipment in Canceled state with a carrier and stop progress
        $shipment = Shipment::factory()->create([
            'organization_id' => $this->organization->id,
            'carrier_id' => $carrier->id,
        ]);
        
        // Add stops with in-transit progress - pickup completed (arrived, loaded, and left)
        ShipmentStop::factory()->create([
            'shipment_id' => $shipment->id,
            'stop_number' => 1,
            'stop_type' => StopType::Pickup,
            'arrived_at' => now()->subHours(2),
            'loaded_unloaded_at' => now()->subHours(1.5),
            'left_at' => now()->subHours(1),
        ]);
        
        // Delivery not yet reached
        ShipmentStop::factory()->create([
            'shipment_id' => $shipment->id,
            'stop_number' => 2,
            'stop_type' => StopType::Delivery,
            'arrived_at' => null,
            'loaded_unloaded_at' => null,
            'left_at' => null,
        ]);

        $shipment->state->transitionTo(Canceled::class);
        $this->assertEquals(Canceled::class, get_class($shipment->state));

        // Execute the uncancel action
        $action = new UncancelShipment();
        $result = $action->handle($shipment);

        // Assert the shipment state has changed to InTransit
        // (Action transitions to Booked, then state service recalculates to InTransit based on stop progress)
        $this->assertEquals(InTransit::class, get_class($result->state));
        $this->assertEquals('in_transit', $result->state->getValue());
    }

    /** @test */
    public function it_can_uncancel_a_shipment_which_listener_recalculates_to_at_delivery()
    {
        // Create a carrier
        $carrier = Carrier::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        // Create a shipment in Canceled state with a carrier and stop progress
        $shipment = Shipment::factory()->create([
            'organization_id' => $this->organization->id,
            'carrier_id' => $carrier->id,
        ]);
        
        // Add stops with at-delivery progress - pickup completed
        ShipmentStop::factory()->create([
            'shipment_id' => $shipment->id,
            'stop_number' => 1,
            'stop_type' => StopType::Pickup,
            'arrived_at' => now()->subHours(3),
            'loaded_unloaded_at' => now()->subHours(2.5),
            'left_at' => now()->subHours(2),
        ]);
        
        // Currently at delivery (arrived but not left)
        ShipmentStop::factory()->create([
            'shipment_id' => $shipment->id,
            'stop_number' => 2,
            'stop_type' => StopType::Delivery,
            'arrived_at' => now()->subHour(),
            'loaded_unloaded_at' => null,
            'left_at' => null,
        ]);

        $shipment->state->transitionTo(Canceled::class);
        $this->assertEquals(Canceled::class, get_class($shipment->state));

        // Execute the uncancel action
        $action = new UncancelShipment();
        $result = $action->handle($shipment);

        // Assert the shipment state has changed to AtDelivery
        // (Action transitions to Booked, then state service recalculates to AtDelivery based on stop progress)
        $this->assertEquals(AtDelivery::class, get_class($result->state));
        $this->assertEquals('at_delivery', $result->state->getValue());
    }

    /** @test */
    public function it_can_uncancel_a_shipment_which_listener_recalculates_to_delivered()
    {
        // Create a carrier
        $carrier = Carrier::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        // Create a shipment in Canceled state with a carrier and completed delivery
        $shipment = Shipment::factory()->create([
            'organization_id' => $this->organization->id,
            'carrier_id' => $carrier->id,
        ]);
        
        // Add stops with completed delivery
        ShipmentStop::factory()->create([
            'shipment_id' => $shipment->id,
            'stop_number' => 1,
            'stop_type' => StopType::Pickup,
            'arrived_at' => now()->subHours(4),
            'loaded_unloaded_at' => now()->subHours(3),
            'left_at' => now()->subHours(2.5),
        ]);
        
        ShipmentStop::factory()->create([
            'shipment_id' => $shipment->id,
            'stop_number' => 2,
            'stop_type' => StopType::Delivery,
            'arrived_at' => now()->subHours(2),
            'loaded_unloaded_at' => now()->subHour(),
            'left_at' => now()->subMinutes(30),
        ]);

        $shipment->state->transitionTo(Canceled::class);
        $this->assertEquals(Canceled::class, get_class($shipment->state));

        // Execute the uncancel action
        $action = new UncancelShipment();
        $result = $action->handle($shipment);

        // Assert the shipment state has changed to Delivered
        // (Action transitions to Booked, then state service recalculates to Delivered based on stop progress)
        $this->assertEquals(Delivered::class, get_class($result->state));
        $this->assertEquals('delivered', $result->state->getValue());
    }

    /** @test */
    public function it_can_uncancel_a_shipment_that_was_only_booked_not_dispatched()
    {
        // Create a carrier
        $carrier = Carrier::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        // Create a shipment in Canceled state with a carrier but NO stops at all
        // This represents a shipment that was only Booked (carrier assigned) but never Dispatched
        $shipment = Shipment::factory()->create([
            'organization_id' => $this->organization->id,
            'carrier_id' => $carrier->id,
        ]);
        
        // First transition to Booked to simulate the real workflow
        $shipment->state->transitionTo(Booked::class);
        // Then cancel it
        $shipment->state->transitionTo(Canceled::class);
        $this->assertEquals(Canceled::class, get_class($shipment->state));

        // Execute the uncancel action
        $action = new UncancelShipment();
        $result = $action->handle($shipment);

        // Assert the shipment state has changed to Booked
        // (Action transitions to Booked since no stop progress, doesn't auto-dispatch)
        $this->assertEquals(Booked::class, get_class($result->state));
        $this->assertEquals('booked', $result->state->getValue());
    }

    /** @test */
    public function it_requires_shipment_edit_permission()
    {
        // Create a user without shipment edit permission
        $user = User::factory()->create();
        $this->organization->users()->attach($user);
        $role = Role::create(['name' => 'tester', 'organization_id' => $this->organization->id]);
        $user->assignRole($role);
        
        // Create a mock request with the unauthorized user
        $request = Mockery::mock(ActionRequest::class);
        $request->shouldReceive('user')->andReturn($user);

        // Create the action and check authorization
        $action = new UncancelShipment();
        $this->assertFalse($action->authorize($request));

        // Now give the user permission and verify it works
        $role->givePermissionTo(Permission::SHIPMENT_EDIT);
        $this->assertTrue($action->authorize($request));
    }

    /** @test */
    public function it_returns_redirect_response_when_called_as_controller()
    {
        // Create a shipment in Canceled state
        $shipment = Shipment::factory()->create([
            'organization_id' => $this->organization->id,
        ]);
        $shipment->state->transitionTo(Canceled::class);

        // Create a user with shipment edit permission
        $user = User::factory()->create();
        $this->organization->users()->attach($user);
        $role = Role::create(['name' => 'editor', 'organization_id' => $this->organization->id]);
        $role->givePermissionTo(Permission::SHIPMENT_EDIT);
        $user->assignRole($role);
        
        // Create a mock request with the authorized user
        $request = Mockery::mock(ActionRequest::class);
        $request->shouldReceive('user')->andReturn($user);

        // Execute the action as controller
        $action = new UncancelShipment();
        $response = $action->asController($request, $shipment);

        // Assert the response is a redirect with success message
        $this->assertStringContainsString('Redirecting', $response->getContent());
        $this->assertTrue($response->isRedirect());
    }
}
