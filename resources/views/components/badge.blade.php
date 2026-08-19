@props([
    'color' => 'gray',  // gray | blue | green | red | yellow | purple
])

@php
$colors = [
    'gray'   => 'bg-gray-100 text-gray-700',
    'blue'   => 'bg-birupesat/10 text-birupesat',
    'green'  => 'bg-success/10 text-success',
    'red'    => 'bg-danger/10 text-danger',
    'yellow' => 'bg-warning/10 text-yellow-700',
    'purple' => 'bg-purple-100 text-purple-700',
];
$classes = 'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold tracking-wide ' . ($colors[$color] ?? $colors['gray']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
