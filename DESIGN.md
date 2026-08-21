# Pilketos Design System

> Design reference for all UI components and pages. Everything built for this app — dashboard, forms, panels, cards — must follow this document. Derived from the voting page and extended for a full dashboard.

---

## 1. Stack & Tooling

| Concern | Tool |
|---|---|
| CSS framework | Tailwind CSS v4 (`@import "tailwindcss"`, CSS-first config via `@theme`) |
| Font delivery | Bunny Fonts via `@fonts` directive (self-hosted at build time, no CDN) |
| JS framework | Alpine.js (reactive state, no CDN) |
| Icons | Lucide (`lucide` npm, `createIcons()` — no CDN) |
| Toast notifications | Notyf (no CDN) |
| Modals / alerts | SweetAlert2 (no CDN) |
| Build | Vite 8 + `@tailwindcss/vite` |

**Rule:** Never use CDN links for any asset. All dependencies must be npm/composer packages loaded via Vite or `@fonts`.

---

## 2. Color Palette

Defined in `resources/css/app.css` under `@theme`. Use CSS variable names as Tailwind utilities (`bg-primary`, `text-birupesat`, etc.).

### Brand Colors

| Token | Hex | Usage |
|---|---|---|
| `primary` | `#fbfafb` | Page background, body |
| `secondary` | `#fffefe` | Footer, sidebar, surface elevated 1 |
| `accent` | `#232322` | Primary text, headings, strong labels |
| `birupesat` | `#2f2575` | Brand primary — borders active, icons, accents |
| `birupesat-hover` | `#221a56` | Hover state of `birupesat` elements |
| `ink` | `#1a1a1b` | Dark text, button backgrounds |

### Semantic Colors

| Token | Hex | Usage |
|---|---|---|
| `danger` | `#dc2626` | Destructive actions, error badges |
| `error` | `#ef4444` | Form validation errors |
| `success` | `#10b981` | Success states, confirmations |
| `warning` | `#f59e0b` | Warnings, caution states |
| `info-hover` | `#1d4ed8` | Info link hover |
| `question` | `#3b82f6` | Info / neutral prompts |

### Neutral Palette (Tailwind defaults)

Use Tailwind's built-in gray scale for supporting text and borders:

| Use case | Class |
|---|---|
| Borders default | `border-gray-200` |
| Borders subtle | `border-gray-100` |
| Text muted | `text-gray-500` |
| Text secondary | `text-gray-600` |
| Text label | `text-gray-700` |
| Background subtle | `bg-gray-50` |
| Background mid | `bg-gray-100` |
| Background gradient | `from-gray-50 to-gray-200` |

---

## 3. Typography

**Font family:** `Montserrat` — all weights 300–900 loaded locally via Bunny.

```css
body {
    font-family: 'Montserrat', sans-serif;
}
```

### Type Scale

| Role | Mobile | Desktop (lg+) | Weight | Color |
|---|---|---|---|---|
| Page title / H1 | `text-2xl` | `text-4xl` | `font-bold` (700) | `text-accent` |
| Card title / H2 | `text-lg` | `text-2xl` | `font-bold` (700) | `text-accent` |
| Section header / H3 | `text-base` | `text-xl` | `font-semibold` (600) | `text-accent` |
| Label / H4 | `text-xs` | `text-sm` | `font-semibold` (600) | `text-gray-700` uppercase `tracking-wide` |
| Body | `text-sm` | `text-base` | `font-normal` (400) | `text-gray-600` |
| Caption / meta | `text-xs` | `text-sm` | `font-medium` (500) | `text-gray-500` |
| Footer | `text-sm` / `text-xs` | same | 400 / 500 | `text-gray-600` / `text-gray-500` |

### Number / Nomor Display

Large decorative numbers (e.g., candidate number watermark):
```
text-6xl lg:text-9xl font-bold opacity-20
```

---

## 4. Spacing & Layout

### Page Shell

```html
<div class="flex flex-col min-h-screen">
    <main class="flex-grow ...">...</main>
    <footer>...</footer>
</div>
```

### Content Container

```html
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
```

### Section Gaps

| Context | Gap |
|---|---|
| Between major page sections | `space-y-8` |
| Between cards in a grid | `gap-2` mobile / `gap-8` lg |
| Between items in a card | `space-y-3` |
| Between inline elements | `gap-2` or `gap-3` |

### Responsive Breakpoints

