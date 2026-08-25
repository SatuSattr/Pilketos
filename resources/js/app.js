import '@hotwired/turbo';
import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';
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

// --- Admin helpers (defined once at module scope, survive Turbo navigations) ---

/**
 * Resolve a CSS custom property to its computed hex/rgb value.
 * Notyf injects `background` as an inline style, so `var(--x)` doesn't work there.
 * @param {string} token  e.g. '--color-success'
 * @param {string} fallback  hex fallback if token is not found
 */
function cssVar(token, fallback) {
    const val = getComputedStyle(document.documentElement).getPropertyValue(token).trim();
    return val || fallback;
}

function makeNotyf() {
    return new Notyf({
        duration: 3500,
        position: { x: 'right', y: 'top' },
        dismissible: true,
        ripple: false,
        types: [
            {
                type: 'success',
                background: '#ffffff',
                icon: false,
            },
            {
                type: 'error',
                background: '#ffffff',
                icon: false,
            },
            {
                type: 'warning',
                background: '#ffffff',
                className: 'notyf-warning',
                icon: false,
            },
            {
                type: 'info',
                background: '#ffffff',
                className: 'notyf-info',
                icon: false,
            },
        ],
    });
}

// Created lazily on first use so the DOM (and CSS) is ready when we read the tokens.
let _notyf = null;
function getNotyf() {
    if (!_notyf) { _notyf = makeNotyf(); }
    return _notyf;
}

/**
 * Copy text to clipboard with fallback for insecure contexts (http://192.168.x.x).
 * Uses navigator.clipboard when available and secure, otherwise falls back to
 * a hidden textarea + document.execCommand('copy').
 * @param {string} text
 * @returns {Promise<boolean>} true if copied
 */
window.copyToClipboard = async function (text) {
    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text);
            return true;
        }
        throw new Error('insecure-context');
    } catch (_e) {
        try {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.setAttribute('readonly', '');
            ta.style.position = 'fixed';
            ta.style.left = '-9999px';
            ta.style.top = '-9999px';
            document.body.appendChild(ta);
            ta.focus();
            ta.select();
            ta.setSelectionRange(0, ta.value.length);
            const ok = document.execCommand('copy');
            document.body.removeChild(ta);
            return ok;
        } catch (_e2) {
            return false;
        }
    }
};

/**
 * Show a toast notification.
 * @param {'success'|'error'|'warning'|'info'} type
 * @param {string} message
 */
