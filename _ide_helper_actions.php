<?php

namespace App\Actions\Organizations;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $email, \App\Models\Organizations\Organization $organization)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $email, \App\Models\Organizations\Organization $organization)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $email, \App\Models\Organizations\Organization $organization)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $email, \App\Models\Organizations\Organization $organization)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $email, \App\Models\Organizations\Organization $organization)
 * @method static dispatchSync(string $email, \App\Models\Organizations\Organization $organization)
 * @method static dispatchNow(string $email, \App\Models\Organizations\Organization $organization)
 * @method static dispatchAfterResponse(string $email, \App\Models\Organizations\Organization $organization)
 * @method static \App\Models\Organizations\OrganizationInvite run(string $email, \App\Models\Organizations\Organization $organization)
 */
class SendInvite
{
}
namespace App\Actions\Shipments;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(array $shipperIds, int $carrierId, array $stops)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(array $shipperIds, int $carrierId, array $stops)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(array $shipperIds, int $carrierId, array $stops)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, array $shipperIds, int $carrierId, array $stops)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, array $shipperIds, int $carrierId, array $stops)
 * @method static dispatchSync(array $shipperIds, int $carrierId, array $stops)
 * @method static dispatchNow(array $shipperIds, int $carrierId, array $stops)
 * @method static dispatchAfterResponse(array $shipperIds, int $carrierId, array $stops)
 * @method static \App\Models\Shipments\Shipment run(array $shipperIds, int $carrierId, array $stops)
 */
class CreateShipment
{
}
namespace Lorisleiva\Actions\Concerns;

/**
 * @method void asController()
 */
trait AsController
{
}
/**
 * @method void asListener()
 */
trait AsListener
{
}
/**
 * @method void asJob()
 */
trait AsJob
{
}
/**
 * @method void asCommand(\Illuminate\Console\Command $command)
 */
trait AsCommand
{
}