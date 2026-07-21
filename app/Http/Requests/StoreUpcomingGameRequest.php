<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesUniquePlayerNames;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreUpcomingGameRequest extends FormRequest
{
    use ValidatesUniquePlayerNames;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'game_date' => 'required|date',
            'location' => 'required|string|max:255',
            'opponent' => 'required|string|max:255',

            'player_names' => 'required|array|max:20',
            'player_names.*' => 'nullable|string|max:255|regex:/^\S+[\s\x{3000}]\S+$/u',

            'position' => 'required|array|max:20',
            'position.*' => 'nullable|string|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'player_names.*.regex' => '姓と名を両方入力してください（例：山田 太郎）',
            'player_names.max' => '選手は最大20人まで登録できます',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $this->validateUniquePlayerNames($validator);
    }
}
