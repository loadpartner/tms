<?php

use App\Actions\Organizations\CheckSeatLimits;
use App\Enums\Subscriptions\SubscriptionType;
use App\Models\Organizations\Organization;
use App\Models\Organizations\OrganizationInvite;
use App\Models\Organizations\OrganizationUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Subscription;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Any additional global test setup can go here.
});

afterEach(function () {
    // Close any created Mockery mocks
    \Mockery::close();
});

it('passes when organization has available seats', function () {
    $organization = current_organization();
    
    // Create a subscription with 3 seats
    Subscription::create([
        'organization_id' => $organization->id,
        'type' => SubscriptionType::USER_SEAT->value,
        'stripe_id' => 'sub_test123',
        'stripe_status' => 'active',
        'quantity' => 3,
    ]);

    // Create one additional user
    $user = User::factory()->create();
    OrganizationUser::create([
        'organization_id' => $organization->id,
        'user_id' => $user->id,
    ]);

    // Should pass since we have 2 users out of 3 seats
    $action = new CheckSeatLimits();
    
    // This should not throw an exception, so we just call it
    $action->handle($organization);
    
    // If we reach this point, the test passes
    expect(true)->toBe(true);
});

it('throws exception when organization has no subscription', function () {
    $organization = current_organization();

    $action = new CheckSeatLimits();

    // Expect an exception about no subscription.
    $this->expectException(BadRequestException::class);
    $this->expectExceptionMessage('Organization does not have an active subscription.');

    $action->handle($organization);
});

it('throws exception when organization has reached seat limit with current members', function () {
    $organization = current_organization();
    
    // Create a subscription with only 1 seat
    Subscription::create([
        'organization_id' => $organization->id,
        'type' => SubscriptionType::USER_SEAT->value,
        'stripe_id' => 'sub_test123',
        'stripe_status' => 'active',
        'quantity' => 1,
    ]);

    // Organization already has 1 member (the current user), so we're at capacity
    $action = new CheckSeatLimits();

    // Expect an exception about not enough seats.
    $this->expectException(BadRequestException::class);
    $this->expectExceptionMessage('Not enough seats available');

    $action->handle($organization);
});

it('throws exception when organization has reached seat limit with pending invites', function () {
    $organization = current_organization();
    
    // Create a subscription with 2 seats
    Subscription::create([
        'organization_id' => $organization->id,
        'type' => SubscriptionType::USER_SEAT->value,
        'stripe_id' => 'sub_test123',
        'stripe_status' => 'active',
        'quantity' => 2,
    ]);

    // Create a pending invite to fill up the remaining slot
    OrganizationInvite::create([
        'organization_id' => $organization->id,
        'email' => 'existing@example.com',
        'code' => 'EXIST1',
        'expire_at' => now()->addDays(7),
    ]);

    // Now we have 1 member + 1 invite = 2 total, so we're at capacity
    $action = new CheckSeatLimits();

    // Expect an exception about not enough seats.
    $this->expectException(BadRequestException::class);
    $this->expectExceptionMessage('Not enough seats available');

    $action->handle($organization);
});

it('returns correct seat usage information with subscription', function () {
    $organization = current_organization();
    
    // Create a subscription with 5 seats
    Subscription::create([
        'organization_id' => $organization->id,
        'type' => SubscriptionType::USER_SEAT->value,
        'stripe_id' => 'sub_test123',
        'stripe_status' => 'active',
        'quantity' => 5,
    ]);

    // Create one additional user
    $user = User::factory()->create();
    OrganizationUser::create([
        'organization_id' => $organization->id,
        'user_id' => $user->id,
    ]);

    // Create a pending invite
    OrganizationInvite::create([
        'organization_id' => $organization->id,
        'email' => 'pending@example.com',
        'code' => 'PEND01',
        'expire_at' => now()->addDays(7),
    ]);

    $action = new CheckSeatLimits();
    $usage = $action->getSeatUsage($organization);

    expect($usage)->toEqual([
        'current_members' => 2, // current user + additional user
        'pending_invites' => 1,
        'total_used' => 3,
        'max_seats' => 5,
        'has_available_seats' => true,
        'has_subscription' => true,
    ]);
});

it('returns correct seat usage information without subscription', function () {
    $organization = current_organization();

    // Create one additional user
    $user = User::factory()->create();
    OrganizationUser::create([
        'organization_id' => $organization->id,
        'user_id' => $user->id,
    ]);

    $action = new CheckSeatLimits();
    $usage = $action->getSeatUsage($organization);

    expect($usage)->toEqual([
        'current_members' => 2, // current user + additional user
        'pending_invites' => 0,
        'total_used' => 2,
        'max_seats' => 0,
        'has_available_seats' => false,
        'has_subscription' => false,
    ]);
}); 