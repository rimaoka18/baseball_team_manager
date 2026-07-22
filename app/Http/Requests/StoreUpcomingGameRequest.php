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
            'game_time' => 'nullable|date_format:H:i',
            'location' => 'required|string|max:255',
            'opponent' => 'required|string|max:255',

            'player_ids' => 'required|array|max:20',
            'player_ids.*' => 'nullable|integer|exists:players,id',

            'position' => 'required|array|max:20',
            'position.*' => 'nullable|string|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'player_ids.max' => '選手は最大20人まで登録できます',
            'player_ids.*.exists' => '選手一覧に存在しない選手が選択されています',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $this->validateUniquePlayerIds($validator);
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('game_time') && preg_match('/^\d{1,2}:\d{2}:\d{2}$/', (string) $this->game_time)) {
            $this->merge([
                'game_time' => substr((string) $this->game_time, 0, 5),
            ]);
        }
    }
}
