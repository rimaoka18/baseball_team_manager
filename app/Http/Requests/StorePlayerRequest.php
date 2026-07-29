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
            'jersey_number' => [
                'nullable',
                'string',
                'regex:/^[0-9]{1,2}$/',
                Rule::unique('players', 'jersey_number'),
            ],
            'photo' => [
                'nullable',
                'image',
                'max:5120',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '選手名を入力してください',
            'name.unique' => 'この選手名は既に登録されています',
            'jersey_number.regex' => '背番号は0〜99の数字で入力してください',
            'jersey_number.unique' => 'この背番号は既に使われています',
            'photo.image' => '画像ファイルを選択してください',
            'photo.max' => '画像は5MB以下にしてください',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $this->merge([
                'name' => trim(preg_replace('/[\s\x{3000}]+/u', ' ', (string) $this->input('name'))),
            ]);
        }

        if ($this->has('jersey_number')) {
            $this->merge(['jersey_number' => trim((string) $this->input('jersey_number'))]);
        }

        if ($this->input('jersey_number') === '') {
            $this->merge(['jersey_number' => null]);
        }
    }
}