This app uses Tailwind's standard breakpoints. The primary responsive jump is **mobile → lg (1024px)**. Always provide both mobile and `lg:` values for sizing, spacing, and font scale.

---

## 5. Borders & Radius

| Element | Radius |
|---|---|
| Cards, panels, modals | `rounded-xl` (0.75rem) |
| Buttons primary | `rounded-2xl` (1rem) |
| SweetAlert2 popups | `rounded-[1.5rem]` (1.5rem) |
| Tags, badges | `rounded-full` |
| Decorative bar (section marker) | `rounded-full` |
| Content blocks inside panels | `rounded-lg` (0.5rem) |

### Border Widths

| State | Class |
|---|---|
| Default card | `border-2 border-gray-200` |
| Hovered card | `border-2 border-birupesat` |
| Selected card | `border-2 border-birupesat` |
| Detail panel | `border-2 border-birupesat` |
| Footer | `border-t border-gray-200` |

---

## 6. Shadows

| Element | Class |
|---|---|
| Card default | `shadow-lg` |
| Card hover / selected | `shadow-xl` + custom `box-shadow` via CSS |
| Detail panel | `shadow-xl` |
| SweetAlert2 | `0 25px 50px -12px rgba(0,0,0,0.25)` |

Card hover shadow (in `app.css`):
```css
box-shadow: 0 10px 25px -5px rgba(47, 37, 117, 0.3);  /* hover */
box-shadow: 0 10px 25px -5px rgba(47, 37, 117, 0.5);  /* selected */
```

---

## 7. Background

### Page Background

Subtle dot-grid on `primary`:
```css
background-color: var(--color-primary);
background-image:
    linear-gradient(rgba(0,0,0,0.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(0,0,0,0.03) 1px, transparent 1px);
background-size: 40px 40px;
```

Apply to any full-page background surface that needs the grid texture.

### Surface Hierarchy

| Level | Color | Usage |
|---|---|---|
| 0 — page | `primary` (`#fbfafb`) | Body, main background |
| 1 — elevated | `white` | Cards, panels, modals |
| 2 — recessed | `gray-50` | Content blocks inside cards/panels |
| 3 — accent surface | `from-gray-50 to-gray-200` (gradient) | Photo/image areas in cards |

---

## 8. Components

### 8.1 Button

#### States

| State | Classes |
|---|---|
| Disabled | `bg-gray-400 text-white cursor-not-allowed` |
| Active / enabled | `bg-ink text-white cursor-pointer` |
| Active hover | `hover:bg-birupesat` (or `-hover` variant) |

#### Sizes

| Size | Classes |
|---|---|
| Primary (CTA) | `py-4 px-12 rounded-2xl font-bold text-lg` |
| Medium | `py-3 px-8 rounded-xl font-semibold text-base` |
| Small | `py-2 px-4 rounded-lg font-medium text-sm` |

#### Transitions

All buttons: `transition-all duration-300`

#### Variants

```html
<!-- Primary CTA -->
<button class="bg-ink text-white py-4 px-12 rounded-2xl font-bold text-lg transition-all duration-300 hover:bg-birupesat">
    Label
</button>

<!-- Danger -->
<button class="bg-danger text-white py-3 px-8 rounded-xl font-semibold text-base transition-all duration-300 hover:opacity-90">
    Hapus
</button>

<!-- Ghost / outline -->
<button class="border-2 border-birupesat text-birupesat py-3 px-8 rounded-xl font-semibold text-base transition-all duration-300 hover:bg-birupesat hover:text-white">
    Label
</button>
```

---

### 8.2 Candidate Card

Structure: outer wrapper → `.card` (white, z-10) + `.detail-panel` (absolute, z-0, initially hidden behind card).

```
.caketos-item
  └── wrapper (relative, w-[10rem] lg:w-[22rem])
       ├── .card (z-10, white, border-2 border-gray-200 rounded-xl)
       │    ├── selection indicator (lucide circle-check, absolute top-right)
       │    ├── hidden radio input
       │    └── label
       │         ├── name header (p-3 lg:p-6 border-b border-gray-100)
       │         ├── photo area (h-[10rem] lg:h-[22rem], gradient bg, overflow-hidden)
       │         │    ├── nomor watermark (absolute, opacity-20, text-6xl lg:text-9xl)
       │         │    └── photo (size-[140%], object-cover, absolute -top-3 -right-9)
       │         └── info row (p-3 lg:p-6 — KELAS label + value)
       └── .detail-panel (absolute top-[5%] left-0, z-0, border-birupesat)
            └── .detail-panel-scroll (p-4 pl-7 lg:p-6 lg:pl-9, overflow-y-auto)
                 ├── nama + kelas header
                 ├── visi block (bar marker: bg-birupesat + label + bg-gray-50 text)
                 └── misi block (bar marker: bg-accent + label + bg-gray-50 text whitespace-pre-line)
```

