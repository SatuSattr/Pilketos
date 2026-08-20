<!doctype html>
<html lang="id">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>{{ $title ?? 'Dashboard' }} — Pilketos Admin</title>
        <link rel="preload" as="image" href="{{ asset('storage/assets/logo.png') }}">
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
        <body class="bg-gray-50 font-sans"
            data-flash-type="{{ session('toast_type', session('success') ? 'success' : (session('error') ? 'error' : '')) }}"
            data-flash-msg="{{ session('toast_msg', session('success') ?? session('error') ?? '') }}">
        <div class="min-h-screen flex flex-col">

            {{-- Navbar --}}
            <header class="sticky top-0 z-30 bg-secondary border-b border-gray-200" x-data="{ mobileOpen: false }">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center h-14 gap-6">

                        {{-- Logo + Brand --}}
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 shrink-0">
                            <img src="{{ asset('storage/assets/logo.png') }}" alt="Pilketos" class="h-8 w-auto object-contain">
                            <div class="flex flex-col leading-tight">
                                <span class="text-base font-bold text-accent">Pilketos</span>
                                <span class="text-[10px] text-gray-400 font-medium">Stable v2.0</span>
                            </div>
                        </a>

                        {{-- Desktop nav links --}}
                        @php
                            $navLinks = [
                                ['route' => 'admin.dashboard',         'match' => 'admin.dashboard',      'label' => 'Dashboard'],
                                ['route' => 'admin.calon.index',       'match' => 'admin.calon.*',        'label' => 'Calon'],
                                ['route' => 'admin.voter.index',       'match' => 'admin.voter.*',        'label' => 'Daftar Pemilih'],
                                ['route' => 'admin.display-key.index', 'match' => 'admin.display-key.*', 'label' => 'Display Keys'],
                            ];
                        @endphp
                        <nav class="hidden md:flex items-stretch h-full gap-0.5">
                            @foreach ($navLinks as $link)
                                <x-nav-link
                                    :href="route($link['route'])"
                                    :active="request()->routeIs($link['match'])">
                                    {{ $link['label'] }}
                                </x-nav-link>
                            @endforeach
                        </nav>

                        {{-- Spacer --}}
                        <div class="flex-1"></div>

                        {{-- Account + Logout --}}
                        <div class="hidden md:flex items-center gap-4">
                            <span class="text-sm text-gray-500">{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit"
                                    class="text-sm text-gray-500 hover:text-danger transition-colors duration-150">
                                    Keluar
                                </button>
                            </form>
                        </div>

                        {{-- Mobile hamburger --}}
                        <button @click="mobileOpen = !mobileOpen"
                            class="md:hidden text-gray-500 hover:text-accent transition-colors">
                            <i data-lucide="menu" class="w-5 h-5" x-show="!mobileOpen"></i>
                            <i data-lucide="x" class="w-5 h-5" x-show="mobileOpen" x-cloak></i>
                        </button>
                    </div>
                </div>

                {{-- Mobile menu --}}
                <div x-show="mobileOpen" x-cloak
                    x-transition:enter="transition duration-150 ease-out"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition duration-100 ease-in"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    class="md:hidden border-t border-gray-200 bg-secondary">
                    <div class="max-w-7xl mx-auto px-4 py-3 flex flex-col gap-0.5">
                        @foreach ($navLinks as $link)
                            @php $isActive = request()->routeIs($link['match']); @endphp
                            <a href="{{ route($link['route']) }}"
                                class="px-3 py-2 text-sm font-medium border-l-2 transition-colors duration-150
                                    {{ $isActive
                                        ? 'bg-black/5 text-accent border-birupesat'
                                        : 'text-gray-600 border-transparent hover:bg-black/5 hover:text-accent' }}">
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                        <div class="border-t border-gray-200 mt-2 pt-2 flex items-center justify-between">
                            <span class="text-sm text-gray-500">{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit"
                                    class="text-sm text-gray-500 hover:text-danger transition-colors duration-150">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>



            {{-- Main content --}}
            <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6">
                {{ $slot }}
            </main>

            <footer class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-4 border-t border-gray-200 text-xs text-gray-500 text-center">
                Pilketos v2.0 &mdash; Admin Panel
            </footer>
        </div>
    </body>
</html>
