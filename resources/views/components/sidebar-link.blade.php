@props([
    'href' => '#',
    'active' => false,
    'icon' => 'circle',
])

<a href="{{ $href }}"
    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200
    {{ $active
        ? 'bg-birupesat text-white'
        : 'text-gray-600 hover:bg-gray-100 hover:text-accent' }}">
    <i data-lucide="{{ $icon }}" class="w-4 h-4 shrink-0"></i>
    {{ $slot }}
</a>
