<?php

namespace App\Actions\Organizations;

use App\Enums\Subscriptions\SubscriptionType;
use App\Models\Organizations\Organization;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckSeatLimits
{
    use AsAction;

    public function handle(Organization $organization): void
    {
        // Check if organization has a subscription
        if (!$organization->subscribed(SubscriptionType::USER_SEAT->value)) {
            throw new \Symfony\Component\HttpFoundation\Exception\BadRequestException('Organization does not have an active subscription.');
        }

        $subscription = $organization->subscription(SubscriptionType::USER_SEAT->value);
        $maxSeats = $subscription->quantity;

        // Count current organization members
        $currentMembers = $organization->users()->count();

        // Count pending invites
        $pendingInvites = $organization->invites()->count();

        $totalUsed = $currentMembers + $pendingInvites;

        if ($totalUsed >= $maxSeats) {
            throw new \Symfony\Component\HttpFoundation\Exception\BadRequestException('Not enough seats available. You are using ' . $totalUsed . ' out of ' . $maxSeats . ' seats. Please upgrade your subscription to invite more users.');
        }
    }

    /**
     * Get seat usage information for an organization
     */
    public function getSeatUsage(Organization $organization): array
    {
        if (!$organization->subscribed(SubscriptionType::USER_SEAT->value)) {
            return [
                'current_members' => $organization->users()->count(),
                'pending_invites' => $organization->invites()->count(),
                'total_used' => $organization->users()->count() + $organization->invites()->count(),
                'max_seats' => 0,
                'has_available_seats' => false,
                'has_subscription' => false,
            ];
        }

        $subscription = $organization->subscription(SubscriptionType::USER_SEAT->value);
        $maxSeats = $subscription->quantity;
        $currentMembers = $organization->users()->count();
        $pendingInvites = $organization->invites()->count();
        $totalUsed = $currentMembers + $pendingInvites;

        return [
            'current_members' => $currentMembers,
            'pending_invites' => $pendingInvites,
            'total_used' => $totalUsed,
            'max_seats' => $maxSeats,
            'has_available_seats' => $totalUsed < $maxSeats,
            'has_subscription' => true,
        ];
    }
} 