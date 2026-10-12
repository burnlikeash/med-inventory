<?php

namespace App\Http\Requests;

use App\Models\Equipment;
use App\Models\Location;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageInventory() === true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'barcode' => $this->filled('barcode') ? $this->barcode : null,
            'department_id' => $this->filled('department_id') ? $this->department_id : null,
            'location_id' => $this->filled('location_id') ? $this->location_id : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'equipment_code' => ['required', 'string', 'max:255', 'unique:equipment,equipment_code'],
            'barcode' => ['nullable', 'string', 'max:255', 'unique:equipment,barcode'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:equipment_categories,id'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'property_number' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'warranty_expiry' => ['nullable', 'date'],
            'status' => ['required', Rule::in(Equipment::OPERATIONAL_STATUSES)],
            'condition_status' => ['required', Rule::in(Equipment::CONDITIONS)],
            'lifecycle_status' => ['required', Rule::in(Equipment::EDITABLE_LIFECYCLE_STATUSES)],
            'department_id' => ['nullable', 'exists:departments,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'remarks' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->filled('location_id')) {
                return;
            }

            $location = Location::find($this->input('location_id'));

            if (
                ! $location
                || ! $this->filled('department_id')
                || (int) $location->department_id !== (int) $this->input('department_id')
            ) {
                $validator->errors()->add(
                    'location_id',
                    'The selected location does not belong to the selected department.'
                );
            }
        });
    }
}
