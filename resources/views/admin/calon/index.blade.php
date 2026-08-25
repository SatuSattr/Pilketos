<x-layouts.admin title="Calon">
    <div
        x-data="{
            panel: null,
            editData: {},
            crop: {x: 50, y: 50, zoom: 1},
            newFotoPreview: null,
            dragging: false,
            createCrop: {x: 50, y: 50, zoom: 1},
            createPreview: null,
            createDragging: false,
            openCreate() {
                this.panel = 'create';
                this.editData = {};
                this.createCrop = {x: 50, y: 50, zoom: 1};
                if(this.createPreview){ URL.revokeObjectURL(this.createPreview); this.createPreview=null; }
            },
            openEdit(data) {
                this.editData = data;
                this.crop = data.fotoCrop ? {x: Number(data.fotoCrop.x), y: Number(data.fotoCrop.y), zoom: Number(data.fotoCrop.zoom)} : {x: 50, y: 50, zoom: 1};
                this.newFotoPreview = null;
                this.panel = 'edit';
            },
            close() {
                this.panel = null; this.dragging = false; this.createDragging = false;
                if(this.newFotoPreview){ URL.revokeObjectURL(this.newFotoPreview); this.newFotoPreview=null; }
                if(this.createPreview){ URL.revokeObjectURL(this.createPreview); this.createPreview=null; }
            },
            handleFotoChange(e){
                const f = e.target.files[0];
                if(f){
                    if(this.newFotoPreview) URL.revokeObjectURL(this.newFotoPreview);
                    this.newFotoPreview = URL.createObjectURL(f);
                    this.crop = {x: 50, y: 50, zoom: 1};
                } else {
                    if(this.newFotoPreview) URL.revokeObjectURL(this.newFotoPreview);
                    this.newFotoPreview = null;
                }
            },
            handleCreateFotoChange(e){
                const f = e.target.files[0];
                if(f){
                    if(this.createPreview) URL.revokeObjectURL(this.createPreview);
                    this.createPreview = URL.createObjectURL(f);
                    this.createCrop = {x: 50, y: 50, zoom: 1};
                } else {
                    if(this.createPreview) URL.revokeObjectURL(this.createPreview);
                    this.createPreview = null;
                }
            },
            startDrag(e){ this.dragging = true; this.updatePos(e); },
            onDrag(e){ if(!this.dragging) return; this.updatePos(e); },
            stopDrag(){ this.dragging = false; },
            startCreateDrag(e){ this.createDragging = true; this.updateCreatePos(e); },
            onCreateDrag(e){ if(!this.createDragging) return; this.updateCreatePos(e); },
            stopCreateDrag(){ this.createDragging = false; },
            updatePos(e){
                const rect = e.currentTarget.getBoundingClientRect();
                const cx = e.touches ? e.touches[0].clientX : e.clientX;
                const cy = e.touches ? e.touches[0].clientY : e.clientY;
                this.crop.x = Math.max(0, Math.min(100, ((cx - rect.left) / rect.width) * 100));
                this.crop.y = Math.max(0, Math.min(100, ((cy - rect.top) / rect.height) * 100));
            },
            updateCreatePos(e){
                const rect = e.currentTarget.getBoundingClientRect();
                const cx = e.touches ? e.touches[0].clientX : e.clientX;
                const cy = e.touches ? e.touches[0].clientY : e.clientY;
                this.createCrop.x = Math.max(0, Math.min(100, ((cx - rect.left) / rect.width) * 100));
                this.createCrop.y = Math.max(0, Math.min(100, ((cy - rect.top) / rect.height) * 100));
            },
            get fotoSrc(){ return this.newFotoPreview || ('/storage/foto_calon/' + (this.editData.foto || '')); }
        }"
        @keydown.escape.window="close()"
    >
        {{-- Page header --}}
        <div class="flex items-center justify-between mb-6">
            <x-page-header title="Daftar Calon" description="Kelola data calon ketua OSIS" class="mb-0" />
            <x-button @click="openCreate()" variant="primary" icon="user-plus">
                Tambah Calon
            </x-button>
        </div>

        {{-- List --}}
        @if ($calons->isEmpty())
            <div class="bg-white rounded-xl border-2 border-gray-200 shadow-lg p-12 text-center">
                <i data-lucide="users" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                <p class="text-gray-500 font-medium">Belum ada calon terdaftar.</p>
                <x-button @click="openCreate()" variant="primary" icon="plus" class="mt-4">
                    Tambah Calon Pertama
                </x-button>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @foreach ($calons as $calon)
                    <div class="bg-white rounded-xl border-2 border-gray-200 shadow-lg overflow-hidden hover:border-birupesat transition-colors duration-200">
                        <div class="flex gap-4 p-4">
                            <div class="relative shrink-0">
                                <x-cropped-img
                                    :src="asset('storage/foto_calon/' . $calon->foto)"
                                    :crop="$calon->foto_crop_data"
                                    :alt="'Foto ' . $calon->nama"
                                    class="w-20 h-20 rounded-xl border-2 border-gray-100"
                                />
                                <span class="absolute -top-2 -left-2 w-6 h-6 rounded-full bg-birupesat text-white text-xs font-bold flex items-center justify-center">
                                    {{ $calon->nomor }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-accent truncate">{{ $calon->nama }}</p>
                                <x-badge color="blue" class="mt-0.5">{{ $calon->kelas }}</x-badge>
                                <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                                    <i data-lucide="vote" class="w-3 h-3"></i>
                                    {{ $calon->votes_count }} suara
                                </p>
                            </div>
                        </div>
                        <div class="border-t border-gray-100 px-4 py-3 flex items-center gap-2 bg-gray-50">
                            <x-button
                                @click="openEdit({
                                    id: {{ $calon->id }},
                                    nomor: '{{ addslashes($calon->nomor) }}',
                                    nama: '{{ addslashes($calon->nama) }}',
                                    kelas: '{{ addslashes($calon->kelas) }}',
                                    visi: {{ json_encode($calon->visi) }},
                                    misi: {{ json_encode($calon->misi) }},
                                    foto: '{{ $calon->foto }}',
                                    fotoCrop: {{ json_encode($calon->foto_crop_data) }},
                                    updateUrl: '{{ route('admin.calon.update', $calon) }}'
                                })"
                                variant="secondary" size="sm" icon="pencil">
                                Edit
                            </x-button>
                            <form method="POST" action="{{ route('admin.calon.destroy', $calon) }}">
                                @csrf @method('DELETE')
                                <x-button
                                    type="button"
                                    variant="danger" size="sm" icon="trash-2"
                                    @click="adminConfirm($event, 'Hapus Calon?', 'Data calon {{ addslashes($calon->nama) }} dan semua suara terkait akan dihapus permanen.', 'Ya, Hapus')">
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
            class="fixed inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl z-50 flex flex-col"
            x-cloak>

            {{-- Panel header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 shrink-0">
                <h2 class="text-base font-bold text-accent">
                    <span x-show="panel === 'create'">Tambah Calon</span>
                    <span x-show="panel === 'edit'" x-cloak>Edit Calon</span>
                </h2>
                <button @click="close()" class="text-gray-400 hover:text-accent transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            {{-- CREATE form --}}
            <div x-show="panel === 'create'" class="flex-1 overflow-y-auto p-6" x-cloak>
                <form method="POST" action="{{ route('admin.calon.store') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <x-form.input name="nomor" label="Nomor Urut" placeholder="1" :required="true"
                            :value="old('nomor')" />
                        <x-form.input name="kelas" label="Kelas" placeholder="XII IPA 1" :required="true"
                            :value="old('kelas')" />
                    </div>
                    <x-form.input name="nama" label="Nama Lengkap" placeholder="Ahmad Sattar Fathulloh"
                        :required="true" :value="old('nama')" />
                    <x-form.textarea name="visi" label="Visi" placeholder="Visi calon..." :required="true" rows="3"
                        :value="old('visi')" />
                    <x-form.textarea name="misi" label="Misi" placeholder="Misi calon..." :required="true" rows="5"
                        :value="old('misi')" />
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">
                            Foto <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="foto" accept="image/*" required @change="handleCreateFotoChange($event)"
                            class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm text-accent bg-white
                                focus:outline-none focus:ring-2 focus:ring-birupesat focus:border-birupesat
                                file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0
                                file:text-xs file:font-semibold file:bg-birupesat/10 file:text-birupesat
                                hover:file:bg-birupesat/20">
                        @error('foto')
                            <p class="mt-1 text-xs text-error">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">JPG, PNG, WEBP. Maks 2MB.</p>
                    </div>

                    {{-- Virtual Crop Editor — muncul setelah foto dipilih --}}
                    <div x-show="createPreview" x-cloak class="border-t border-gray-200 pt-5 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">
                                Atur Crop Foto <span class="text-gray-400 font-normal normal-case">(virtual — file tidak diubah)</span>
                            </label>
                            <p class="text-xs text-gray-500">Geser titik fokus di preview, atur zoom.</p>
                        </div>

                        <div class="space-y-1.5">
                            <p class="text-[10px] font-semibold text-gray-700 uppercase tracking-wide">Preview</p>
                            <div
                                @mousedown="startCreateDrag($event)" @mousemove="onCreateDrag($event)" @mouseup="stopCreateDrag()" @mouseleave="stopCreateDrag()"
                                @touchstart.prevent="startCreateDrag($event)" @touchmove.prevent="onCreateDrag($event)" @touchend="stopCreateDrag()"
                                class="relative w-full aspect-square rounded-xl overflow-hidden border-2 border-gray-200 bg-gray-50 cursor-crosshair select-none touch-none"
                            >
                                <img :src="createPreview" alt="preview" draggable="false"
                                    class="absolute inset-0 w-full h-full object-cover pointer-events-none select-none"
                                    :style="`object-position:${createCrop.x}% ${createCrop.y}%; transform:scale(${createCrop.zoom}); transform-origin:${createCrop.x}% ${createCrop.y}%`">
                                <div class="absolute w-5 h-5 -ml-2.5 -mt-2.5 rounded-full border-2 border-white shadow-lg bg-birupesat/80 pointer-events-none flex items-center justify-center"
                                    :style="`left:${createCrop.x}%; top:${createCrop.y}%`">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                </div>
                                <div class="absolute inset-0 rounded-xl ring-1 ring-birupesat/10 pointer-events-none"></div>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Zoom <span class="text-birupesat font-bold" x-text="Number(createCrop.zoom).toFixed(2) + 'x'"></span>
                            </label>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-500">1.0</span>
                                <input type="range" min="1" max="3" step="0.05" x-model.number="createCrop.zoom"
                                    class="flex-1 accent-birupesat h-2">
                                <span class="text-xs text-gray-500">3.0</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-1.5 text-xs">
                                <span class="text-gray-500 font-medium">Fokus:</span>
                                <input type="number" inputmode="decimal" step="any"
                                    x-model="createCrop.x"
                                    class="w-16 rounded-lg border-2 border-gray-200 px-2 py-1 text-xs font-mono text-accent focus:border-birupesat focus:outline-none"
                                    placeholder="X">
                                <span class="text-gray-500">%</span>
                                <span class="text-gray-400">,</span>
                                <input type="number" inputmode="decimal" step="any"
                                    x-model="createCrop.y"
                                    class="w-16 rounded-lg border-2 border-gray-200 px-2 py-1 text-xs font-mono text-accent focus:border-birupesat focus:outline-none"
                                    placeholder="Y">
                                <span class="text-gray-500">%</span>
                            </div>
                            <button type="button" @click="createCrop = {x: 50, y: 50, zoom: 1}"
                                class="inline-flex items-center gap-1 text-xs font-semibold text-gray-600 hover:text-birupesat transition-colors shrink-0">
                                <i data-lucide="rotate-ccw" class="w-3 h-3"></i> Reset
                            </button>
                        </div>

                        <input type="hidden" name="foto_crop[x]" :value="Number(createCrop.x).toFixed(1)">
                        <input type="hidden" name="foto_crop[y]" :value="Number(createCrop.y).toFixed(1)">
                        <input type="hidden" name="foto_crop[zoom]" :value="Number(createCrop.zoom).toFixed(2)">
                    </div>

                    <div class="flex gap-3 pt-2">
                        <x-button type="submit" variant="primary" icon="save">Simpan Calon</x-button>
                        <x-button type="button" @click="close()" variant="secondary">Batal</x-button>
                    </div>
                </form>
            </div>

            {{-- EDIT form --}}
            <div x-show="panel === 'edit'" class="flex-1 overflow-y-auto p-6" x-cloak>
                <form method="POST" :action="editData.updateUrl" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">
                                Nomor Urut <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nomor" :value="editData.nomor" required
                                class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm text-accent bg-white
                                    focus:outline-none focus:ring-2 focus:ring-birupesat focus:border-birupesat">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">
                                Kelas <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="kelas" :value="editData.kelas" required
                                class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm text-accent bg-white
                                    focus:outline-none focus:ring-2 focus:ring-birupesat focus:border-birupesat">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama" :value="editData.nama" required
                            class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm text-accent bg-white
                                focus:outline-none focus:ring-2 focus:ring-birupesat focus:border-birupesat">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">
                            Visi <span class="text-danger">*</span>
                        </label>
                        <textarea name="visi" rows="3" required
                            x-text="editData.visi"
                            class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm text-accent bg-white
                                focus:outline-none focus:ring-2 focus:ring-birupesat focus:border-birupesat resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">
                            Misi <span class="text-danger">*</span>
                        </label>
                        <textarea name="misi" rows="5" required
                            x-text="editData.misi"
                            class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm text-accent bg-white
                                focus:outline-none focus:ring-2 focus:ring-birupesat focus:border-birupesat resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">
                            Ganti Foto (opsional)
                        </label>
                        <input type="file" name="foto" accept="image/*" @change="handleFotoChange($event)"
                            class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm text-accent bg-white
                                focus:outline-none focus:ring-2 focus:ring-birupesat focus:border-birupesat
                                file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0
                                file:text-xs file:font-semibold file:bg-birupesat/10 file:text-birupesat">
                        <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin ganti foto.</p>
                    </div>

                    {{-- Virtual Crop Editor --}}
                    <div class="border-t border-gray-200 pt-5 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">
                                Atur Crop Foto <span class="text-gray-400 font-normal normal-case">(virtual — file tidak diubah)</span>
                            </label>
                            <p class="text-xs text-gray-500">Geser titik fokus di preview, atur zoom. Hasil akan terlihat di kartu voting & daftar calon.</p>
                        </div>

                        {{-- Preview --}}
                        <div class="space-y-1.5">
                            <p class="text-[10px] font-semibold text-gray-700 uppercase tracking-wide">Preview</p>
                            <div
                                @mousedown="startDrag($event)" @mousemove="onDrag($event)" @mouseup="stopDrag()" @mouseleave="stopDrag()"
                                @touchstart.prevent="startDrag($event)" @touchmove.prevent="onDrag($event)" @touchend="stopDrag()"
                                class="relative w-full aspect-square rounded-xl overflow-hidden border-2 border-gray-200 bg-gray-50 cursor-crosshair select-none touch-none"
                            >
                                <img :src="fotoSrc" alt="preview" draggable="false"
                                    class="absolute inset-0 w-full h-full object-cover pointer-events-none select-none"
                                    :style="`object-position:${crop.x}% ${crop.y}%; transform:scale(${crop.zoom}); transform-origin:${crop.x}% ${crop.y}%`">
                                <div class="absolute w-5 h-5 -ml-2.5 -mt-2.5 rounded-full border-2 border-white shadow-lg bg-birupesat/80 pointer-events-none flex items-center justify-center"
                                    :style="`left:${crop.x}%; top:${crop.y}%`">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                </div>
                                <div class="absolute inset-0 rounded-xl ring-1 ring-birupesat/10 pointer-events-none"></div>
                            </div>
                        </div>

                        {{-- Zoom control --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Zoom <span class="text-birupesat font-bold" x-text="Number(crop.zoom).toFixed(2) + 'x'"></span>
                            </label>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-500">1.0</span>
                                <input type="range" min="1" max="3" step="0.05" x-model.number="crop.zoom"
                                    class="flex-1 accent-birupesat h-2">
                                <span class="text-xs text-gray-500">3.0</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-1.5 text-xs">
                                <span class="text-gray-500 font-medium">Fokus:</span>
                                <input type="number" inputmode="decimal" step="any"
                                    x-model="crop.x"
                                    class="w-16 rounded-lg border-2 border-gray-200 px-2 py-1 text-xs font-mono text-accent focus:border-birupesat focus:outline-none"
                                    placeholder="X">
                                <span class="text-gray-500">%</span>
                                <span class="text-gray-400">,</span>
                                <input type="number" inputmode="decimal" step="any"
                                    x-model="crop.y"
                                    class="w-16 rounded-lg border-2 border-gray-200 px-2 py-1 text-xs font-mono text-accent focus:border-birupesat focus:outline-none"
                                    placeholder="Y">
                                <span class="text-gray-500">%</span>
                            </div>
                            <button type="button" @click="crop = {x: 50, y: 50, zoom: 1}"
                                class="inline-flex items-center gap-1 text-xs font-semibold text-gray-600 hover:text-birupesat transition-colors shrink-0">
                                <i data-lucide="rotate-ccw" class="w-3 h-3"></i> Reset
                            </button>
                        </div>

                        {{-- Hidden inputs for submission --}}
                        <input type="hidden" name="foto_crop[x]" :value="Number(crop.x).toFixed(1)">
                        <input type="hidden" name="foto_crop[y]" :value="Number(crop.y).toFixed(1)">
                        <input type="hidden" name="foto_crop[zoom]" :value="Number(crop.zoom).toFixed(2)">
                    </div>

                    <div class="flex gap-3 pt-2">
                        <x-button type="submit" variant="primary" icon="save">Simpan Perubahan</x-button>
                        <x-button type="button" @click="close()" variant="secondary">Batal</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
