<?php

declare(strict_types=1);

namespace App\Modules\GratitudeVariants\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGratitudeVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:120'],
            'language' => ['required', 'string', 'in:ru,en,de,uk'],
            'body' => ['required', 'string', 'min:10', 'max:10000'],
            'tags' => ['present', 'array', 'max:20'],
            'tags.*' => ['required', 'string', 'max:40', 'regex:/^#?[\pL\pN][\pL\pN_\x{0027}\x{2019}-]*$/u'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $tags = $this->input('tags', []);

        if (! is_array($tags)) {
            return;
        }

        $normalized = [];

        foreach ($tags as $tag) {
            if (! is_string($tag)) {
                continue;
            }

            $tag = mb_strtolower(ltrim(trim($tag), '#'));

            if ($tag !== '') {
                $normalized[] = $tag;
            }
        }

        $this->merge(['tags' => array_values(array_unique($normalized))]);
    }
}
