<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    public function notes() {
        return $this->morphMany(Note::class, 'notable');
    }
} 