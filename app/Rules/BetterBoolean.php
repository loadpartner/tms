<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BetterBoolean implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $acceptableTrue = ['1', 1, 'true', 't', 'y', 'yes', true];
        $acceptableFalse = ['0', 0, 'false', 'f', 'n', 'no', false];

        $valueLower = is_string($value) ? strtolower($value) : $value;

        if (!in_array($valueLower, array_merge($acceptableTrue, $acceptableFalse), true)) {
            $fail("The $attribute field must be a boolean value (accepted: t, f, true, false, 0, 1, y, n, yes, no).");
        }
    }
}
