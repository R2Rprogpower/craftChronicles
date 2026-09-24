<?php

declare(strict_types=1);

namespace App\Modules\MovieTierList\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'tiers' => ['required', 'array', 'max:20'],
            'tiers.*.id' => ['required', 'string', 'max:80', 'regex:/^[A-Za-z0-9_-]+$/', 'distinct'],
            'tiers.*.label' => ['required', 'string', 'max:12'],
            'tiers.*.caption' => ['nullable', 'string', 'max:80'],
            'tiers.*.color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'tiers.*.position' => ['required', 'integer', 'min:0', 'max:19'],
            'movies' => ['required', 'array', 'max:500'],
            'movies.*.id' => ['required', 'string', 'max:80', 'regex:/^[A-Za-z0-9_-]+$/', 'distinct'],
            'movies.*.title' => ['required', 'string', 'max:200'],
            'movies.*.year' => ['nullable', 'integer', 'between:1888,2100'],
            'movies.*.genre' => ['nullable', 'string', 'max:80'],
            'movies.*.poster_url' => ['nullable', 'url:http,https', 'max:2048'],
            'movies.*.links' => ['present', 'array', 'max:2'],
            'movies.*.links.*' => ['required', 'url:http,https', 'max:2048'],
            'movies.*.tier' => ['required', 'string', 'max:80', 'regex:/^[A-Za-z0-9_-]+$/'],
            'movies.*.position' => ['required', 'integer', 'min:0', 'max:499'],
        ];
    }

    /** @return list<callable> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $tiers = $this->input('tiers', []);
            $movies = $this->input('movies', []);

            if (! is_array($tiers) || ! is_array($movies)) {
                return;
            }

            $tierIds = ['unranked'];

            foreach ($tiers as $tier) {
                if (is_array($tier) && is_string($tier['id'] ?? null)) {
                    $tierIds[] = $tier['id'];
                }
            }

            foreach ($movies as $index => $movie) {
                if (is_array($movie) && is_string($movie['tier'] ?? null) && ! in_array($movie['tier'], $tierIds, true)) {
                    $validator->errors()->add("movies.{$index}.tier", 'У фильма указан несуществующий тир.');
                }
            }
        }];
    }
}
