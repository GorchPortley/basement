# SDLabs

An open, egalitarian library/marketplace for DIY loudspeaker designs and the
drivers behind them — with in-browser frequency-response analysis. Reworked from
scratch as a clean, themeable starting point.

## Quickstart

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan storage:link
npm install && npm run dev
```

Demo logins (password `password`): `admin@sdlabs.test` (admin),
`designer@sdlabs.test`.

## What's here

- **Designs** & **Components** — polymorphically-owned, hybrid columns + JSON
  `payload`, files via Spatie Media Library.
- **Frequency-response viewer** — parses FRD files and shows the complex-summed
  multi-driver response (Chart.js).
- **Author studio** — publish/edit designs & components.
- **Lean admin** — Livewire + Chart.js analytics and moderation (replaces the
  old Filament panel).
- **maryUI + daisyUI** wireframe UI, theme-switchable (`wireframe` / `business`).

See [`docs+planning/ARCHITECTURE.md`](docs+planning/ARCHITECTURE.md) for the full
design, the old→new mapping, and what's intentionally deferred (payments,
snapshots, reviews).
