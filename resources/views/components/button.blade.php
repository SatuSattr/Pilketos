@props([
    'variant' => 'primary',   // primary | secondary | danger | success | ghost
    'size' => 'md',           // sm | md | lg
    'type' => 'button',
    'href' => null,
    'icon' => null,
    'iconRight' => null,
    'loading' => false,
])

@php
$base = 'inline-flex items-center justify-center gap-2 font-semibold rounded-2xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-birupesat focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed';

$variants = [
    'primary'   => 'bg-ink text-white hover:bg-accent shadow-sm hover:-translate-y-0.5',
    'secondary' => 'bg-white text-accent border-2 border-gray-200 hover:border-birupesat hover:text-birupesat',
    'danger'    => 'bg-danger text-white hover:bg-red-700 shadow-sm',
    'success'   => 'bg-success text-white hover:bg-emerald-600 shadow-sm',
    'ghost'     => 'text-gray-600 hover:bg-gray-100 hover:text-accent',
];

$sizes = [
    'sm' => 'px-3 py-1.5 text-xs',
    'md' => 'px-5 py-2.5 text-sm',
    'lg' => 'px-6 py-3 text-base',
];

$classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon) <i data-lucide="{{ $icon }}" class="w-4 h-4"></i> @endif
        {{ $slot }}
        @if ($iconRight) <i data-lucide="{{ $iconRight }}" class="w-4 h-4"></i> @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon) <i data-lucide="{{ $icon }}" class="w-4 h-4"></i> @endif
        {{ $slot }}
        @if ($iconRight) <i data-lucide="{{ $iconRight }}" class="w-4 h-4"></i> @endif
    </button>
@endif
