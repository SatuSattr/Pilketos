<x-layouts.admin title="Daftar Pemilih">
    <div
        x-data="{
            panel: null,
            editData: {},
            openCreate() { this.panel = 'create'; },
            openEdit(data) { this.editData = data; this.panel = 'edit'; },
            openImport() { this.panel = 'import'; },
            close() { this.panel = null; }
        }"
        @keydown.escape.window="close()"
    >
        {{-- Page header --}}
        <div class="flex items-center justify-between mb-6">
            <x-page-header title="Daftar Pemilih" description="Kelola daftar hak suara" class="mb-0" />
            <div class="flex gap-2">
                <x-button @click="openCreate()" variant="secondary" size="sm" icon="user-plus">
                    Tambah
                </x-button>
                <x-button @click="openImport()" variant="primary" size="sm" icon="upload">
                    Import
                </x-button>
            </div>
        </div>

        {{-- Search + Filter --}}
        <form method="GET" class="mb-4 flex flex-row items-center gap-2">
            <div class="relative flex-1 max-w-sm">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama pemilih..."
                    class="w-full pl-9 pr-4 py-2.5 rounded-xl border-2 border-gray-200 text-sm text-accent bg-white
                        focus:outline-none focus:ring-2 focus:ring-birupesat focus:border-birupesat">
            </div>
            <div x-data="{ open: false, value: '{{ $status }}' }" class="relative shrink-0">
                <input type="hidden" name="status" :value="value">
                <button type="button" @click="open = !open"
                    class="flex items-center justify-between gap-2 w-full sm:w-44 rounded-xl border-2 border-gray-200 bg-white pl-4 pr-3 py-2.5 text-sm font-medium text-accent
                        focus:outline-none focus:ring-2 focus:ring-birupesat focus:border-birupesat transition-colors"
                    :class="open && 'border-birupesat ring-2 ring-birupesat'">
                    <span x-text="value === 'sudah' ? 'Sudah Memilih' : value === 'belum' ? 'Belum Memilih' : 'Semua'"></span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200" :class="open && 'rotate-180'"></i>
                </button>
                <div x-show="open" x-cloak
                    @click.outside="open = false"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-xl border-2 border-gray-200 shadow-lg overflow-hidden z-20">
                    <button type="button"
                        @click="value = 'semua'; open = false; $nextTick(() => $el.closest('form').submit())"
                        class="w-full text-left px-4 py-2.5 text-sm font-medium transition-colors"
                        :class="value === 'semua' ? 'bg-birupesat text-white' : 'text-accent hover:bg-gray-50'">
                        Semua
                    </button>
                    <button type="button"
                        @click="value = 'sudah'; open = false; $nextTick(() => $el.closest('form').submit())"
                        class="w-full text-left px-4 py-2.5 text-sm font-medium transition-colors border-t border-gray-100"
                        :class="value === 'sudah' ? 'bg-birupesat text-white' : 'text-accent hover:bg-gray-50'">
                        Sudah Memilih
                    </button>
                    <button type="button"
                        @click="value = 'belum'; open = false; $nextTick(() => $el.closest('form').submit())"
                        class="w-full text-left px-4 py-2.5 text-sm font-medium transition-colors border-t border-gray-100"
                        :class="value === 'belum' ? 'bg-birupesat text-white' : 'text-accent hover:bg-gray-50'">
                        Belum Memilih
                    </button>
                </div>
            </div>
        </form>

        {{-- Stats bar --}}
        <div class="flex gap-4 mb-4">
            <x-badge color="blue">Total: {{ $voters->total() }}</x-badge>
            <x-badge color="green">Sudah memilih: {{ $voters->getCollection()->where('has_voted', true)->count() }} (halaman ini)</x-badge>
        </div>

        <div class="bg-white rounded-xl border-2 border-gray-200 shadow-lg overflow-hidden">
            @if ($voters->isEmpty())
                <div class="text-center py-12">
                    <i data-lucide="users" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                    <p class="text-gray-500">Belum ada pemilih terdaftar.</p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">#</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($voters as $voter)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-4 py-3 text-gray-400">{{ $voters->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-3 font-medium text-accent">{{ $voter->nama }}</td>
                                <td class="px-4 py-3">
                                    @if ($voter->has_voted)
                                        <x-badge color="green" class="gap-1">
                                            <i data-lucide="check" class="w-3 h-3"></i> Sudah memilih
                                        </x-badge>
                                    @else
                                        <x-badge color="gray">Belum memilih</x-badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <x-button
                                            @click="openEdit({ id: {{ $voter->id }}, nama: '{{ addslashes($voter->nama) }}', updateUrl: '{{ route('admin.voter.update', $voter) }}' })"
                                            variant="ghost" size="sm" icon="pencil">
                                            Edit
                                        </x-button>
                                        @if ($voter->has_voted)
                                            <form method="POST" action="{{ route('admin.voter.reset', $voter) }}">
                                                @csrf
                                                <x-button
                                                    type="button"
                                                    variant="ghost" size="sm" icon="rotate-ccw"
                                                    @click="adminConfirm($event, 'Reset Vote?', 'Status vote {{ addslashes($voter->nama) }} akan direset.', 'Ya, Reset', 'warning')">
                                                    Reset
                                                </x-button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.voter.destroy', $voter) }}">
                                            @csrf @method('DELETE')
                                            <x-button
                                                type="button"
                                                variant="ghost" size="sm" icon="trash-2"
                                                class="text-danger hover:text-danger hover:bg-danger/10"
                                                @click="adminConfirm($event, 'Hapus Pemilih?', 'Data {{ addslashes($voter->nama) }} akan dihapus permanen.', 'Ya, Hapus')">
                                                Hapus
                                            </x-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if ($voters->hasPages())
                    <div class="px-4 py-3 border-t border-gray-100">
                        {{ $voters->withQueryString()->links() }}
                    </div>
                @endif
            @endif
        </div>

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
                <h2 class="text-base font-bold text-accent">
                    <span x-show="panel === 'create'">Tambah Pemilih</span>
                    <span x-show="panel === 'edit'" x-cloak>Edit Pemilih</span>
                    <span x-show="panel === 'import'" x-cloak>Import Pemilih</span>
                </h2>
                <button @click="close()" class="text-gray-400 hover:text-accent transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            {{-- CREATE form --}}
            <div x-show="panel === 'create'" class="flex-1 overflow-y-auto p-6" x-cloak>
                <form method="POST" action="{{ route('admin.voter.store') }}" class="space-y-4">
                    @csrf
                    <x-form.input
                        name="nama"
                        label="Nama Lengkap"
                        placeholder="Ahmad Sattar Fathulloh"
                        :required="true"
                        hint="Nama ini yang akan dicocokkan saat pemilih mengisi form voting."
                    />
                    <div class="flex gap-3">
                        <x-button type="submit" variant="primary" icon="save">Simpan</x-button>
                        <x-button type="button" @click="close()" variant="secondary">Batal</x-button>
                    </div>
                </form>
            </div>

            {{-- EDIT form --}}
            <div x-show="panel === 'edit'" class="flex-1 overflow-y-auto p-6" x-cloak>
                <form method="POST" :action="editData.updateUrl" class="space-y-4">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama" :value="editData.nama" required
                            class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm text-accent bg-white
                                focus:outline-none focus:ring-2 focus:ring-birupesat focus:border-birupesat">
                    </div>
                    <div class="flex gap-3">
                        <x-button type="submit" variant="primary" icon="save">Simpan</x-button>
                        <x-button type="button" @click="close()" variant="secondary">Batal</x-button>
                    </div>
                </form>
            </div>

            {{-- IMPORT form --}}
            <div x-show="panel === 'import'" class="flex-1 overflow-y-auto p-6" x-cloak>
                <p class="text-sm text-gray-500 mb-4">Pisahkan nama dengan simbol <code class="font-mono bg-gray-100 px-1 rounded">|</code>. Nama duplikat akan diabaikan dan ditampilkan.</p>
                <form method="POST" action="{{ route('admin.voter.import') }}" class="space-y-4">
                    @csrf
                    <x-form.textarea
                        name="daftar_nama"
                        label="Daftar Nama"
                        placeholder="Ahmad Sattar Fathulloh|Ahmad Azzam Mozaris|Ahmad Yusuf Ar-Rafi, S.Pd|Budi Santoso"
                        rows="10"
                        :required="true"
                    />
                    <div class="flex gap-3">
                        <x-button type="submit" variant="primary" icon="upload">Import</x-button>
                        <x-button type="button" @click="close()" variant="secondary">Batal</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
