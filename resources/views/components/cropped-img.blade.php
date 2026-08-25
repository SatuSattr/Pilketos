@props(['src', 'alt' => '', 'crop' => null])

@php
    // Normalize crop: supports array, JSON string, or null (virtual crop — file untouched)
    $raw = $crop;
    if (is_string($raw)) {
        $decoded = json_decode($raw, true);
        $raw = is_array($decoded) ? $decoded : [];
    }
    $raw = is_array($raw) ? $raw : [];
    $x = isset($raw['x']) ? max(0, min(100, (float) $raw['x'])) : 50;
    $y = isset($raw['y']) ? max(0, min(100, (float) $raw['y'])) : 50;
    $zoom = isset($raw['zoom']) ? max(0.5, min(3, (float) $raw['zoom'])) : 1;
    $position = $x . '% ' . $y . '%';
    $origin = $position;
@endphp

<div {{ $attributes->merge(['class' => 'relative overflow-hidden']) }}>
    <img
        src="{{ $src }}"
        alt="{{ $alt }}"
        draggable="false"
        class="absolute inset-0 w-full h-full object-cover select-none"
        style="object-position: {{ $position }}; transform: scale({{ $zoom }}); transform-origin: {{ $origin }}; will-change: transform, object-position;"
        loading="lazy"
    />
</div>
