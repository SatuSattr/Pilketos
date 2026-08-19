<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login — Pilketos Admin</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-primary min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-sm">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <img src="{{ asset('storage/assets/logo.png') }}" alt="Pilketos"
                class="h-16 w-auto object-contain mx-auto mb-4">
            <h1 class="text-2xl font-bold text-accent">Pilketos</h1>
            <p class="text-sm text-gray-500 mt-1">Masuk ke panel administrasi</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-xl border-2 border-gray-200 shadow-lg p-6">
            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-4">
                @csrf

                <x-form.input name="email" label="Email" type="email" placeholder="admin@pilketos.local"
                    :required="true" />

                <x-form.input name="password" label="Password" type="password" placeholder="••••••••"
                    :required="true" />

                @if ($errors->has('email'))
                    <p class="text-xs text-error flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3 h-3"></i>
                        {{ $errors->first('email') }}
                    </p>
                @endif

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="remember" name="remember"
                        class="w-4 h-4 rounded border-gray-300 text-birupesat focus:ring-birupesat">
                    <label for="remember" class="text-sm text-gray-600">Ingat saya</label>
                </div>

                <x-button type="submit" variant="primary" class="w-full" icon="log-in">
                    Masuk
                </x-button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            Pilketos v2.0 &mdash; Sistem Pemilihan Ketua OSIS
        </p>
    </div>
</body>

</html>
