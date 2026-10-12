<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

class LocationRequest extends FormRequest
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
        $location = $this->route('location');

        return [
            'department_id' => ['required', 'exists:departments,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('locations', 'name')
                    ->where(fn ($query) => $query->where('department_id', $this->input('department_id')))
                    ->ignore($location),
            ],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $location = $this->route('location');

            if (! $location) {
                return;
            }

            if (
                (int) $location->department_id !== (int) $this->input('department_id')
                && $location->equipment()->exists()
            ) {
                $validator->errors()->add(
                    'department_id',
                    'This location cannot be moved to another department while equipment is assigned to it.'
                );
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'This location already exists in the selected department.',
        ];
    }
}
