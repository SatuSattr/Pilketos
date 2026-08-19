<x-layouts.admin title="Dashboard">
    <x-page-header title="Dashboard" description="Ringkasan status pemilihan saat ini" />

    {{-- Stats grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stats-card
            title="Total Pemilih"
            :value="$totalVoters"
            icon="users"
            color="blue"
        />
        <x-stats-card
            title="Sudah Memilih"
            :value="$totalHasVoted"
            icon="circle-check"
            color="green"
            :sub="$participationRate . '% partisipasi'"
        />
        <x-stats-card
            title="Belum Memilih"
            :value="$totalNotVoted"
            icon="clock"
            color="yellow"
        />
        <x-stats-card
            title="Key Aktif"
            :value="$activeKeys"
            icon="key-round"
            color="blue"
        />
    </div>

    {{-- Chart + Perolehan Suara --}}
    <div class="flex flex-col lg:flex-row gap-4 mb-6">

        {{-- Chart (70%) --}}
        <div class="w-full lg:w-[70%] bg-white rounded-xl border-2 border-gray-200 shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-accent flex items-center gap-2">
                    <i data-lucide="line-chart" class="w-5 h-5 text-birupesat"></i>
                    Grafik Perolehan Suara
                </h2>
                <div class="flex items-center gap-2">
                    <button id="chartZoomIn" title="Zoom In"
                        class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors text-gray-500 hover:text-accent">
                        <i data-lucide="zoom-in" class="w-4 h-4"></i>
                    </button>
                    <button id="chartZoomOut" title="Zoom Out"
                        class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors text-gray-500 hover:text-accent">
                        <i data-lucide="zoom-out" class="w-4 h-4"></i>
                    </button>
                    <button id="chartZoomReset" title="Reset Zoom"
                        class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors text-gray-500 hover:text-accent">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <div class="relative h-64 lg:h-80">
                <canvas id="voteChart"></canvas>
            </div>

            {{-- Legend --}}
            <div id="chartLegend" class="flex flex-wrap gap-4 mt-3"></div>

            {{-- URL untuk fetch data --}}
            <span id="vote-chart-url" class="hidden" data-url="{{ route('admin.dashboard.chart-data') }}"></span>
        </div>

        {{-- Perolehan Suara (30%) --}}
        <div class="w-full lg:w-[30%] bg-white rounded-xl border-2 border-gray-200 shadow-lg p-6">
            <h2 class="text-base font-bold text-accent flex items-center gap-2 mb-4">
                <i data-lucide="trophy" class="w-5 h-5 text-birupesat"></i>
                Perolehan Suara
            </h2>

            @if ($calons->isEmpty())
                <p class="text-sm text-gray-500 text-center py-6">Belum ada data calon.</p>
            @else
                <div class="space-y-4">
                    @foreach ($calons as $calon)
                        @php
                            $pct = $totalVotes > 0 ? round(($calon->votes_count / $totalVotes) * 100, 1) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-birupesat bg-birupesat/10 rounded-full w-6 h-6 flex items-center justify-center shrink-0">
                                        {{ $calon->nomor }}
                                    </span>
                                    <span class="text-sm font-semibold text-accent truncate max-w-[110px]">{{ $calon->nama }}</span>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-sm font-bold text-accent">{{ $calon->votes_count }}</span>
                                    <span class="text-xs text-gray-500 ml-1">({{ $pct }}%)</span>
                                </div>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-birupesat h-2 rounded-full transition-all duration-500"
                                    style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                    <span>Total suara masuk</span>
                    <span class="font-bold text-accent">{{ $totalVotes }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <a href="{{ route('admin.calon.create') }}"
            class="bg-white rounded-xl border-2 border-gray-200 shadow-lg p-5 flex items-center gap-4 hover:border-birupesat transition-colors duration-200 group">
            <div class="w-10 h-10 rounded-xl bg-birupesat/10 flex items-center justify-center group-hover:bg-birupesat transition-colors duration-200">
                <i data-lucide="user-plus" class="w-5 h-5 text-birupesat group-hover:text-white transition-colors duration-200"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-accent">Tambah Calon</p>
                <p class="text-xs text-gray-500">Daftarkan calon baru</p>
            </div>
        </a>
        <a href="{{ route('admin.voter.create') }}"
            class="bg-white rounded-xl border-2 border-gray-200 shadow-lg p-5 flex items-center gap-4 hover:border-birupesat transition-colors duration-200 group">
            <div class="w-10 h-10 rounded-xl bg-success/10 flex items-center justify-center group-hover:bg-success transition-colors duration-200">
                <i data-lucide="list-plus" class="w-5 h-5 text-success group-hover:text-white transition-colors duration-200"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-accent">Tambah Pemilih</p>
                <p class="text-xs text-gray-500">Kelola daftar hak suara</p>
            </div>
        </a>
        <a href="{{ route('admin.display-key.create') }}"
            class="bg-white rounded-xl border-2 border-gray-200 shadow-lg p-5 flex items-center gap-4 hover:border-birupesat transition-colors duration-200 group">
            <div class="w-10 h-10 rounded-xl bg-warning/10 flex items-center justify-center group-hover:bg-warning transition-colors duration-200">
                <i data-lucide="key-round" class="w-5 h-5 text-yellow-600 group-hover:text-white transition-colors duration-200"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-accent">Buat Display Key</p>
                <p class="text-xs text-gray-500">Buat key untuk bilik voting</p>
            </div>
        </a>
    </div>
</x-layouts.admin>
