<?php

use App\Actions\Organizations\SendInvite;
use App\Enums\Subscriptions\SubscriptionType;
use App\Mail\Organizations\UserInvite;
use App\Models\Organizations\Organization;
use App\Models\Organizations\OrganizationInvite;
use App\Models\Organizations\OrganizationUser;
use App\Models\User; // Assuming your User model is here
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Subscription;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Any additional global test setup can go here.
});

afterEach(function () {
    // Close any created Mockery mocks
    \Mockery::close();
});

it('sends an invite successfully when seats are available', function () {
    Mail::fake();

    // Create an organization using its factory.
    $organization = current_organization();
    
    // Create a subscription with 2 seats
    Subscription::create([
        'organization_id' => $organization->id,
        'type' => SubscriptionType::USER_SEAT->value,
        'stripe_id' => 'sub_test123',
        'stripe_status' => 'active',
        'quantity' => 2,
    ]);

    $email = 'invitee@example.com';

    // Execute the action.
    $action = new SendInvite();
    $invite = $action->handle($email, $organization);

    // Verify that the returned invite is valid.
    expect($invite)->toBeInstanceOf(OrganizationInvite::class);
    expect($invite->code)->toHaveLength(6);
    expect($invite->code)->toEqual(strtoupper($invite->code)); // Code should be uppercase.
    expect($invite->email)->toBe(strtolower($email));

    // Check that the expiration is set approximately 7 days in the future.
    $expectedExpire = now()->addDays(7)->format('Y-m-d');
    $actualExpire = Carbon::parse($invite->expire_at)->format('Y-m-d');
    expect($actualExpire)->toBe($expectedExpire);

    // Assert that the invite email was sent.
    Mail::assertSent(UserInvite::class, function ($mail) use ($email, $invite) {
        return $mail->hasTo($email) && $mail->invite->id === $invite->id;
    });
});

it('throws exception if an open invite already exists for this email', function () {
    Mail::fake();

    $organization = current_organization();
    
    // Create a subscription with enough seats
    Subscription::create([
        'organization_id' => $organization->id,
        'type' => SubscriptionType::USER_SEAT->value,
        'stripe_id' => 'sub_test123',
        'stripe_status' => 'active',
        'quantity' => 5,
    ]);
    
    $email = 'duplicate@example.com';

    // Pre-create an invite for this email.
    OrganizationInvite::create([
        'organization_id' => $organization->id,
        'email' => strtolower($email),
        'code' => 'ABCDEF',
        'expire_at' => now()->addDays(7),
    ]);

    $action = new SendInvite();

    // Expect an exception about an already open invite.
    $this->expectException(BadRequestException::class);
    $this->expectExceptionMessage('An open invite already exists for this email address.');

    $action->handle($email, $organization);
});

it('throws exception if user is already a member of the organization', function () {
    Mail::fake();

    $organization = current_organization();
    
    // Create a subscription with enough seats
    Subscription::create([
        'organization_id' => $organization->id,
        'type' => SubscriptionType::USER_SEAT->value,
        'stripe_id' => 'sub_test123',
        'stripe_status' => 'active',
        'quantity' => 5,
    ]);
    
    $email = 'member@example.com';

    // Create a user with the given email.
    $user = User::factory()->create(['email' => strtolower($email)]);

    // Create an organization-user record to simulate organization membership.
    OrganizationUser::create([
        'organization_id' => $organization->id,
        'user_id' => $user->id,
    ]);

    $action = new SendInvite();

    // Expect an exception stating the user is already a member.
    $this->expectException(BadRequestException::class);
    $this->expectExceptionMessage('This user is already a member of this organization.');

    $action->handle($email, $organization);
});

it('throws exception if organization has no subscription', function () {
    Mail::fake();

    $organization = current_organization();
    $email = 'test@example.com';

    $action = new SendInvite();

    // Expect an exception about no subscription.
    $this->expectException(BadRequestException::class);
    $this->expectExceptionMessage('Organization does not have an active subscription.');

    $action->handle($email, $organization);
});

it('throws exception if organization has reached seat limit with current members', function () {
    Mail::fake();

    $organization = current_organization();
    
    // Create a subscription with only 1 seat
    Subscription::create([
        'organization_id' => $organization->id,
        'type' => SubscriptionType::USER_SEAT->value,
        'stripe_id' => 'sub_test123',
        'stripe_status' => 'active',
        'quantity' => 1,
    ]);

    // The organization already has 1 member (the current user), so we're at capacity
    $email = 'test@example.com';

    $action = new SendInvite();

    // Expect an exception about not enough seats.
    $this->expectException(BadRequestException::class);
    $this->expectExceptionMessage('Not enough seats available');

    $action->handle($email, $organization);
});

it('throws exception if organization has reached seat limit with pending invites', function () {
    Mail::fake();

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
    $email = 'test@example.com';

    $action = new SendInvite();

    // Expect an exception about not enough seats.
    $this->expectException(BadRequestException::class);
    $this->expectExceptionMessage('Not enough seats available');

    $action->handle($email, $organization);
});
