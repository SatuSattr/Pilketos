<pilketos-project-context>

## UI/UX & Frontend Rule

**Always read `DESIGN.md` before:**

- Making any UI or UX change (layout, spacing, color, typography, component style)
- Adding a new Blade component or page
- Tweaking existing components

`DESIGN.md` is the single source of truth for colors, typography, spacing, component patterns, and the slide-over CRUD pattern. Changes that deviate from it without explicit user approval are not acceptable.

## What This App Is

Pilketos is a school student council election (Pemilihan Ketua OSIS) web application. It has two surfaces:

1. **Public voting page** (`/`) — students pick a candidate on a touch-friendly card UI, guarded by a per-booth display key.
2. **Admin panel** (`/admin/*`) — authenticated single-user dashboard for managing candidates, voters, booth keys, and monitoring live results.

## Stack

| Layer         | Technology                                                                               |
| ------------- | ---------------------------------------------------------------------------------------- |
| Backend       | Laravel 13, PHP 8.4, SQLite                                                              |
| CSS           | Tailwind CSS v4 (CSS-first `@theme` in `resources/css/app.css`)                          |
| JS            | Alpine.js (reactive state), Vite 8                                                       |
| Icons         | Lucide (npm, `createIcons()` — tree-shaken, no CDN)                                      |
| Alerts/modals | SweetAlert2 (npm, no CDN)                                                                |
| Charts        | Chart.js (tree-shaken) + `chartjs-adapter-date-fns` + `chartjs-plugin-zoom` + `hammerjs` |
| Fonts         | Montserrat via Bunny Fonts (`@fonts` directive, self-hosted at build)                    |

**Hard rules:** No CDN links ever. No hardcoded hex colors in Blade or JS — always use CSS variable tokens.

## Authentication

- Standard Laravel `auth` guard, single admin user.
- Guest redirect target: `admin.login` (set in `bootstrap/app.php` via `redirectGuestsTo`).
- Admin credentials (seeded): `admin@pilketos.local` / `admin123` (`AdminSeeder`).
- Login success flashes `toast_type=success` + `toast_msg` session keys for the toast system.

## Database (SQLite)

| Table          | Purpose                                                                                   |
| -------------- | ----------------------------------------------------------------------------------------- |
| `users`        | Admin account only                                                                        |
| `calons`       | Candidates (`nomor`, `nama`, `kelas`, `visi`, `misi`, `foto`)                             |
| `voters`       | Eligible voters (`nama`, `has_voted`)                                                     |
| `votes`        | Cast votes (`voter_id`, `calon_id`)                                                       |
| `display_keys` | Per-booth access keys (`nama`, `key`, `is_active`, `successful_votes`, `failed_attempts`) |

Foto stored at `storage/foto_calon/` (public disk). Logo assets at `public/storage/assets/` (`logo.png`, `logo_white.png`).

## Routes

All admin routes are under `/admin` prefix, grouped by `auth` middleware. Key routes:

```
GET  /                          voting.index
POST /vote                      voting.vote
POST /display-key/validate      voting.validate-key

GET  admin/dashboard            admin.dashboard
GET  admin/dashboard/chart-data admin.dashboard.chart-data  (JSON API for Chart.js)

admin/calon      → CalonController     (index, store, show, update, destroy)
admin/voter      → VoterController     (index, store, update, destroy + import + reset)
admin/display-key → DisplayKeyController (index, store, destroy + toggle + reset-stats)
```

Note: `create` and `edit` GET routes do **not** exist for any resource — forms are served via slide-over panels embedded in each index page.

## Color Tokens (`@theme` in `app.css`)

| Token             | Hex       | Usage                                  |
| ----------------- | --------- | -------------------------------------- |
| `primary`         | `#fbfafb` | Page background                        |
| `secondary`       | `#fffefe` | Navbar, surface elevated               |
| `accent`          | `#232322` | Primary text / headings                |
| `birupesat`       | `#2f2575` | Brand primary — active states, borders |
| `birupesat-hover` | `#221a56` | Hover of birupesat elements            |
| `ink`             | `#1a1a1b` | Dark button background                 |
| `danger`          | `#dc2626` | Destructive actions                    |
| `error`           | `#ef4444` | Form validation errors                 |
| `success`         | `#10b981` | Success states                         |
| `warning`         | `#f59e0b` | Caution states                         |
| `question`        | `#3b82f6` | Neutral prompts                        |

## Frontend Architecture

### `resources/js/app.js`

Single entry point. Key behaviors:

