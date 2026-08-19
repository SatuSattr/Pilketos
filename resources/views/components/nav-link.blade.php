@props([
    'href' => '#',
    'active' => false,
])

<a href="{{ $href }}"
    class="flex items-center h-full px-3 text-sm font-medium transition-colors duration-150 border-b-2 -mb-px
        {{ $active
            ? 'bg-black/5 text-accent border-birupesat'
            : 'text-gray-600 border-transparent hover:bg-black/5 hover:text-accent' }}">
    {{ $slot }}
</a>
