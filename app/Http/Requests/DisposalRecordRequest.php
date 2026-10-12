<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DisposalRecordRequest extends FormRequest
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
            'reason' => ['required', 'string'],
            'disposal_date' => ['required', 'date'],
            'disposal_method' => ['nullable', 'string', 'max:255'],
            'disposal_reference' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
