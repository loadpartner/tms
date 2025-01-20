<?php

namespace App\Http\Requests\Shipments;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'shipper_ids' => ['required', 'array'],
            // 'shipper_ids.*' => ['required', 'exists:shippers,id'],
            
            'shipper_ids' => ['required', 'exists:shippers,id'],

            'carrier_id' => ['required', 'exists:carriers,id'],
            'stops' => ['required', 'array'],
            'stops.*.stop_type' => ['required', 'in:pickup,delivery'],
            'stops.*.facility_id' => ['required', 'exists:facilities,id'],
            'stops.*.appointment.datetime' => ['required', 'date'],
        ];
    }
}
