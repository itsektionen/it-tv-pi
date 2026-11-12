<?php

namespace App\Http\Requests;

use App\Models\Media;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
            'sections.*.type' => ['required', Rule::in(['media', 'clock', 'iframe'])],
            'sections.*.duration' => ['nullable', 'integer', 'min:1'],
            'sections.*.data' => ['nullable', 'array'],
            'sections.*.data.media' => ['exclude_unless:sections.*.type,media', 'required', 'uuid', 'exists:media,id'],
            'sections.*.data.url' => ['exclude_unless:sections.*.type,iframe', 'required', 'url:http,https', 'max:2048'],
            'sections.*.data.reload' => ['exclude_unless:sections.*.type,iframe', 'boolean'],
            'sections.*.data.caption' => ['exclude_unless:sections.*.type,media', 'nullable', 'string', 'max:255'],
            'sections.*.data.caption_position' => ['exclude_unless:sections.*.type,media', 'nullable', Rule::in(['above', 'below'])],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $sections = collect($this->input('sections'))->whereNull('duration');
                $videos = Media::where('type', 'video')->whereNotNull('duration')
                    ->whereIn('id', $sections->where('type', 'media')->pluck('data.media'))
                    ->pluck('id');

                foreach ($sections as $index => $section) {
                    if ($section['type'] !== 'media' || !$videos->contains($section['data']['media'])) {
                        $validator->errors()->add("sections.{$index}.duration", 'A duration is required unless the slide plays a video.');
                    }
                }
            },
        ];
    }
}
