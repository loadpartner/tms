<?php

namespace App\Enums\Accounting;


enum Currency: string
{
    // name => code
    case UNITED_STATES_DOLLAR = 'usd';


    public function symbol() {
        return match($this) {
            static::UNITED_STATES_DOLLAR => '$'
        };
    }
}