<?php

namespace App\Http\Requests;

use App\Models\Organizations\OrganizationInvite;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizationInviteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', OrganizationInvite::find($this->route('invite')));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
