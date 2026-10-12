<?php

namespace App\Http\Requests;

use App\Models\Location;
use Illuminate\Foundation\Http\FormRequest;

class StoreEquipmentMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageInventory() === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'department_id' => ['required', 'exists:departments,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'reason' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $equipment = $this->route('equipment');
            $location = Location::find($this->input('location_id'));

            if (
                $location
                && (int) $location->department_id !== (int) $this->input('department_id')
            ) {
                $validator->errors()->add(
                    'location_id',
                    'The selected location does not belong to the selected department.'
                );
            }

            if (
                $equipment
                && (int) $equipment->department_id === (int) $this->input('department_id')
                && (int) $equipment->location_id === (int) $this->input('location_id')
            ) {
                $validator->errors()->add(
                    'location_id',
                    'The equipment is already assigned to this location.'
                );
            }
        });
    }
}
