<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('players', 'name'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '選手名を入力してください',
            'name.unique' => 'この選手名は既に登録されています',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $this->merge([
                'name' => trim(preg_replace('/[\s\x{3000}]+/u', ' ', (string) $this->input('name'))),
            ]);
        }
    }
}
