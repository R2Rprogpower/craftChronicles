<?php

declare(strict_types=1);

namespace App\Modules\NewsFeed\Services;

use Carbon\CarbonImmutable;
use DOMDocument;
use Illuminate\Http\Client\Factory as HttpFactory;
use RuntimeException;
use SimpleXMLElement;
use Throwable;

class NewsFeedReader
{
    public function __construct(
        private readonly HttpFactory $http,
        private readonly PublicFeedUrl $urlGuard,
    ) {}

    /**
     * @return array{feed_url: string, items: list<array{title: string, url: string, published_at: CarbonImmutable|null}>}
     */
    public function read(string $siteUrl, ?string $feedUrl): array
    {
        $requestedUrl = $feedUrl ?: $siteUrl;
        $response = $this->fetch($requestedUrl);
        $body = $response['body'];
        $contentType = mb_strtolower($response['content_type']);

        if ($feedUrl === null && ! $this->looksLikeXml($body, $contentType)) {
            $feedUrl = $this->discoverFeedUrl($siteUrl, $body);

            if ($feedUrl === null) {
                throw new RuntimeException('RSS/Atom не найден. Укажи прямой URL ленты вручную.');
            }

            $body = $this->fetch($feedUrl)['body'];
        }

        $resolvedFeedUrl = $feedUrl ?: $siteUrl;

        return [
            'feed_url' => $resolvedFeedUrl,
            'items' => $this->parse($body, $resolvedFeedUrl),
        ];
    }

    /** @return array{body: string, content_type: string} */
    private function fetch(string $url): array
    {
        $this->urlGuard->assertSafe($url);

        $response = $this->http
            ->timeout(15)
            ->connectTimeout(6)
            ->retry(2, 250)
            ->withUserAgent('CraftChronicles News Feed/1.0')
            ->accept('application/rss+xml, application/atom+xml, application/xml, text/xml, text/html')
            ->get($url);

        if (! $response->successful()) {
            throw new RuntimeException("Источник ответил HTTP {$response->status()}.");
        }

        if (strlen($response->body()) > 5_000_000) {
            throw new RuntimeException('Ответ источника превышает допустимый размер.');
        }

        return [
            'body' => $response->body(),
            'content_type' => (string) $response->header('Content-Type'),
        ];
    }

    private function looksLikeXml(string $body, string $contentType): bool
    {
        $trimmed = ltrim($body);

        return str_contains($contentType, 'xml')
            || str_starts_with($trimmed, '<?xml')
            || str_starts_with($trimmed, '<rss')
            || str_starts_with($trimmed, '<feed')
            || str_starts_with($trimmed, '<rdf:RDF');
    }

    private function discoverFeedUrl(string $siteUrl, string $html): ?string
    {
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $loaded = $document->loadHTML($html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded) {
            return null;
        }

        foreach ($document->getElementsByTagName('link') as $link) {
            $rel = mb_strtolower($link->getAttribute('rel'));
            $type = mb_strtolower($link->getAttribute('type'));
            $href = trim($link->getAttribute('href'));

            if (str_contains($rel, 'alternate') && $href !== '' && (str_contains($type, 'rss') || str_contains($type, 'atom'))) {
                return $this->absoluteUrl($siteUrl, $href);
            }
        }

        return null;
    }

    /** @return list<array{title: string, url: string, published_at: CarbonImmutable|null}> */
    private function parse(string $body, string $feedUrl): array
    {
        $previous = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body, SimpleXMLElement::class, LIBXML_NONET | LIBXML_NOCDATA);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $xml instanceof SimpleXMLElement) {
            throw new RuntimeException('Источник вернул некорректный RSS/Atom.');
        }

        $items = [];
        $rssItems = $xml->channel->item;

        if (count($rssItems) === 0) {
            $rssItems = $xml->item;
        }

        if (count($rssItems) > 0) {
            foreach ($rssItems as $item) {
                $url = trim((string) $item->link);
                $title = $this->cleanTitle((string) $item->title);
                $date = trim((string) $item->pubDate);

                if ($date === '') {
                    $date = trim((string) $item->children('http://purl.org/dc/elements/1.1/')->date);
                }

                $this->appendItem($items, $title, $url, $date, $feedUrl);
            }

            return array_slice($items, 0, 200);
        }

        foreach ($xml->entry as $entry) {
            $url = '';

            foreach ($entry->link as $link) {
                $attributes = $link->attributes();
                $rel = (string) ($attributes['rel'] ?? 'alternate');

                if ($rel === 'alternate' || $url === '') {
                    $url = (string) ($attributes['href'] ?? '');
                }
            }

            $date = trim((string) $entry->published) ?: trim((string) $entry->updated);
            $this->appendItem($items, $this->cleanTitle((string) $entry->title), $url, $date, $feedUrl);
        }

        if ($items === []) {
            throw new RuntimeException('В RSS/Atom не найдено публикаций.');
        }

        return array_slice($items, 0, 200);
    }

    /**
     * @param  list<array{title: string, url: string, published_at: CarbonImmutable|null}>  $items
     */
    private function appendItem(array &$items, string $title, string $url, string $date, string $feedUrl): void
    {
        if ($title === '' || $url === '') {
            return;
        }

        $absoluteUrl = $this->absoluteUrl($feedUrl, html_entity_decode($url, ENT_QUOTES | ENT_HTML5));

        try {
            $publishedAt = $date === '' ? null : CarbonImmutable::parse($date);
        } catch (Throwable) {
            $publishedAt = null;
        }

        $items[] = [
            'title' => $title,
            'url' => $absoluteUrl,
            'published_at' => $publishedAt,
        ];
    }

    private function cleanTitle(string $title): string
    {
        return trim(html_entity_decode(strip_tags($title), ENT_QUOTES | ENT_HTML5));
    }

    private function absoluteUrl(string $baseUrl, string $url): string
    {
        if (preg_match('~^https?://~i', $url) === 1) {
            return $url;
        }

        $parts = parse_url($baseUrl);

        if (! is_array($parts) || ! isset($parts['scheme'], $parts['host'])) {
            return $url;
        }

        $origin = $parts['scheme'].'://'.$parts['host'].(isset($parts['port']) ? ':'.$parts['port'] : '');

        if (str_starts_with($url, '//')) {
            return $parts['scheme'].':'.$url;
        }

        if (str_starts_with($url, '/')) {
            return $origin.$url;
        }

        $path = $parts['path'] ?? '/';
        $directory = rtrim(str_replace('\\', '/', dirname($path)), '/');

        return $origin.($directory === '' ? '' : $directory).'/'.$url;
    }
}
