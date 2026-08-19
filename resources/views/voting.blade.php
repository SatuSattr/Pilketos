<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>Pilketos | Pilih Calon Ketua OSIS</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="bg-primary">
        <div class="flex flex-col min-h-screen">
            <main class="flex-grow flex items-center justify-center">
                <div class="mx-auto w-full">
                    <div class="text-center px-6 lg:p-0 mb-6 lg:mb-12">
                        <img src="{{ asset('storage/assets/logo.png') }}" alt="Pilketos" class="h-16 w-auto object-contain mx-auto mb-4">
                        <h1
                            class="text-2xl lg:text-4xl font-bold text-accent mb-2 lg:mb-2"
                        >
                            Pemilihan Ketua OSIS
                        </h1>
                        <p class="text-lg lg:text-xl text-gray-600 mb-2">
                            Pilih satu calon ketua OSIS favorit Anda
                        </p>
                    </div>

                    <form id="votingForm" class="space-y-8">
                        <div
                            class="flex flex-nowrap gap-2 lg:gap-8 items-center justify-center"
                            x-data="voting"
                        >
                            <template
                                x-for="(calon, index) in calons"
                                :key="calon.id"
                            >
                                <div
                                    :id="'caketos-container-' + (index + 1)"
                                    class="caketos-item"
                                >
                                    <div
                                        class="cursor-pointer flex w-[10rem] lg:w-[22rem] group items-center relative"
                                    >
                                        <div
                                            class="bg-white z-10 card w-full border-2 border-gray-200 rounded-xl shadow-lg hover:shadow-xl hover:border-birupesat cursor-pointer transition-all duration-300 overflow-hidden max-w-sm group relative"
                                            :data-calon-id="calon.id"
                                            :data-visi="calon.visi"
                                            :data-misi="calon.misi"
                                            :data-nama="calon.nama"
                                            :data-kelas="calon.kelas"
                                        >
                                            <i
                                                data-lucide="circle-check"
                                                class="selection-indicator opacity-0 text-birupesat absolute top-2.5 right-2.5 text-lg lg:text-2xl z-20 transition-opacity duration-150 ease-in-out"
                                            ></i>

                                            <input
                                                type="radio"
                                                name="id_calon"
                                                :value="calon.id"
                                                :id="'calon_' + calon.id"
                                                class="hidden candidate-radio"
                                            />

                                            <label
                                                :for="'calon_' + calon.id"
                                                class="cursor-pointer block"
                                            >
                                                <div
                                                    class="flex gap-3 p-3 lg:p-6 border-b border-gray-100"
                                                >
                                                    <h3
                                                        class="font-bold text-lg lg:text-2xl leading-5 lg:leading-6"
                                                    >
                                                        <span
                                                            x-text="calon.firstName"
                                                        ></span
                                                        ><br />
                                                        <span
                                                            class="text-gray-500 text-sm lg:text-xl font-medium"
                                                            x-text="calon.lastName"
                                                        ></span>
                                                    </h3>
                                                </div>
                                                <div
                                                    class="h-[10rem] lg:h-[22rem] bg-gradient-to-br from-gray-50 to-gray-200 flex items-center justify-center overflow-hidden relative"
                                                >
                                                    <h1
                                                        class="absolute duration-200 ease-in-out top-3 m-0 left-4 font-bold opacity-20 text-6xl lg:text-9xl"
                                                        x-text="calon.nomor"
                                                    ></h1>
                                                    <img
                                                        class="size-[140%] object-cover absolute -top-3 -right-9"
                                                        :src="calon.urlFoto"
                                                        :alt="calon.nama"
                                                    />
                                                </div>
                                                <div
                                                    class="p-3 lg:p-6 space-y-3"
                                                >
                                                    <div
                                                        class="flex justify-between text-sm lg:text-xl"
                                                    >
                                                        <span
                                                            class="text-gray-500 font-medium"
                                                            >KELAS</span
                                                        >
                                                        <span
                                                            class="text-accent font-semibold"
                                                            x-text="calon.kelas"
                                                        ></span>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>

                                        <div
                                            class="detail-panel absolute top-[5%] left-0 w-[10rem] lg:w-[22rem] h-[90%] bg-white border-2 border-birupesat rounded-xl shadow-xl overflow-hidden pointer-events-none z-0"
                                            style="transform: translateX(0)"
                                        >
                                            <div
                                                class="detail-panel-scroll p-4 pl-7 lg:p-6 lg:pl-9 h-full overflow-y-auto"
                                            >
                                                <div class="mb-4">
                                                    <h3
                                                        class="font-bold text-lg lg:text-2xl text-accent mb-1 detail-nama"
                                                    ></h3>
                                                    <p
                                                        class="text-sm lg:text-base text-gray-600 detail-kelas"
                                                    ></p>
                                                </div>

                                                <div class="mb-4">
                                                    <div
                                                        class="flex items-center gap-2 mb-2"
                                                    >
                                                        <div
                                                            class="w-1 h-4 rounded-full bg-birupesat"
                                                        ></div>
                                                        <h4
                                                            class="text-xs lg:text-sm font-semibold text-gray-700 uppercase tracking-wide"
                                                        >
                                                            Visi
                                                        </h4>
                                                    </div>
                                                    <p
                                                        class="text-xs lg:text-sm text-gray-600 leading-relaxed bg-gray-50 rounded-lg p-3 detail-visi"
                                                    ></p>
                                                </div>

                                                <div>
                                                    <div
                                                        class="flex items-center gap-2 mb-2"
                                                    >
                                                        <div
                                                            class="w-1 h-4 rounded-full bg-accent"
                                                        ></div>
                                                        <h4
                                                            class="text-xs lg:text-sm font-semibold text-gray-700 uppercase tracking-wide"
                                                        >
                                                            Misi
                                                        </h4>
                                                    </div>
                                                    <p
                                                        class="text-xs lg:text-sm text-gray-600 leading-relaxed bg-gray-50 rounded-lg p-3 whitespace-pre-line detail-misi"
                                                    ></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="text-center mt-12">
                            <button
                                type="submit"
                                id="voteButton"
                                disabled
                                class="bg-gray-400 text-white py-4 px-12 rounded-2xl font-bold text-lg transition-all duration-300 cursor-not-allowed"
                            >
                                Pilih Calon Favorit
                            </button>
                            <p class="text-sm text-gray-500 mt-3">
                                Silakan pilih salah satu calon terlebih dahulu
                            </p>
                        </div>
                    </form>
                </div>
            </main>

            <footer class="bg-secondary border-t border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            Pilketos v2.0 FOSS - Sistem Pemilihan Ketua OSIS
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Made with $20 Claude subscription by Sattar
                        </p>
                    </div>
                </div>
            </footer>
        </div>

        <script type="application/json" id="calons-data">
            @json($calons)
        </script>
    </body>
</html>