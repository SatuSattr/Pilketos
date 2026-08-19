import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import {
    Chart,
    LineController,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    TimeScale,
    Tooltip,
    Legend,
    Filler,
} from 'chart.js';
import 'chartjs-adapter-date-fns';
import zoomPlugin from 'chartjs-plugin-zoom';
import {
    createIcons,
    CircleCheck,
    LayoutDashboard,
    Users,
    ListChecks,
    KeyRound,
    LineChart,
    LogOut,
    LogIn,
    UserPlus,
    ListPlus,
    Pencil,
    Trash2,
    Save,
    ArrowLeft,
    ArrowRight,
    Plus,
    Upload,
    RotateCcw,
    AlertCircle,
    CircleX,
    ZoomIn,
    ZoomOut,
    Play,
    Pause,
    RefreshCw,
    Search,
    Menu,
    X,
    Trophy,
    Check,
    Vote,
    UserCircle,
    Clock,
    Activity,
                Copy,
                CopyCheck,

} from 'lucide';

// Register only the Chart.js components we need
Chart.register(LineController, LineElement, PointElement, CategoryScale, LinearScale, TimeScale, Tooltip, Legend, Filler, zoomPlugin);

document.addEventListener('alpine:init', () => {
    // Only register voting data if the voting page element exists
    if (document.getElementById('calons-data')) {
        Alpine.data('voting', () => ({
            calons: JSON.parse(
                document.getElementById('calons-data').textContent,
            ),
        }));
    }
});

