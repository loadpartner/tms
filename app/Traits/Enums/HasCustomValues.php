<?php

namespace App\Traits\Enums;

use App\Models\EnumCustomValue;

trait HasCustomValues
{
    public function getAll() {
        // Get all default enum values
        $defaultValues = collect(static::cases())->mapWithKeys(function ($case) {
            return [$case->value => $case->name];
        });
        
        // Get custom values for this enum class
        $customValues = EnumCustomValue::where('enum_class', static::class)
            ->get()
            ->mapWithKeys(function ($customValue) {
                return [$customValue->value => $customValue->label];
            });
        
        // Merge custom values with default values (custom values override defaults)
        return $defaultValues->merge($customValues);
    }

    public function getAllValues() {
        return $this->getAll()->keys()->all();
    }
}