window.adminToast = function (type, message) {
    const n = getNotyf();
    if (type === 'warning' || type === 'info') {
        n.open({ type, message });
    } else {
        n[type === 'error' ? 'error' : 'success'](message);
    }
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

/**
 * Runs on every page (re)load — Turbo soft-navigations AND hard full-page loads.
 * `turbo:load` covers Turbo soft-navigations; `DOMContentLoaded` covers the initial
 * hard navigation (e.g. redirect after login) where Turbo hasn't intercepted anything.
 * We guard with a flag so the handler never runs twice on the same page.
 */
let _pageInitDone = false;

// ── Dashboard poll lifecycle ──────────────────────────────────────────
// Stored at module scope so it survives Turbo soft-navigations.
// Must be cleared on every navigation and on 401/419 to avoid spam.
let dashboardPollTimer = null;
function stopDashboardPolling() {
    if (dashboardPollTimer !== null) {
        clearInterval(dashboardPollTimer);
        dashboardPollTimer = null;
    }
}

function onPageReady() {
    // Avoid running twice on the same page (turbo:load + DOMContentLoaded can both fire).
    if (_pageInitDone) { return; }
    _pageInitDone = true;

    // Reset flag on the next Turbo navigation so it runs again for the next page.
    document.addEventListener('turbo:before-visit', () => { _pageInitDone = false; }, { once: true });

    // Skip if on the voting page (fallback safety guard)
    if (document.getElementById('votingForm')) {
        return;
    }

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

    // Fire flash toast from session (data attr on body) — re-reads on every navigation
    const flashType = document.body.dataset.flashType;
    const flashMsg  = document.body.dataset.flashMsg;
    if (flashType && flashMsg) {
        window.adminToast(flashType, flashMsg);
    }

    // ── Dashboard realtime polling ───────────────────────────────────────────
    const chartCanvas = document.getElementById('voteChart');
    const chartUrlEl  = document.getElementById('vote-chart-url');
    const statsUrlEl  = document.getElementById('dashboard-stats-url');

    if (chartCanvas && chartUrlEl && statsUrlEl) {
        const chartUrl = chartUrlEl.dataset.url;
        const statsUrl = statsUrlEl.dataset.url;
        const style    = getComputedStyle(document.documentElement);
        const colorAccent = style.getPropertyValue('--color-accent').trim() || '#232322';

        const SERIES_COLORS = ['#2f2575', '#10b981', '#f59e0b', '#ef4444', '#3b82f6'];

        let voteChart = null;

        // Smooth integer counter animation (easeOutCubic)
        const runningAnimations = new Map();
        function animateCounter(el, toValue, duration = 600) {
            const fromValue = parseInt(el.textContent.replace(/\D/g, ''), 10) || 0;
            if (fromValue === toValue) { return; }

            if (runningAnimations.has(el)) {
                cancelAnimationFrame(runningAnimations.get(el));
            }

            const start = performance.now();
            function step(now) {
                const t = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - t, 3); // easeOutCubic
                const current = Math.round(fromValue + (toValue - fromValue) * eased);
                el.textContent = current;
                if (t < 1) {
                    runningAnimations.set(el, requestAnimationFrame(step));
                } else {
                    runningAnimations.delete(el);
                }
            }
            runningAnimations.set(el, requestAnimationFrame(step));
        }

        // ── Stats card updater ───────────────────────────────────────────────
        async function fetchStats() {
            try {
                const res  = await fetch(statsUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (res.status === 401 || res.status === 419 || res.status === 403) {
                    stopDashboardPolling();
                    return;
                }
                if (!res.ok) { return; }
                const data = await res.json();

                // Stats cards — target by id, update [data-stat-value] inside
                const cards = {
                    'stat-total-voters': data.totalVoters,
                    'stat-has-voted':    data.totalHasVoted,
                    'stat-not-voted':    data.totalNotVoted,
                    'stat-active-keys':  data.activeKeys,
                };
                for (const [id, value] of Object.entries(cards)) {
                    const el = document.querySelector(`#${id} [data-stat-value]`);
                    if (el) { animateCounter(el, value); }
                }

                // "Sudah Memilih" sub text (participation rate)
                const subEl = document.querySelector('#stat-has-voted [data-stat-sub]');
                if (subEl) { subEl.textContent = `${data.participationRate}% partisipasi`; }

                // Total votes footer
                const totalVotesEl = document.getElementById('stat-total-votes');
                if (totalVotesEl) { animateCounter(totalVotesEl, data.totalVotes); }

                // Perolehan suara rows
                data.calons.forEach((calon) => {
                    const row = document.querySelector(`[data-calon-id="${calon.id}"]`);
                    if (!row) { return; }

                    const votesEl = row.querySelector('[data-votes]');
                    const pctEl   = row.querySelector('[data-pct]');
                    const barEl   = row.querySelector('[data-bar]');

                    if (votesEl) { animateCounter(votesEl, calon.votes_count); }
                    if (pctEl)   { pctEl.textContent = `(${calon.pct}%)`; }
                    if (barEl)   { barEl.style.width = `${calon.pct}%`; }
                });
            } catch (e) {
                // Silently ignore network errors between polls
            }
        }

        // ── Chart updater ────────────────────────────────────────────────────
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

        async function fetchChart() {
            try {
                const res    = await fetch(chartUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                if (res.status === 401 || res.status === 419 || res.status === 403) {
                    stopDashboardPolling();
                    return;
                }
                if (!res.ok) { return; }
                const series = await res.json();

                if (!voteChart) {
                    voteChart = new Chart(chartCanvas, {
                        type: 'line',
                        data: { datasets: buildDatasets(series) },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            // Disable ALL Chart.js animations — updates are instant & seamless
                            animation: false,
                            animations: false,
                            transitions: { active: { animation: { duration: 0 } } },
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: (ctx) => ` ${ctx.dataset.label}: ${ctx.parsed.y} suara`,
                                    },
                                },
                                zoom: {
                                    pan:  { enabled: true, mode: 'x' },
                                    zoom: { wheel: { enabled: true }, pinch: { enabled: true }, mode: 'x' },
                                },
                            },
                            scales: {
                                x: {
                                    type: 'time',
                                    time: {
                                        tooltipFormat: 'HH:mm:ss',
                                        displayFormats: { second: 'HH:mm:ss', minute: 'HH:mm', hour: 'HH:mm' },
                                    },
                                    grid: { display: false },
                                    ticks: { color: colorAccent, font: { family: 'Montserrat', size: 11 }, maxTicksLimit: 8, maxRotation: 0 },
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: { stepSize: 1, color: colorAccent, font: { family: 'Montserrat', size: 11 } },
                                    grid: { color: 'rgba(0,0,0,0.05)' },
                                },
                            },
                        },
                    });
                } else {
                    // Replace datasets and update with no animation — x-axis shifts naturally
                    // as the time range of the data grows (trading terminal effect)
                    voteChart.data.datasets = buildDatasets(series);
                    voteChart.update('none');
                }

                buildLegend(series);
            } catch (e) {
                // Silently ignore
            }
        }

        // Zoom control buttons
        document.getElementById('chartZoomIn')?.addEventListener('click', () => { voteChart?.zoom(1.3); });
        document.getElementById('chartZoomOut')?.addEventListener('click', () => { voteChart?.zoom(0.7); });
        document.getElementById('chartZoomReset')?.addEventListener('click', () => { voteChart?.resetZoom(); });

        // Initial load — fetch both immediately then poll every 5s
        // Clear any leftover timer from a previous soft-navigation before starting a new one.
        stopDashboardPolling();
        fetchStats();
        fetchChart();
        dashboardPollTimer = setInterval(() => { fetchStats(); fetchChart(); }, 5000);
    } else {
        // Not on dashboard — ensure any previous dashboard timer is stopped
        // (covers the case where turbo:before-visit didn't fire, e.g. initial hard load of non-dashboard page).
        stopDashboardPolling();
    }
}

// Voting page init — runs once on initial load (voting page is Turbo-disabled)
document.addEventListener('alpine:initialized', () => {
    if (!document.getElementById('votingForm')) {
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

// Stop polling on any Turbo navigation — the interval lives at module scope
// and would otherwise survive soft-navigations (e.g. logout -> login).
document.addEventListener('turbo:before-visit', stopDashboardPolling);
document.addEventListener('turbo:before-cache', stopDashboardPolling);
// Also stop when the logout form is submitted directly (hard submit fallback).
document.addEventListener('submit', (e) => {
    if (e.target instanceof HTMLFormElement && e.target.action.includes('/logout')) {
        stopDashboardPolling();
    }
});

// Wire onPageReady to both events:
// - turbo:load  → Turbo soft-navigations between admin pages
// - DOMContentLoaded → hard full-page loads (e.g. redirect after login)
document.addEventListener('turbo:load', onPageReady);
document.addEventListener('DOMContentLoaded', onPageReady);

window.Alpine = Alpine;
Alpine.start();
