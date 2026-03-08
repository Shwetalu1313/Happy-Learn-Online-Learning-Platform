@props([
    'src' => null,
    'name' => 'User',
    'size' => 40,
    'imgClass' => '',
])

@php
    $displayName = trim((string) $name);
    $parts = preg_split('/\s+/', $displayName, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $initials = collect($parts)
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
    $initials = $initials !== '' ? $initials : 'NA';
    $source = is_string($src) ? trim($src) : '';
    $hasSource = $source !== '';
@endphp

<span {{ $attributes->class(['initials-avatar', 'show-fallback' => ! $hasSource]) }} style="--ia-size: {{ (int) $size }}px;">
    @if($hasSource)
        <img
            src="{{ $source }}"
            alt="{{ $displayName }}"
            class="initials-avatar__img {{ $imgClass }}"
            onerror="this.parentElement.classList.add('show-fallback');"
        >
    @endif
    <span class="initials-avatar__fallback">{{ $initials }}</span>
</span>
