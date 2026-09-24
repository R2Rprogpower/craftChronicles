<?php

declare(strict_types=1);

namespace App\Modules\MovieTierList\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMovieTierListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'revision' => ['required', 'integer', 'min:1'],
            'movies' => ['required', 'array', 'max:500'],
            'movies.*.id' => ['required', 'string', 'max:80', 'regex:/^[A-Za-z0-9_-]+$/', 'distinct'],
            'movies.*.title' => ['required', 'string', 'max:200'],
            'movies.*.year' => ['nullable', 'integer', 'between:1888,2100'],
            'movies.*.genre' => ['nullable', 'string', 'max:80'],
            'movies.*.poster_url' => ['nullable', 'url:http,https', 'max:2048'],
            'movies.*.tier' => ['required', 'string', Rule::in(['S', 'A', 'B', 'C', 'D', 'F', 'unranked'])],
            'movies.*.position' => ['required', 'integer', 'min:0', 'max:499'],
        ];
    }
}