**Selection indicator:** Lucide `circle-check`, `opacity-0` by default, `opacity-100` when `.selected` via CSS.

**Detail panel behaviour:**
- Default: `transform: translateX(0)`, hidden behind card (`z-0` < card `z-10`).
- Expanded right (normal): JS animates to `translateX(+(cardWidth - 10)px)`.
- Expanded left (first of 2 candidates, lg+ only): JS animates to `translateX(-(cardWidth - 10)px)`.
- Scrollbar: custom thin 4px, `bg-birupesat`, via `.detail-panel-scroll` in app.css. Left panel gets `direction: rtl` on container + `direction: ltr` on children.

---

### 8.3 Stats Card

For the dashboard. Clean metric display.

```html
<div class="bg-white rounded-xl border-2 border-gray-200 shadow-lg p-6 space-y-2">
    <!-- Icon + label row -->
    <div class="flex items-center gap-2">
        <div class="w-1 h-4 rounded-full bg-birupesat"></div>
        <span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Total Suara</span>
    </div>
    <!-- Value -->
    <p class="text-4xl font-bold text-accent">128</p>
    <!-- Sub-label -->
    <p class="text-sm text-gray-500">dari 300 pemilih terdaftar</p>
</div>
```

---

### 8.4 Section Marker

Thin vertical bar before a section label — used in detail panels and section headers:

```html
<div class="flex items-center gap-2 mb-2">
    <div class="w-1 h-4 rounded-full bg-birupesat"></div>
    <h4 class="text-xs lg:text-sm font-semibold text-gray-700 uppercase tracking-wide">
        Visi
    </h4>
</div>
```

Use `bg-birupesat` for primary sections, `bg-accent` for secondary sections.

---

### 8.5 Content Block

Recessed content area (text, lists inside a card):

```html
<p class="text-xs lg:text-sm text-gray-600 leading-relaxed bg-gray-50 rounded-lg p-3">
    Content here.
</p>
```

For multi-line text (e.g., misi list): add `whitespace-pre-line`.

---

### 8.6 Navbar (Top Navigation)

Admin navbar. Sticky top, `h-14`, `bg-secondary`, `border-b`. Layout: `[Logo+Brand] [Nav links] ── spacer ── [User name] [Keluar]`.

```html
<header class="sticky top-0 z-30 bg-secondary border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center h-14 gap-6">
            <!-- Logo + Brand -->
            <a href="..." class="flex items-center gap-2.5 shrink-0">
                <img src="/storage/assets/logo.png" alt="Pilketos" class="h-8 w-auto object-contain">
                <div class="flex flex-col leading-tight">
                    <span class="text-base font-bold text-accent">Pilketos</span>
                    <span class="text-[10px] text-gray-400 font-medium">Stable v2.0</span>
                </div>
            </a>
            <!-- Nav links (use x-nav-link component) -->
            <nav class="hidden md:flex items-stretch h-full gap-0.5">
                <!-- active: bg-black/5 + border-b-2 border-birupesat, no rounded corners -->
                <!-- inactive: text-gray-600 hover:bg-black/5 -->
            </nav>
            <div class="flex-1"></div>
            <!-- Account -->
            <span class="text-sm text-gray-500">{{ auth()->user()->name }}</span>
            <form method="POST" action="...">@csrf
                <button class="text-sm text-gray-500 hover:text-danger">Keluar</button>
            </form>
        </div>
    </div>
</header>
```

**Nav link active state** (via `x-nav-link` component): `bg-black/5 text-accent border-b-2 border-birupesat -mb-px h-full px-3`. No rounded corners, no icons.

**Sidebar has been removed.** The app uses top navbar only.

---

### 8.7 Slide-over Panel (CRUD Forms)

All create/edit/import forms are served in a right-side slide-over panel embedded in the index page — no dedicated create/edit pages.

