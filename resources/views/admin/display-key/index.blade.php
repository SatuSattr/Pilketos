<x-layouts.admin title="Display Keys">
    <div
        x-data="{
            panel: null,
            openCreate() { this.panel = 'create'; },
            close() { this.panel = null; }
        }"
        @keydown.escape.window="close()"
    >
        {{-- Page header --}}
        <div class="flex items-center justify-between mb-6">
            <x-page-header title="Display Keys" description="Kelola key untuk akses halaman voting di setiap bilik" class="mb-0" />
            <x-button @click="openCreate()" variant="primary" icon="plus">
                Buat Key Baru
            </x-button>
        </div>

        @if ($displayKeys->isEmpty())
            <div class="bg-white rounded-xl border-2 border-gray-200 shadow-lg p-12 text-center">
                <i data-lucide="key-round" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                <p class="text-gray-500 font-medium">Belum ada display key.</p>
                <x-button @click="openCreate()" variant="primary" icon="plus" class="mt-4">
                    Buat Key Pertama
                </x-button>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @foreach ($displayKeys as $key)
                    <div class="bg-white rounded-xl border-2 {{ $key->is_active ? 'border-gray-200' : 'border-gray-100 opacity-70' }} shadow-lg p-5">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <p class="font-bold text-accent">{{ $key->nama }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <code class="text-sm font-mono bg-birupesat/10 text-birupesat px-2 py-0.5 rounded-lg">
                                        {{ $key->key }}
                                    </code>
                                    @if ($key->is_active)
                                        <x-badge color="green">Aktif</x-badge>
                                    @else
                                        <x-badge color="gray">Nonaktif</x-badge>
                                    @endif
                                </div>
                            </div>
                            <div class="flex gap-1">
                                <form method="POST" action="{{ route('admin.display-key.toggle', $key) }}">
                                    @csrf
                                    <x-button type="submit" variant="ghost" size="sm"
                                        icon="{{ $key->is_active ? 'pause' : 'play' }}"
                                        class="{{ $key->is_active ? 'text-warning' : 'text-success' }}">
                                        {{ $key->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </x-button>
                                </form>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="bg-success/10 rounded-lg p-3 text-center">
                                <p class="text-xl font-bold text-success">{{ $key->successful_votes }}</p>
                                <p class="text-xs text-gray-500 mt-0.5 flex items-center justify-center gap-1">
                                    <i data-lucide="circle-check" class="w-3 h-3"></i> Vote berhasil
                                </p>
                            </div>
                            <div class="bg-danger/10 rounded-lg p-3 text-center">
                                <p class="text-xl font-bold text-danger">{{ $key->failed_attempts }}</p>
                                <p class="text-xs text-gray-500 mt-0.5 flex items-center justify-center gap-1">
                                    <i data-lucide="circle-x" class="w-3 h-3"></i> Percobaan gagal
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
                            <form method="POST" action="{{ route('admin.display-key.reset-stats', $key) }}">
                                @csrf
                                <x-button
                                    type="button"
                                    variant="ghost" size="sm" icon="rotate-ccw"
                                    @click="adminConfirm($event, 'Reset Statistik?', 'Statistik key {{ addslashes($key->nama) }} akan direset ke 0.', 'Ya, Reset', 'warning')">
                                    Reset Stats
                                </x-button>
                            </form>
                            <form method="POST" action="{{ route('admin.display-key.destroy', $key) }}">
                                @csrf @method('DELETE')
                                <x-button
                                    type="button"
                                    variant="ghost" size="sm" icon="trash-2"
                                    class="text-danger hover:bg-danger/10"
                                    @click="adminConfirm($event, 'Hapus Key?', 'Key {{ addslashes($key->nama) }} dan semua data vote terkait akan dihapus permanen.', 'Ya, Hapus')">
                                    Hapus
                                </x-button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Backdrop --}}
        <div
            x-show="panel !== null"
            x-transition:enter="transition duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="close()"
            class="fixed inset-0 bg-black/40 z-40"
            x-cloak>
        </div>

        {{-- Slide-over panel --}}
        <div
            x-show="panel !== null"
            x-transition:enter="transition duration-300 ease-out"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition duration-200 ease-in"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="fixed inset-y-0 right-0 w-full max-w-md bg-white shadow-2xl z-50 flex flex-col"
            x-cloak>

            {{-- Panel header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 shrink-0">
                <h2 class="text-base font-bold text-accent">Buat Display Key</h2>
                <button @click="close()" class="text-gray-400 hover:text-accent transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            {{-- CREATE form --}}
            <div class="flex-1 overflow-y-auto p-6">
                <p class="text-sm text-gray-500 mb-4">
                    Key ini digunakan untuk mengakses halaman voting di bilik pemilihan.
                    Setiap bilik sebaiknya menggunakan key yang berbeda.
                </p>
                <form method="POST" action="{{ route('admin.display-key.store') }}" class="space-y-4">
                    @csrf
                    <x-form.input
                        name="nama"
                        label="Nama / Label"
                        placeholder="Bilik 1"
                        :required="true"
                        hint="Contoh: Bilik 1, Ruang A, Laptop Panitia"
                    />
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">
                            Key <span class="text-danger">*</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="text" name="key" id="key-input"
                                value="{{ old('key', $suggestedKey) }}"
                                required maxlength="50"
                                class="flex-1 rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-mono text-accent bg-white
                                    focus:outline-none focus:ring-2 focus:ring-birupesat focus:border-birupesat uppercase">
                            <x-button type="button" variant="secondary" size="sm" icon="refresh-cw"
                                onclick="document.getElementById('key-input').value = Math.random().toString(36).substring(2,10).toUpperCase()">
                                Generate
                            </x-button>
                        </div>
                        @error('key')
                            <p class="mt-1 text-xs text-error">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Huruf dan angka saja. Akan otomatis diubah ke huruf kapital.</p>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <x-button type="submit" variant="primary" icon="key-round">Buat Key</x-button>
                        <x-button type="button" @click="close()" variant="secondary">Batal</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
