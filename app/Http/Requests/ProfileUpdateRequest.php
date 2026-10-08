<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->no_whatsapp) {
            $rawWa = preg_replace('/[^0-9]/', '', $this->no_whatsapp);
            if (str_starts_with($rawWa, '0')) {
                $normalizedWa = '62' . substr($rawWa, 1);
            } elseif (str_starts_with($rawWa, '8')) {
                $normalizedWa = '62' . $rawWa;
            } elseif (str_starts_with($rawWa, '62')) {
                $normalizedWa = $rawWa;
            } else {
                $normalizedWa = '62' . $rawWa;
            }
            $this->merge(['no_whatsapp' => $normalizedWa]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'no_whatsapp' => ['nullable', 'string', 'regex:/^628[0-9]{8,13}$/'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'hapus_foto' => ['nullable', 'boolean'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }
}