```html
<div x-data="{ panel: null, editData: {}, openCreate() {...}, openEdit(data) {...}, close() {...} }"
     @keydown.escape.window="close()">

    <!-- Backdrop -->
    <div x-show="panel !== null" x-cloak
         x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="close()"
         class="fixed inset-0 bg-black/40 z-40">
    </div>

    <!-- Panel -->
    <div x-show="panel !== null" x-cloak
         x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 w-full max-w-md bg-white shadow-2xl z-50 flex flex-col">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 shrink-0">
            <h2 class="text-base font-bold text-accent">...</h2>
            <button @click="close()"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>

        <!-- Form section (x-show="panel === 'create'" etc.) -->
        <div x-show="panel === 'create'" x-cloak class="flex-1 overflow-y-auto p-6">
            <form method="POST" action="...">...</form>
        </div>
    </div>
</div>
```

**Widths:** `max-w-md` for simple forms (voter, display-key), `max-w-xl` for calon (more fields + image upload).

**Edit data binding:** pass data inline via `@click="openEdit({id, nama, ..., updateUrl})"`. Use `<input type="hidden" name="_method" value="PUT">` — not `@method('PUT')` inside Alpine-bound forms. Use `x-text="editData.field"` for textareas, `:value` for text inputs.

**`x-cloak` is required** on every `x-show` element in slide-overs. The `[x-cloak] { display: none !important; }` rule in `app.css` prevents flash-of-visible-content on page load.

---

### 8.8 Table

Standard data table for CRUD views.

```html
<div class="bg-white rounded-xl border-2 border-gray-200 shadow-lg overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Nama</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Kelas</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <tr class="hover:bg-gray-50 transition-colors duration-150">
                <td class="px-6 py-4 text-accent font-medium">Shabira Syahla</td>
                <td class="px-6 py-4 text-gray-600">XI-1</td>
                <td class="px-6 py-4 text-right">
                    <!-- action buttons -->
                </td>
            </tr>
        </tbody>
    </table>
</div>
```

---

### 8.9 Form Fields

```html
<!-- Text input -->
<div class="space-y-1.5">
    <label class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Nama</label>
    <input
        type="text"
        class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm text-accent placeholder-gray-400 focus:outline-none focus:border-birupesat transition-colors duration-200"
        placeholder="Nama lengkap"
    />
</div>

<!-- Textarea -->
<div class="space-y-1.5">
    <label class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Visi</label>
    <textarea
        class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm text-accent placeholder-gray-400 focus:outline-none focus:border-birupesat transition-colors duration-200 resize-none"
        rows="4"
        placeholder="Tuliskan visi..."
    ></textarea>
</div>

<!-- Select -->
<div class="space-y-1.5">
    <label class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Kelas</label>
    <select class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm text-accent focus:outline-none focus:border-birupesat transition-colors duration-200">
        <option>XI-1</option>
    </select>
</div>
```

**Focus state:** always `focus:border-birupesat`, `focus:outline-none`.  
**Error state:** `border-error` + `text-error text-xs mt-1` message below field.

---

### 8.10 Badge / Tag

```html
<!-- Status active -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-success/10 text-success">
    Aktif
</span>

<!-- Status inactive -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
    Nonaktif
</span>

<!-- Number badge -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-birupesat text-white">
    01
</span>
```

---

### 8.11 Footer

```html
<footer class="bg-secondary border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <div class="text-center">
            <p class="text-sm text-gray-600">Pilketos v2.0 FOSS - Sistem Pemilihan Ketua OSIS</p>
            <p class="text-xs text-gray-500 mt-1">Made with $20 Claude subscription by Sattar</p>
        </div>
    </div>
</footer>
```

---

### 8.12 Modal / Dialog

Handled by SweetAlert2. Global styles in `app.css` (see section 9). Do not build custom modals — use Swal for all confirmations, alerts, and input dialogs.

Swal customisation conventions:
- `borderRadius: '1.5rem'`
- `fontFamily: 'Montserrat'`
- Confirm: `bg-ink`, `rounded-xl`, `py-3 px-8`
- Cancel: `bg-gray-500`, same sizing
- Icons override: match semantic colors from `@theme`

---

### 8.13 Custom Scrollbar

Applied via `.detail-panel-scroll` class in `app.css`. Reuse on any scrollable panel:

```html
<div class="detail-panel-scroll overflow-y-auto h-full">
    ...
</div>
```

