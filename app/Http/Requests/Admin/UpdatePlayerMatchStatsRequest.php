<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlayerMatchStatsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'minutes' => ['required', 'integer', 'min:0', 'max:120'],
            'goals' => ['nullable', 'integer', 'min:0'],
            'assists' => ['nullable', 'integer', 'min:0'],
            'shots' => ['nullable', 'integer', 'min:0'],
            'saves' => ['nullable', 'integer', 'min:0'],
            'yellow' => ['nullable', 'integer', 'min:0', 'max:2'],
            'red' => ['nullable', 'integer', 'min:0', 'max:1'],
            'clean_sheet' => ['nullable', 'boolean'],
            'conceded' => ['nullable', 'integer', 'min:0'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:10'],
        ];
    }
}