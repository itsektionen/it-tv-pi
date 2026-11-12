<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSlideshowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sections' => ['required', 'array', 'min:1'],
            'sections.*.type' => ['required', Rule::in(['image', 'clock', 'iframe'])],
            'sections.*.duration' => ['required', 'integer', 'min:1'],
            'sections.*.data' => ['nullable', 'array'],
            'sections.*.data.image' => ['exclude_unless:sections.*.type,image', 'required', 'uuid', 'exists:images,id'],
            'sections.*.data.url' => ['exclude_unless:sections.*.type,iframe', 'required', 'url:http,https', 'max:2048'],
            'sections.*.data.reload' => ['exclude_unless:sections.*.type,iframe', 'boolean'],
            'sections.*.data.caption' => ['exclude_unless:sections.*.type,image', 'nullable', 'string', 'max:255'],
            'sections.*.data.caption_position' => ['exclude_unless:sections.*.type,image', 'nullable', Rule::in(['above', 'below'])],
        ];
    }
}