- **Admin pages** (`#votingForm` absent): runs `createIcons()`, initializes Chart.js dashboard (polling every 30s), exposes two global helpers:
    - `window.adminToast(type, message)` — SweetAlert2 Toast mixin (top-end, 3.5s, progress bar).
    - `window.adminConfirm(event, title, text, confirmLabel, variant)` — SweetAlert2 confirm that submits the closest `<form>` on confirmation.
    - Flash toast fires automatically from `data-flash-type` / `data-flash-msg` on `<body>`.
- **Voting page** (`#votingForm` present): runs candidate card animations, display key validation, voting submission.

### Flash Toast System

Controllers flash `toast_type` (e.g. `'success'`) and `toast_msg` to session. The admin layout reads these onto `<body data-flash-type="..." data-flash-msg="...">`. `app.js` reads them on `alpine:initialized` and fires `adminToast`. Do **not** use the old `session('success')` HTML div pattern — that has been removed.

### `[x-cloak]` Anti-flash

`[x-cloak] { display: none !important; }` is defined at the top of `app.css`. All slide-over panels and their backdrops carry `x-cloak` to prevent flash-of-visible-content on page load.

## Admin Layout (`resources/views/components/layouts/admin.blade.php`)

- Sticky navbar: `[Logo + "Pilketos" + "Stable v2.0"] [Nav links] ── spacer ── [User name] [Keluar]`
- Nav links use `x-nav-link` component — active style is `bg-black/5 + border-b-2 border-birupesat`, no rounded corners, full navbar height.
- Logo: `public/storage/assets/logo.png`.
- No sidebar. No flash message HTML divs. Toast-only feedback.

## Blade Components (`resources/views/components/`)

| Component     | Tag                 | Notes                                                                                                 |
| ------------- | ------------------- | ----------------------------------------------------------------------------------------------------- |
| Admin layout  | `<x-layouts.admin>` | Navbar + main + footer shell                                                                          |
| Nav link      | `<x-nav-link>`      | Replaces old `sidebar-link`; active = bottom border                                                   |
| Page header   | `<x-page-header>`   | Title + optional description                                                                          |
| Stats card    | `<x-stats-card>`    | Single metric display                                                                                 |
| Button        | `<x-button>`        | Variants: `primary`, `secondary`, `danger`, `ghost`. Props: `variant`, `size`, `icon`, `href`, `type` |
| Badge         | `<x-badge>`         | Colors: `green`, `blue`, `gray`                                                                       |
| Form input    | `<x-form.input>`    | With `label`, `name`, `placeholder`, `required`, `hint`, `value`                                      |
| Form textarea | `<x-form.textarea>` | Same as input + `rows`                                                                                |
| Form select   | `<x-form.select>`   |                                                                                                       |

## CRUD Pattern — Slide-over Panels

All create/edit/import forms live inside the index page as right-side slide-over panels, not separate pages. Pattern per index view:

```blade
<div x-data="{ panel: null, editData: {}, openCreate() {...}, openEdit(data) {...}, close() {...} }"
     @keydown.escape.window="close()">

    {{-- table / card list with @click="openEdit({...})" and adminConfirm for deletes --}}

    {{-- Backdrop: x-show="panel !== null" x-cloak --}}
    {{-- Slide-over: x-show="panel !== null" x-cloak, translate-x-full → translate-x-0 --}}
        {{-- Panel sections: x-show="panel === 'create'" etc. --}}
</div>
```

- Edit data is passed inline via Alpine `@click="openEdit({id, nama, ..., updateUrl})"`.
- Forms use `<input type="hidden" name="_method" value="PUT">` (not `@method('PUT')` blade directive inside Alpine-bound forms).
- Textarea values in edit panels use `x-text="editData.field"` (not `:value`).
- Panel widths: `max-w-md` for simple forms, `max-w-xl` for calon (more fields).

## Seeders

- `AdminSeeder` — creates the admin user.
- `CalonSeeder` — sample candidates with photos.
- `DemoSeeder` — 70 voters, 40–55 randomly voted with a 40–65% split between candidates, 4 display keys, wave timestamps ~2 hours ago, round-robin IPs `192.168.1.10–13`.
- All registered in `DatabaseSeeder`.

</pilketos-project-context>

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application running on PHP 8.4. You are an expert with the Laravel ecosystem. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:

- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists (settled decisions, non-obvious traps, standing constraints). Framework and package guidelines that only apply to specific paths (testing, frontend, components) also live there, under `.ai/rules/boost` — this is not just recorded decisions, it is load-bearing guidance you have not seen inline. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.
- Record durable rules with `record-rule` so the next agent or teammate inherits them instead of working them out again. Pass a `glob` (e.g. `app/Http/Controllers/**`), a short `title`, and a few-line `note`. Always use `record-rule`, never your native memory or notes tool — native memory is personal and session-scoped; only `.ai/rules` is shared with the team and persists in the repo.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
    - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

</laravel-boost-guidelines>