- Width: 4px
- Track: transparent
- Thumb: `birupesat`, `border-radius: 999px`
- Thumb hover: `birupesat-hover`
- Firefox: `scrollbar-width: thin`
- Left-side scrollbar: add `.scrollbar-left` class (uses `direction: rtl` + inner `direction: ltr`)

---

## 9. Animation & Transitions

### Standard Transitions

| Use case | Classes |
|---|---|
| All interactive elements | `transition-all duration-300` |
| Color-only transitions | `transition-colors duration-200` |
| Opacity only | `transition-opacity duration-150 ease-in-out` |

### JS-Driven Animations

Panel slide (rAF + ease-in-out):
- Duration: 400ms
- Easing: `t < 0.5 ? 2t² : 1 - (-2t+2)²/2` (ease-in-out quad)
- Cancels previous animation before starting

Do not mix CSS transitions with rAF animations on the same property.

---

## 10. Blade Component Rules

All UI elements that repeat across pages **must** be extracted as Blade components under `resources/views/components/`. No copy-pasting HTML blocks.

### Planned Component Inventory

| Component | Blade tag | Description |
|---|---|---|
| App layout | `<x-layouts.app>` | Shell: header + sidebar + main + footer |
| Topbar | `<x-topbar>` | Sticky top nav |
| Sidebar | `<x-sidebar>` | Left nav with active state |
| Page header | `<x-page-header>` | Title + breadcrumb + action slot |
| Stats card | `<x-stats-card>` | Single metric display |
| Candidate card | `<x-candidate-card>` | Full voting card with detail panel |
| Table | `<x-data-table>` | Responsive table wrapper |
| Button | `<x-button>` | Variant-aware button |
| Badge | `<x-badge>` | Status/label badge |
| Form field | `<x-form.input>`, `<x-form.textarea>`, `<x-form.select>` | Labeled fields with error state |
| Section marker | `<x-section-marker>` | Bar + label heading |
| Footer | `<x-footer>` | App footer |

### Component Conventions

- Each component receives props via `@props([])`.
- Default slot for inner content.
- Named slots (`@slot('actions')`, `@slot('header')`) for flexible composition.
- Use `$attributes->merge(['class' => '...'])` to allow class overrides from parent.
- Never hardcode colors — always use CSS variable tokens.

---

## 11. Dashboard Layout

```
┌─────────────────────────────────────────────┐
│  Topbar (sticky, z-50, h-16)                │
├──────────┬──────────────────────────────────┤
│          │  Page Header (title + actions)   │
│ Sidebar  ├──────────────────────────────────┤
│ (w-64)   │  Stats Row (grid-cols-2 lg:4)    │
│          ├──────────────────────────────────┤
│          │  Main Content (table / form)     │
│          │                                  │
├──────────┴──────────────────────────────────┤
│  Footer                                     │
└─────────────────────────────────────────────┘
```

Main content area: `flex-1 p-6 lg:p-8 overflow-auto bg-primary` (grid background applies).

---

## 12. Icons

**Library:** Lucide (via `lucide` npm, `createIcons()`).  
**Size:** always sized via font-size (`text-lg`, `text-2xl`) or explicit `w-4 h-4` classes since Lucide copies classes to the rendered `<svg>`.  
**Color:** inherits `currentColor` — set via `text-{color}` on the `<i>` element.

```html
<i data-lucide="icon-name" class="w-4 h-4 text-birupesat"></i>
```

Call `createIcons({ icons: { ... } })` in `alpine:initialized` — pass only the icons you use to keep bundle size small.

---

## 13. Accessibility

- All interactive elements must be keyboard accessible.
- Form inputs must have associated `<label>` elements (via `for`/`id` or wrapping).
- Buttons with icon-only content must have `aria-label`.
- Color is never the sole differentiator of state — always pair with text, icon, or border change.
- Focus rings: use `focus:outline-none focus:ring-2 focus:ring-birupesat focus:ring-offset-2` on focusable elements where the default ring is removed.
- Images must have descriptive `alt` attributes.

---

## 14. Writing New Pages

Checklist before shipping a new page or component:

1. Read this document and `app.css` `@theme` tokens first.
2. Use existing component tags — do not duplicate HTML.
3. Match the responsive pattern: mobile styles first, `lg:` overrides.
4. No inline styles except for JS-driven dynamic transforms.
5. No CDN links — all assets via npm + Vite.
6. No hardcoded hex colors in Blade or JS — reference CSS variables.
7. Run `npm run build` after any CSS/JS change.
8. Run `php artisan test --compact` to verify regressions.
