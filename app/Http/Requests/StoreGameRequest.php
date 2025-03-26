<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGameRequest extends FormRequest
{
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
            'team_score' => 'required|integer|min:0',
            'opponent_score' => 'required|integer|min:0',

            'player_names' => 'required|array',
            'player_names.*' => 'required|string|max:255|regex:/^\S+\s\S+$/',

            'ab.*' => 'nullable|integer|min:0',
            'r.*' => 'nullable|integer|min:0',
            'h.*' => 'nullable|integer|min:0',
            'rbi.*' => 'nullable|integer|min:0',
            'hr.*' => 'nullable|integer|min:0',
            'bb.*' => 'nullable|integer|min:0',
            'k.*' => 'nullable|integer|min:0',

            'ip.*' => 'nullable|numeric|min:0',
            'ph.*' => 'nullable|integer|min:0',
            'pr.*' => 'nullable|integer|min:0',
            'er.*' => 'nullable|integer|min:0',
            'pbb.*' => 'nullable|integer|min:0',
            'pk.*' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'player_names.*.regex' => '姓と名を両方入力してください（例：山田 太郎）',
        ];
    }
}