document.addEventListener('alpine:initialized', () => {
    // Only run voting logic if we're on the voting page
    if (!document.getElementById('votingForm')) {
        createIcons({
            icons: {
                CircleCheck,
                LayoutDashboard,
                Users,
                ListChecks,
                KeyRound,
                LineChart,
                LogOut,
                LogIn,
                UserPlus,
                ListPlus,
                Pencil,
                Trash2,
                Save,
                ArrowLeft,
                ArrowRight,
                Plus,
                Upload,
                RotateCcw,
                AlertCircle,
                CircleX,
                ZoomIn,
                ZoomOut,
                Play,
                Pause,
                RefreshCw,
                Search,
                Menu,
                X,
                Trophy,
                Check,
                Vote,
                UserCircle,
                Clock,
                Activity,
                Copy,
                CopyCheck,
            },
        });

        // Init vote chart on dashboard if canvas exists
        const chartCanvas = document.getElementById('voteChart');
        const chartUrlEl = document.getElementById('vote-chart-url');
        if (chartCanvas && chartUrlEl) {
            const chartUrl = chartUrlEl.dataset.url;
            const style = getComputedStyle(document.documentElement);
            const colorAccent = style.getPropertyValue('--color-accent').trim() || '#232322';

            // Warna per calon (index-based)
            const SERIES_COLORS = ['#2f2575', '#10b981', '#f59e0b', '#ef4444', '#3b82f6'];

            let voteChart = null;

            function buildDatasets(series) {
                return series.map((calon, i) => ({
                    label: `No. ${calon.nomor} – ${calon.nama}`,
                    data: calon.points.map((p) => ({ x: p.t, y: p.y })),
                    borderColor: SERIES_COLORS[i % SERIES_COLORS.length],
                    backgroundColor: SERIES_COLORS[i % SERIES_COLORS.length] + '18',
                    borderWidth: 3,
                    pointBackgroundColor: SERIES_COLORS[i % SERIES_COLORS.length],
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    tension: 0.4,
                    fill: true,
                    stepped: false,
                }));
            }

            function buildLegend(series) {
                const el = document.getElementById('chartLegend');
                if (!el) { return; }
                el.innerHTML = series.map((calon, i) => `
                    <span class="flex items-center gap-1.5 text-xs font-semibold" style="color:${SERIES_COLORS[i % SERIES_COLORS.length]}">
                        <span class="inline-block w-3 h-3 rounded-full" style="background:${SERIES_COLORS[i % SERIES_COLORS.length]}"></span>
                        No. ${calon.nomor} – ${calon.nama}
                    </span>
                `).join('');
            }

            async function fetchAndRender() {
                const res = await fetch(chartUrl, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const series = await res.json();

                if (!voteChart) {
                    voteChart = new Chart(chartCanvas, {
                        type: 'line',
                        data: { datasets: buildDatasets(series) },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: (ctx) => ` ${ctx.dataset.label}: ${ctx.parsed.y} suara`,
                                    },
                                },
                                zoom: {
                                    pan: {
                                        enabled: true,
                                        mode: 'x',
                                    },
                                    zoom: {
                                        wheel: { enabled: true },
                                        pinch: { enabled: true },
                                        mode: 'x',
                                    },
                                },
                            },
                            scales: {
                                x: {
                                    type: 'time',
                                    time: {
                                        tooltipFormat: 'HH:mm:ss',
                                        displayFormats: {
                                            second: 'HH:mm:ss',
                                            minute: 'HH:mm',
                                            hour: 'HH:mm',
                                        },
                                    },
                                    grid: { display: false },
                                    ticks: {
                                        color: colorAccent,
                                        font: { family: 'Montserrat', size: 11 },
                                        maxTicksLimit: 8,
                                        maxRotation: 0,
                                    },
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        color: colorAccent,
                                        font: { family: 'Montserrat', size: 11 },
                                    },
                                    grid: { color: 'rgba(0,0,0,0.05)' },
                                },
                            },
                        },
                    });
                } else {
                    voteChart.data.datasets = buildDatasets(series);
                    voteChart.update('none');
                }

                buildLegend(series);
            }

            // Zoom control buttons
            document.getElementById('chartZoomIn')?.addEventListener('click', () => {
                voteChart?.zoom(1.3);
            });
            document.getElementById('chartZoomOut')?.addEventListener('click', () => {
                voteChart?.zoom(0.7);
            });
            document.getElementById('chartZoomReset')?.addEventListener('click', () => {
                voteChart?.resetZoom();
            });

            // Initial render + auto-refresh every 30 seconds
            fetchAndRender();
            setInterval(fetchAndRender, 30000);
        }

        // --- Admin helpers ---

        // SweetAlert2 Toast mixin
        const AdminToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            customClass: {
                popup: 'rounded-[1.5rem] font-sans shadow-xl',
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            },
        });

        /**
         * Show a toast notification.
         * @param {'success'|'error'|'warning'|'info'} type
         * @param {string} message
         */
        window.adminToast = function (type, message) {
            AdminToast.fire({ icon: type, title: message });
        };

        /**
         * Show a SweetAlert2 confirm dialog, then submit a form if confirmed.
         * @param {Event} e
         * @param {string} title
         * @param {string} text
         * @param {string} confirmLabel
         * @param {'danger'|'warning'} variant  'danger' = red button, 'warning' = yellow
         */
        window.adminConfirm = function (e, title, text, confirmLabel = 'Ya, lanjutkan', variant = 'danger') {
            e.preventDefault();
            const form = e.target.closest('form') ?? e.target;
            const confirmColor = variant === 'danger' ? '#dc2626' : '#f59e0b';
            Swal.fire({
                title,
                text,
                icon: variant === 'danger' ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonText: confirmLabel,
                cancelButtonText: 'Batal',
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#6b7280',
                borderRadius: '1.5rem',
                customClass: {
                    popup: 'rounded-[1.5rem] font-sans',
                    confirmButton: 'rounded-xl py-3 px-8 font-semibold',
                    cancelButton: 'rounded-xl py-3 px-8 font-semibold',
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        };

        // Fire flash toast from session (data attr on body)
        const flashType = document.body.dataset.flashType;
        const flashMsg  = document.body.dataset.flashMsg;
        if (flashType && flashMsg) {
            AdminToast.fire({ icon: flashType, title: flashMsg });
        }

        return;
    }

    const candidateRadios = document.querySelectorAll('.candidate-radio');
    const candidateCards = document.querySelectorAll('.card');
    const voteButton = document.getElementById('voteButton');
    const allItems = document.querySelectorAll('.caketos-item');
    let currentlyExpanded = null;
    const activeAnimations = new Map();

    function ease(t) {
        return t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;
    }

    function cancelAnimation(el) {
        const existing = activeAnimations.get(el);
        if (existing) {
            cancelAnimationFrame(existing);
            activeAnimations.delete(el);
        }
    }

    function animateValue(el, from, to, duration, callback, onDone) {
        cancelAnimation(el);
        const start = performance.now();

        function step(now) {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            const eased = ease(progress);
            callback(from + (to - from) * eased);
            if (progress < 1) {
                activeAnimations.set(el, requestAnimationFrame(step));
            } else {
                activeAnimations.delete(el);
                if (onDone) onDone();
            }
        }
        activeAnimations.set(el, requestAnimationFrame(step));
    }

    function getItemsAfter(item) {
        const after = [];
        allItems.forEach((other) => {
            if (
                other !== item &&
                other.compareDocumentPosition(item) &
                    Node.DOCUMENT_POSITION_PRECEDING
            ) {
                after.push(other);
            }
        });
        return after;
    }

    /**
     * Returns true when the first candidate's panel should slide LEFT:
     * - exactly 2 candidates are rendered, AND
     * - this container is the very first .caketos-item, AND
     * - the viewport is lg+ (≥ 1024 px, Tailwind's lg breakpoint).
     *
     * On mobile or when there are ≠ 2 candidates, all panels slide right.
     */
    function shouldSlideLeft(container) {
        const items = document.querySelectorAll('.caketos-item');
        if (items.length !== 2) {
            return false;
        }
        if (items[0] !== container) {
            return false;
        }
        return window.matchMedia('(min-width: 1024px)').matches;
    }

    function expandPanel(card, container) {
        const detailPanel = container.querySelector('.detail-panel');
        const cardWidth = card.offsetWidth;
        const overlap = 10;
        const slideLeft = shouldSlideLeft(container);
        const slideDistance = slideLeft
            ? -(cardWidth - overlap)
            : cardWidth - overlap;

        // Move scrollbar to the left for the inverted (left-sliding) panel.
        const scrollEl = detailPanel.querySelector('.detail-panel-scroll');
        if (scrollEl) {
            scrollEl.classList.toggle('scrollbar-left', slideLeft);
        }

        detailPanel.querySelector('.detail-visi').textContent = card.dataset.visi;
        detailPanel.querySelector('.detail-misi').textContent = card.dataset.misi;

        animateValue(
            detailPanel,
            0,
            slideDistance,
            400,
            (val) => {
                detailPanel.style.transform = 'translateX(' + val + 'px)';
            },
            () => {
                detailPanel.style.pointerEvents = 'auto';
            },
        );

        // Only push siblings when the panel slides right — a left-sliding panel
        // moves away from the second card, so no push is needed.
        if (!slideLeft) {
            const siblingsAfter = getItemsAfter(container);
            siblingsAfter.forEach((sib) => {
                const currentTransform = sib.style.transform;
                const currentVal = currentTransform
                    ? parseFloat(
                          currentTransform.match(/translateX\((.+)px\)/)?.[1] ||
                              0,
                      )
                    : 0;
                animateValue(
                    sib,
                    currentVal,
                    slideDistance,
                    400,
                    (val) => {
                        sib.style.transform = 'translateX(' + val + 'px)';
                    },
                );
            });
        }

        detailPanel.style.pointerEvents = 'none';
        currentlyExpanded = container;
    }

    function collapsePanel(container) {
        const detailPanel = container.querySelector('.detail-panel');
        const currentTransform = detailPanel.style.transform;
        const currentVal = currentTransform
            ? parseFloat(
                  currentTransform.match(/translateX\((.+)px\)/)?.[1] || 0,
              )
            : 0;

        const siblingsAfter = getItemsAfter(container);

        animateValue(
            detailPanel,
            currentVal,
            0,
            400,
            (val) => {
                detailPanel.style.transform = 'translateX(' + val + 'px)';
            },
            () => {
                detailPanel.style.pointerEvents = 'none';
            },
        );

        siblingsAfter.forEach((sib) => {
            const sibTransform = sib.style.transform;
            const sibCurrent = sibTransform
                ? parseFloat(
                      sibTransform.match(/translateX\((.+)px\)/)?.[1] || 0,
                  )
                : 0;
            animateValue(sib, sibCurrent, 0, 400, (val) => {
                sib.style.transform = 'translateX(' + val + 'px)';
            });
        });

        currentlyExpanded = null;
    }

    // In dual-candidate mode, both panels are always open and non-interactive.
    const isDualMode = allItems.length === 2;

    if (isDualMode) {
        // Mark the flex container so CSS can suppress selected outlines.
        const flexContainer = document.querySelector('[x-data="voting"]');
        if (flexContainer) {
            flexContainer.classList.add('dual-mode');
        }

        // Auto-expand both panels immediately after a short delay so that
        // Alpine has finished rendering and card dimensions are available.
        setTimeout(() => {
            allItems.forEach((container) => {
                const card = container.querySelector('.card');
                if (card) {
                    expandPanel(card, container);
                }
            });
        }, 50);
    }

    candidateCards.forEach((card) => {
        card.addEventListener('click', function (e) {
            if (e.target.tagName === 'LABEL' || e.target.closest('label')) {
                return;
            }

            // In dual mode panels are always open — block all expand/collapse.
            if (isDualMode) {
                return;
            }

            const container = this.closest('.caketos-item');

            if (currentlyExpanded === container) {
                return;
            }

            if (currentlyExpanded) {
                const prevContainer = currentlyExpanded;
                collapsePanel(prevContainer);
                setTimeout(() => expandPanel(this, container), 50);
            } else {
                expandPanel(this, container);
            }
        });
    });

    candidateRadios.forEach((radio) => {
        radio.addEventListener('change', function () {
            candidateCards.forEach((card) => card.classList.remove('selected'));
            if (this.checked) {
                this.closest('.card').classList.add('selected');
                voteButton.disabled = false;
                voteButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
                voteButton.classList.add(
                    'bg-birupesat',
                    'hover:bg-birupesat-hover',
                    'cursor-pointer',
                );
                voteButton.textContent = 'VOTE SEKARANG!';
                voteButton.nextElementSibling.textContent =
                    'Klik untuk memberikan suara Anda';
            }
        });
    });

    // Read CSRF token from meta tag injected in Blade
    function getCsrfToken() {
        return document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content');
    }

    function validateDisplayKey(key) {
        return fetch('/display-key/validate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
            },
            body: JSON.stringify({ key }),
        }).then((res) => res.json());
    }

    function showTokenPopup() {
        Swal.fire({
            title: 'Masukkan Display Key',
            input: 'text',
            inputPlaceholder: 'Contoh: ABCD1234',
            allowOutsideClick: false,
            allowEscapeKey: false,
            backdrop: true,
            inputAttributes: {
                autocapitalize: 'characters',
                autocorrect: 'off',
                style: 'text-transform: uppercase; font-family: monospace; letter-spacing: 0.1em;',
            },
            preConfirm: (key) => {
                if (!key) {
                    Swal.showValidationMessage('Key tidak boleh kosong');
                    return false;
                }
                return validateDisplayKey(key.toUpperCase()).then((data) => {
                    if (!data.success) {
                        Swal.showValidationMessage(
                            data.message || 'Key tidak valid atau tidak aktif',
                        );
                        return false;
                    }
                    sessionStorage.setItem('display_token', key.toUpperCase());
                    return true;
                });
            },
        });
    }

    document.getElementById('votingForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const displayKey = sessionStorage.getItem('display_token');
        if (!displayKey) {
            Swal.fire({
                icon: 'error',
                title: 'Key Hilang',
                text: 'Silakan masukkan display key terlebih dahulu.',
            }).then(() => showTokenPopup());
            return;
        }

        const selectedCandidate = document.querySelector(
            'input[name="id_calon"]:checked',
        );
        if (!selectedCandidate) {
            Swal.fire({
                icon: 'warning',
                title: 'Belum Memilih',
                text: 'Silakan pilih salah satu calon terlebih dahulu!',
                confirmButtonText: 'OK, Mengerti',
            });
            return;
        }

        const candidateCard = selectedCandidate.closest('.card');
        const candidateName = candidateCard
            .querySelector('h3')
            .textContent.trim()
            .replace(/\s+/g, ' ');

        Swal.fire({
            icon: 'question',
            title: 'Konfirmasi Pilihan',
            html: `Apakah Anda yakin ingin memilih <strong>${candidateName}</strong> sebagai calon ketua OSIS?`,
            input: 'text',
            inputLabel: 'Masukkan Nama Anda',
            inputPlaceholder: 'Contoh: Ahmad Sattar',
            inputAttributes: {
                maxlength: 200,
                autocapitalize: 'off',
                autocorrect: 'off',
            },
            showCancelButton: true,
            confirmButtonText: 'Ya, Pilih Calon Ini',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            preConfirm: (namaPemilih) => {
                if (!namaPemilih || !namaPemilih.trim()) {
                    Swal.showValidationMessage('Nama tidak boleh kosong!');
                    return false;
                }
                return namaPemilih.trim();
            },
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            Swal.fire({
                title: 'Memproses Vote...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading(),
            });

            fetch('/vote', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify({
                    calon_id: parseInt(selectedCandidate.value),
                    nama_pemilih: result.value,
                    display_key: displayKey,
                }),
            })
                .then((res) => res.json())
                .then((data) => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Vote Berhasil!',
                            html: 'Terima kasih telah berpartisipasi dalam pemilihan ketua OSIS!',
                            confirmButtonText: 'Selesai',
                            timer: 5000,
                            timerProgressBar: true,
                        }).then(() => resetForm());
                    } else {
                        // Specific error handling
                        if (
                            data.type === 'ambiguous' ||
                            data.type === 'not_found'
                        ) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Nama Tidak Dikenali',
                                text: data.message,
                                confirmButtonText: 'Coba Lagi',
                            }).then(() => {
                                // Re-trigger the voting form submit to show the name dialog again
                                document
                                    .getElementById('votingForm')
                                    .dispatchEvent(new Event('submit'));
                            });
                        } else if (data.type === 'already_voted') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Sudah Memilih',
                                text: data.message,
                            });
                        } else if (data.message?.includes('Key')) {
                            // Key invalid/expired, force re-auth
                            sessionStorage.removeItem('display_token');
                            Swal.fire({
                                icon: 'error',
                                title: 'Key Tidak Valid',
                                text: data.message,
                            }).then(() => showTokenPopup());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Terjadi kesalahan.',
                            });
                        }
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Koneksi Gagal',
                        text: 'Gagal menghubungi server. Silakan coba lagi.',
                    });
                });
        });
    });

    function resetForm() {
        document.getElementById('votingForm').reset();
        candidateCards.forEach((card) => card.classList.remove('selected'));
        voteButton.disabled = true;
        voteButton.classList.remove(
            'bg-birupesat',
            'hover:bg-birupesat-hover',
            'cursor-pointer',
        );
        voteButton.classList.add('bg-gray-400', 'cursor-not-allowed');
        voteButton.textContent = 'Pilih Calon Favorit';
        voteButton.nextElementSibling.textContent =
            'Silakan pilih salah satu calon terlebih dahulu';
        activeAnimations.forEach((id, el) => cancelAnimationFrame(id));
        activeAnimations.clear();
        allItems.forEach((item) => {
            item.style.transform = '';
            const panel = item.querySelector('.detail-panel');
            if (panel) {
                panel.style.transform = 'translateX(0)';
                panel.style.pointerEvents = 'none';
            }
        });
        currentlyExpanded = null;

        // In dual mode, immediately re-expand both panels after reset.
        if (isDualMode) {
            setTimeout(() => {
                allItems.forEach((container) => {
                    const card = container.querySelector('.card');
                    if (card) {
                        expandPanel(card, container);
                    }
                });
            }, 50);
        }
    }

    const savedKey = sessionStorage.getItem('display_token');

    if (!savedKey) {
        showTokenPopup();
    } else {
        validateDisplayKey(savedKey).then((data) => {
            if (!data.success) {
                sessionStorage.removeItem('display_token');
                showTokenPopup();
            }
        });
    }

    createIcons({ icons: { CircleCheck } });
});

window.Alpine = Alpine;
Alpine.start();
