<?php

use App\Models\Organizations\Organization;
use Illuminate\Support\Facades\Context;


// Gets the current organization id based on first the context, then the current user
function current_organization_id(): ?int
{
    return Context::getHidden('current_organization_id') ?? auth()->user()?->current_organization_id;
}

// Gets the current organization based on current_organization_id
function current_organization(): ?Organization
{
    return Organization::find(current_organization_id());
}


// Set the context organization id, which overrides the current user's organization id
function set_context_organization(int $organization_id): void
{
    Context::addHidden('current_organization_id', $organization_id);
}

// clear the context organization id
function clear_context_organization(): void
{
    Context::forgetHidden('current_organization_id');
}

// run a closure with a specific organization id set in the context
function run_with_organization(int $organization_id, Closure $closure): mixed
{
    set_context_organization($organization_id);
    $result = $closure();
    clear_context_organization();
    return $result;
}