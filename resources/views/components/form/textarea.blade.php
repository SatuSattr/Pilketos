@props([
    'label' => '',
    'name' => '',
    'placeholder' => '',
    'required' => false,
    'rows' => 4,
    'hint' => null,
])

@php $hasError = $errors->has($name); @endphp

<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $name }}"
            class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">
            {{ $label }}
            @if ($required) <span class="text-danger">*</span> @endif
        </label>
    @endif

    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->except(['class', 'label', 'name', 'placeholder', 'required', 'rows', 'hint']) }}
        class="w-full rounded-xl border-2 px-4 py-2.5 text-sm text-accent bg-white
            placeholder:text-gray-400 transition-colors duration-200 resize-y
            focus:outline-none focus:ring-2 focus:ring-birupesat focus:ring-offset-1
            {{ $hasError ? 'border-error' : 'border-gray-200 focus:border-birupesat' }}"
    >{{ old($name) }}</textarea>

    @if ($hasError)
        <p class="mt-1 text-xs text-error flex items-center gap-1">
            <i data-lucide="alert-circle" class="w-3 h-3"></i>
            {{ $errors->first($name) }}
        </p>
    @elseif ($hint)
        <p class="mt-1 text-xs text-gray-500">{{ $hint }}</p>
    @endif
</div>
