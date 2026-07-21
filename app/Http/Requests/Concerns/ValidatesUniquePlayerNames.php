<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Contracts\Validation\Validator;

trait ValidatesUniquePlayerNames
{
    /**
     * Reject player_names entries that refer to the same person more than
     * once in this submission. Full-width (　) and half-width ( ) spaces
     * are treated as equivalent so "今岡　稜" and "今岡 稜" do not create
     * duplicate Player records.
     */
    protected function validateUniquePlayerNames(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $seen = [];

            foreach ($this->input('player_names', []) as $index => $name) {
                $normalized = $this->normalizePlayerName($name);

                if ($normalized === '') {
                    continue;
                }

                if (isset($seen[$normalized])) {
                    $validator->errors()->add(
                        "player_names.$index",
                        "「{$name}」は既に入力されています（表記ゆれを含む重複はできません）"
                    );
                }

                $seen[$normalized] = true;
            }
        });
    }

    private function normalizePlayerName(?string $name): string
    {
        if ($name === null) {
            return '';
        }

        // Collapse full-width and half-width whitespace runs to a single
        // half-width space, then trim, so "今岡　稜" and "今岡 稜" compare equal.
        return trim(preg_replace('/[\s\x{3000}]+/u', ' ', $name));
    }
}
