<x-layouts.admin title="Akun Admin">
    <div
        x-data="{
            panel: null,
            editData: {},
            openCreate() { this.panel = 'create'; },
            openEdit(data) { this.editData = data; this.panel = 'edit'; },
            close() { this.panel = null; }
        }"
        @keydown.escape.window="close()"
    >
        {{-- Page header --}}
        <div class="flex items-center justify-between mb-6">
            <x-page-header title="Akun Admin" description="Kelola akun yang dapat mengakses panel admin" class="mb-0" />
            <x-button @click="openCreate()" variant="primary" size="sm" icon="user-plus">
                Tambah Akun
            </x-button>
        </div>

        {{-- User table --}}
        <div class="bg-white rounded-xl border-2 border-gray-200 shadow-lg overflow-hidden">
            @if ($users->isEmpty())
                <div class="text-center py-12">
                    <i data-lucide="users" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                    <p class="text-gray-500">Belum ada akun admin.</p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Dibuat</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors duration-100">
                                <td class="px-4 py-3 font-medium text-accent">
                                    {{ $user->name }}
                                    @if ($user->id === auth()->id())
                                        <x-badge color="blue" class="ml-1">Anda</x-badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <x-button
                                            variant="ghost"
                                            size="sm"
                                            icon="pencil"
                                            @click="openEdit({
                                                id: {{ $user->id }},
                                                name: @js($user->name),
                                                email: @js($user->email),
                                                updateUrl: '{{ route('admin.user.update', $user) }}'
                                            })"
                                            aria-label="Edit akun {{ $user->name }}"
                                        >
                                            Edit
                                        </x-button>
                                        @if ($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.user.destroy', $user) }}">
                                                @csrf
                                                <input type="hidden" name="_method" value="DELETE">
                                                <x-button
                                                    type="submit"
                                                    variant="danger"
                                                    size="sm"
                                                    icon="trash-2"
                                                    onclick="adminConfirm(event, 'Hapus Akun?', 'Akun {{ addslashes($user->name) }} akan dihapus permanen.', 'Hapus', 'danger')"
                                                    aria-label="Hapus akun {{ $user->name }}"
                                                >
                                                    Hapus
                                                </x-button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Backdrop --}}
        <div
            x-show="panel !== null"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="close()"
            class="fixed inset-0 z-40 bg-black/30"
        ></div>

        {{-- Slide-over panel --}}
        <div
            x-show="panel !== null"
            x-cloak
            x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="fixed inset-y-0 right-0 z-50 w-full max-w-md bg-white shadow-2xl flex flex-col"
        >
            {{-- Panel header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 shrink-0">
                <h2 class="font-semibold text-accent text-base" x-show="panel === 'create'">Tambah Akun Admin</h2>
                <h2 class="font-semibold text-accent text-base" x-show="panel === 'edit'">Edit Akun Admin</h2>
                <button
                    type="button"
                    @click="close()"
                    aria-label="Tutup panel"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-accent hover:bg-gray-100 transition-colors duration-150"
                >
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            {{-- Panel body --}}
            <div class="flex-1 overflow-y-auto px-6 py-5">

                {{-- Create form --}}
                <div x-show="panel === 'create'">
                    <form method="POST" action="{{ route('admin.user.store') }}" class="space-y-4">
                        @csrf
                        <x-form.input
                            name="name"
                            label="Nama"
                            placeholder="Nama lengkap"
                            :required="true"
                            :value="old('name')"
                        />
                        <x-form.input
                            name="email"
                            label="Email"
                            type="email"
                            placeholder="email@contoh.com"
                            :required="true"
                            :value="old('email')"
                        />
                        <x-form.input
                            name="password"
                            label="Password"
                            type="password"
                            placeholder="Minimal 8 karakter"
                            :required="true"
                        />
                        <x-form.input
                            name="password_confirmation"
                            label="Konfirmasi Password"
                            type="password"
                            placeholder="Ulangi password"
                            :required="true"
                        />
                        <div class="flex gap-3 pt-2">
                            <x-button type="submit" variant="primary" icon="user-plus">Tambah</x-button>
                            <x-button type="button" @click="close()" variant="secondary">Batal</x-button>
                        </div>
                    </form>
                </div>

                {{-- Edit form --}}
                <div x-show="panel === 'edit'">
                    <form method="POST" :action="editData.updateUrl" class="space-y-4">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <div>
                            <label for="edit-name" class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-error">*</span></label>
                            <input
                                id="edit-name"
                                type="text"
                                name="name"
                                :value="editData.name"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border-2 border-gray-200 text-sm text-accent bg-white focus:outline-none focus:ring-2 focus:ring-birupesat focus:border-birupesat"
                                placeholder="Nama lengkap"
                            >
                            @error('name') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="edit-email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-error">*</span></label>
                            <input
                                id="edit-email"
                                type="email"
                                name="email"
                                :value="editData.email"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border-2 border-gray-200 text-sm text-accent bg-white focus:outline-none focus:ring-2 focus:ring-birupesat focus:border-birupesat"
                                placeholder="email@contoh.com"
                            >
                            @error('email') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="edit-password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                            <input
                                id="edit-password"
                                type="password"
                                name="password"
                                class="w-full px-3.5 py-2.5 rounded-xl border-2 border-gray-200 text-sm text-accent bg-white focus:outline-none focus:ring-2 focus:ring-birupesat focus:border-birupesat"
                                placeholder="Kosongkan jika tidak ingin mengubah"
                            >
                            @error('password') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengubah password.</p>
                        </div>
                        <div>
                            <label for="edit-password-confirm" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                            <input
                                id="edit-password-confirm"
                                type="password"
                                name="password_confirmation"
                                class="w-full px-3.5 py-2.5 rounded-xl border-2 border-gray-200 text-sm text-accent bg-white focus:outline-none focus:ring-2 focus:ring-birupesat focus:border-birupesat"
                                placeholder="Ulangi password baru"
                            >
                        </div>
                        <div class="flex gap-3 pt-2">
                            <x-button type="submit" variant="primary" icon="save">Simpan</x-button>
                            <x-button type="button" @click="close()" variant="secondary">Batal</x-button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-layouts.admin>
