<?php

namespace App\Traits;

trait HasAliases
{
    /**
     * Expects the following
     * public string $aliasName = 'load'
     * OR
     * public function getAliasName() : string
     *
     * AND
     * public array $aliasProperties = ['number' => "load_number"]
     */

     public function getAliasName() : string {
        return $this->aliasName ?? strtolower(class_basename($this));
     }

}
