<?php

namespace App\Services;

use InvalidArgumentException;

class LessonVideoService
{
    /**
     * @return array<int, string>
     */
    public function providerKeys(): array
    {
        $providers = config('lesson_video.providers', []);

        if (! is_array($providers)) {
            return [];
        }

        return array_keys($providers);
    }

    public function getProviderLabel(string $provider): string
    {
        $providers = config('lesson_video.providers', []);

        if (is_array($providers) && isset($providers[$provider]['label']) && is_string($providers[$provider]['label'])) {
            return $providers[$provider]['label'];
        }

        return ucfirst($provider);
    }

    /**
     * @return array<string, mixed>
     */
    public function buildPayload(?string $provider, ?string $source, ?int $startAt, bool $isPreview): array
    {
        $source = trim((string) $source);

        if ($source === '') {
            return [
                'video_provider' => null,
                'video_source' => null,
                'video_id' => null,
                'video_start_at' => 0,
                'video_is_preview' => false,
            ];
        }

        $resolvedProvider = $this->normalizeProvider($provider);
        $resolvedStart = max(0, (int) ($startAt ?? 0));

        if ($resolvedProvider === 'youtube') {
            $videoId = $this->extractYouTubeVideoId($source);
            if ($videoId === null) {
                throw new InvalidArgumentException('Invalid YouTube URL or video ID.');
            }

            return [
                'video_provider' => $resolvedProvider,
                'video_source' => $source,
                'video_id' => $videoId,
                'video_start_at' => $resolvedStart,
                'video_is_preview' => $isPreview,
            ];
        }

        throw new InvalidArgumentException('Unsupported video provider.');
    }

    public function embedUrl(?string $provider, ?string $videoId, ?int $startAt): ?string
    {
        if (! is_string($provider) || trim($provider) === '' || ! is_string($videoId) || trim($videoId) === '') {
            return null;
        }

        if ($provider !== 'youtube') {
            return null;
        }

        $query = [
            'rel' => 0,
            'modestbranding' => 1,
            'playsinline' => 1,
            'iv_load_policy' => 3,
        ];

        $resolvedStart = max(0, (int) ($startAt ?? 0));
        if ($resolvedStart > 0) {
            $query['start'] = $resolvedStart;
        }

        $queryString = http_build_query($query);

        return 'https://www.youtube-nocookie.com/embed/'.$videoId.'?'.$queryString;
    }

    private function normalizeProvider(?string $provider): string
    {
        $provider = trim((string) $provider);

        if ($provider === '') {
            $provider = (string) config('lesson_video.default_provider', 'youtube');
        }

        if (! in_array($provider, $this->providerKeys(), true)) {
            throw new InvalidArgumentException('Unsupported video provider.');
        }

        return $provider;
    }

    private function extractYouTubeVideoId(string $source): ?string
    {
        $source = trim($source);

        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $source) === 1) {
            return $source;
        }

        $parts = parse_url($source);
        if (! is_array($parts)) {
            return null;
        }

        $host = strtolower((string) ($parts['host'] ?? ''));
        $path = trim((string) ($parts['path'] ?? ''), '/');
        $query = (string) ($parts['query'] ?? '');

        if ($host === 'youtu.be' || $host === 'www.youtu.be') {
            $candidate = explode('/', $path)[0] ?? '';

            return preg_match('/^[A-Za-z0-9_-]{11}$/', $candidate) === 1 ? $candidate : null;
        }

        $youtubeHosts = [
            'youtube.com',
            'www.youtube.com',
            'm.youtube.com',
            'music.youtube.com',
            'www.youtube-nocookie.com',
            'youtube-nocookie.com',
        ];

        if (! in_array($host, $youtubeHosts, true)) {
            return null;
        }

        parse_str($query, $queryParams);
        if (isset($queryParams['v']) && is_string($queryParams['v']) && preg_match('/^[A-Za-z0-9_-]{11}$/', $queryParams['v']) === 1) {
            return $queryParams['v'];
        }

        $segments = explode('/', $path);
        if ((isset($segments[0]) && $segments[0] === 'embed' && isset($segments[1])) ||
            (isset($segments[0]) && $segments[0] === 'shorts' && isset($segments[1])) ||
            (isset($segments[0]) && $segments[0] === 'live' && isset($segments[1]))) {
            $candidate = $segments[1];

            return preg_match('/^[A-Za-z0-9_-]{11}$/', $candidate) === 1 ? $candidate : null;
        }

        return null;
    }
}
