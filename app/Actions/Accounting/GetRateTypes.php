<?php

namespace App\Actions\Accounting;

use App\Http\Resources\Accounting\RateTypeResource;
use App\Models\Accounting\RateType;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetRateTypes {
    use AsAction;

    public function handle() : Collection
    {
        return RateType::all();
    }

    public function asController()
    {
        return $this->handle();
    }

    public function jsonResponse(Collection $rateTypes)
    {
        return response()->json(RateTypeResource::collection($rateTypes));
    }
}