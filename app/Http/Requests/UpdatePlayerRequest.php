<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlayerRequest extends FormRequest
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
                Rule::unique('players', 'name')->ignore($this->route('player')),
            ],
            'jersey_number' => [
                'nullable',
                'integer',
                'min:0',
                'max:99',
                Rule::unique('players', 'jersey_number')->ignore($this->route('player')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '選手名を入力してください',
            'name.unique' => 'この選手名は既に登録されています',
            'jersey_number.integer' => '背番号は数字で入力してください',
            'jersey_number.min' => '背番号は0以上で入力してください',
            'jersey_number.max' => '背番号は99以下で入力してください',
            'jersey_number.unique' => 'この背番号は既に使われています',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $this->merge([
                'name' => trim(preg_replace('/[\s\x{3000}]+/u', ' ', (string) $this->input('name'))),
            ]);
        }

        if ($this->input('jersey_number') === '' || $this->input('jersey_number') === null) {
            $this->merge(['jersey_number' => null]);
        }
    }
}
