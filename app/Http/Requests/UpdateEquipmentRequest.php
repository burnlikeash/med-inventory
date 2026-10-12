<?php

namespace App\Http\Requests;

use App\Models\Equipment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageInventory() === true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'barcode' => $this->filled('barcode') ? $this->barcode : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $equipment = $this->route('equipment');

        return [
            'equipment_code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('equipment', 'equipment_code')->ignore($equipment),
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('equipment', 'barcode')->ignore($equipment),
            ],
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
            'remarks' => ['nullable', 'string'],
        ];
    }
}
