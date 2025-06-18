<?php

namespace App\Facades;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed get($id, string $alias)
 * @method static bool set($id, array $values)
 * 
 * @see \App\Services\AliasResolverService
 */
class AliasResolver extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'alias.resolver';
    }


} 