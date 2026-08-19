@props([
    'title' => '',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'mb-6']) }}>
    <div class="flex items-center gap-3 mb-1">
        <span class="w-1 h-6 rounded-full bg-birupesat inline-block"></span>
        <h1 class="text-xl lg:text-2xl font-bold text-accent">{{ $title }}</h1>
    </div>
    @if ($description)
        <p class="text-sm text-gray-500 ml-4">{{ $description }}</p>
    @endif
</div>
