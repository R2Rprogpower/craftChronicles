<?php

declare(strict_types=1);

namespace App\Modules\NewsFeed\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsSourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'news_topic_id' => ['required', 'integer', 'exists:news_topics,id'],
            'name' => ['required', 'string', 'max:160'],
            'language' => ['required', 'string', 'in:uk,ru,de,en'],
            'site_url' => ['required', 'url:http,https', 'max:2000'],
            'feed_url' => ['nullable', 'url:http,https', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $feedUrl = $this->input('feed_url');

        $this->merge([
            'name' => is_string($this->input('name')) ? trim($this->input('name')) : $this->input('name'),
            'site_url' => is_string($this->input('site_url')) ? trim($this->input('site_url')) : $this->input('site_url'),
            'feed_url' => is_string($feedUrl) && trim($feedUrl) !== '' ? trim($feedUrl) : null,
        ]);
    }
}
