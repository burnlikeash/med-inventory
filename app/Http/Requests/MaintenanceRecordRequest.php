<?php

namespace App\Http\Requests;

use App\Models\Equipment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MaintenanceRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canMaintainEquipment() === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'maintenance_type' => ['required', 'string', 'max:255'],
            'maintenance_date' => ['required', 'date'],
            'performed_by' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'result' => ['nullable', 'string', 'max:255'],
            'next_maintenance_date' => ['nullable', 'date', 'after_or_equal:maintenance_date'],
            'remarks' => ['nullable', 'string'],
            'equipment_status' => ['required', Rule::in(Equipment::OPERATIONAL_STATUSES)],
        ];
    }
}
