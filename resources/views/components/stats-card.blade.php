@props([
    'title' => '',
    'value' => '',
    'icon' => 'activity',
    'color' => 'blue',   // blue | green | red | yellow
    'sub' => null,
])

@php
$colorMap = [
    'blue'   => ['icon_bg' => 'bg-birupesat/10', 'icon_text' => 'text-birupesat'],
    'green'  => ['icon_bg' => 'bg-success/10',   'icon_text' => 'text-success'],
    'red'    => ['icon_bg' => 'bg-danger/10',     'icon_text' => 'text-danger'],
    'yellow' => ['icon_bg' => 'bg-warning/10',    'icon_text' => 'text-yellow-600'],
];
$c = $colorMap[$color] ?? $colorMap['blue'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border-2 border-gray-200 shadow-lg p-5']) }}>
    <div class="flex items-start justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">{{ $title }}</p>
            <p class="text-2xl lg:text-3xl font-bold text-accent" data-stat-value>{{ $value }}</p>
            @if ($sub)
                <p class="text-xs text-gray-500 mt-1" data-stat-sub>{{ $sub }}</p>
            @endif
        </div>
        <div class="w-10 h-10 rounded-xl {{ $c['icon_bg'] }} flex items-center justify-center shrink-0">
            <i data-lucide="{{ $icon }}" class="w-5 h-5 {{ $c['icon_text'] }}"></i>
        </div>
    </div>
</div>
