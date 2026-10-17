<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePresetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('data'))) {
            $this->merge(['data' => json_decode($this->input('data'), true)]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'data' => ['present', 'array'],
            'data.*.screen_group_id' => ['required', 'integer', 'exists:screen_groups,id'],
            'data.*.slideshow_id' => ['required', 'integer', 'exists:slideshows,id'],
        ];
    }

    public function effects(): array
    {
        return collect($this->validated('data'))
            ->map(fn ($effect) => [
                'screen_group_id' => (int) $effect['screen_group_id'],
                'slideshow_id' => (int) $effect['slideshow_id'],
            ])
            ->values()
            ->all();
    }
}
