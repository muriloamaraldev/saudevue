<?php

namespace App\Http\Requests;

use App\Models\Patient;
use App\Rules\Cpf;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientRequest extends FormRequest
{
    /**
     * Normaliza o CPF para 11 dígitos antes da validação, de modo que a
     * regra de unicidade compare sempre o valor canônico.
     */
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('cpf'))) {
            $this->merge(['cpf' => Cpf::normalizar($this->input('cpf'))]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'cpf' => [
                'required',
                'string',
                'size:11',
                new Cpf,
                Rule::unique(Patient::class, 'cpf')
                    ->ignore($this->route('patient'))
                    ->where('user_id', $this->user()->id),
            ],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'phone' => ['nullable', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
