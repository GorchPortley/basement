# SDLabs — architecture & handoff

This is the extracted **core** of the SDLabs speaker-design library, rebuilt from
scratch on a modern Laravel + Livewire stack with a deliberately minimal
("wireframe") UI that's easy to theme. It's meant as an organized starting point
for further customization, not a finished product.

The old app (`sdlabs_old`, Wave + Filament + Chatify) was mined for its essential
functions; everything not core to the library was dropped or replaced with a
simpler equivalent.

---

## Stack

| Concern        | Choice                                             |
|----------------|----------------------------------------------------|
| Framework      | Laravel 13, PHP 8.3                                |
| Interactivity  | Livewire 4 (class-based components)                |
| UI kit         | **maryUI** components on **daisyUI** (Tailwind v4) |
| Theming        | daisyUI themes: `wireframe` (default) / `business` (dark) |
| Auth           | Laravel Fortify (login, register, 2FA, verification) |
| Media/uploads  | Spatie Media Library (named collections)          |
| Object storage | Garage (S3) disks defined in `config/filesystems.php` |
| Charts         | Chart.js (frequency viewer + admin analytics)     |
| Forum          | Flarum SSO (kept from existing progress)          |
| Database       | sqlite by default                                  |

`livewire/flux` is installed (starter-kit leftover) but unused — the app is built
entirely on maryUI/daisyUI. It can be removed once you're sure nothing depends on it.

---

## Domain model

Two owned entities, joined by two pivots. Ownership is **polymorphic** (`owner_*`)
so a design/component can belong to a `User` today or an organisation later.

```
User ──(morphMany owner)──▶ Design ◀──(belongsToMany)──▶ Component ◀──(morphMany owner)── User
                              │  via component_design (position, qty, crossover, air_volume, payload + media)
                              │
                              └──(morphedByMany collaborator)──▶ User   via collaborator_design (payload)
```

- **Design** (`app/Models/Design.php`) — a published speaker project.
- **Component** (`app/Models/Component.php`) — a driver/part designs reference
  (the old `Driver` model, generalised). Referenced via an affiliate `link`,
  never sold on-site.
- **ComponentDesign** — the "recipe" pivot: how a component is deployed in a
  design (crossover position/corners, quantity, enclosure volume). Carries its
  own media so a designer can attach *measured* in-situ FRD/ZMA data.
- **CollaboratorDesign** — morph pivot crediting additional users on a design.

### Hybrid storage (columns + payload)

Fields you browse/filter/sort by are **real, indexed columns** (name, category,
price, `official`, `active`…). Flexible or rarely-queried data lives in a JSON
**`payload`** column (bill of materials, Thiele-Small parameters, free-form specs).
This keeps the marketplace queryable while staying schema-light — see
`database/migrations/..._create_core_tables.php`.

Enum-like fields are stored as **strings** and cast to PHP enums
(`app/Enums/*`) — sqlite has no real `ENUM` type and the original app had to
migrate away from DB enums, so we avoid that from the start.

### Files → media collections

The old JSON file-array columns (`frd_files`, `enclosure_files`, …) are replaced
by Spatie Media Library collections:

- Design: `card`, `frd`, `enclosure`, `electronic`, `other`
- Component: `card`, `frequency` (FRD), `impedance` (ZMA), `other`
- ComponentDesign: `frequency`, `impedance`, `other` (measured per placement)
- User: `avatar`

---

## Frequency-response viewer

`app/Livewire/FrequencyResponseViewer.php` + `resources/js/app.js` (`frdViewer`).

Ported verbatim from the old app (the math is good): it parses FRD text
(`freq  dB  phase`), draws each driver's amplitude/phase curve on a logarithmic
axis, and computes the **complex sum** of all drivers (magnitude+phase combined
per frequency) with low-frequency smoothing — so you can see how the finished
speaker measures. Data now comes from media collections instead of JSON columns.

Embed it with either target:

```blade
<livewire:frequency-response-viewer :design="$design" />
<livewire:frequency-response-viewer :component="$component" />
```

---

## Routes

| Path | What |
|------|------|
| `/` | Landing page |
| `/designs`, `/designs/{slug}` | Public design index / detail |
| `/components`, `/components/{slug}` | Public component index / detail |
| `/dashboard` | Author "studio" — your designs & components (auth) |
| `/studio/designs/{create,edit}` | Design create/edit form (auth) |
| `/studio/components/{create,edit}` | Component create/edit form (auth) |
| `/admin`, `/admin/designs`, `/admin/components` | Lean admin (auth + `admin` middleware) |

Public index interactivity (search/filter/sort/paginate) lives in
`app/Livewire/{Designs,Components}/Index.php`.

---

## Admin (replaces Filament)

A lean Livewire admin under `app/Livewire/Admin/`, gated by the `admin`
middleware (`app/Http/Middleware/EnsureUserIsAdmin.php`) and a per-request
`boot()` guard on each component:

- **Overview** — stat tiles + Chart.js analytics (30-day activity, category mix).
  This is where **brand-account management and licensing analytics** attach as
  those features come online.
- **Designs / Components** — moderation tables: toggle `official` (verified) and
  `active` (published), delete.

Admin is granted via the `is_admin` column on `users` (set in the seeder; not
mass-assignable). `official` means **manufacturer-verified**, never paid
placement — keep it that way (community guardrail from the product notes).

---

## What's intentionally deferred

These were core to the *concept* but are out of scope for this clean starting
point. The data model already leaves room for them:

- **Payments / checkout** — `access` (free/tip/gated) + `price` capture the
  designer's intent; gated files show a "coming soon" notice. Wire Stripe
  Connect later.
- **Design snapshots / versioning** (zip + PDF spec sheet) — the old
  `createDesignSnapshot()` (DomPDF + ZipArchive). Add a `design_snapshots` table
  + model when you build purchase-unlock.
- **Reviews/ratings** — dropped for now; the old app used a review-rating package.
- **Full-text search** — currently `LIKE` scopes; add Laravel Scout if needed.

Kept from existing progress and **not** rebuilt: Flarum forum SSO
(`ForumAuthenticationController` + Fortify listeners), auth/settings scaffolding.

> Note: the starter kit's `routes/settings.php` points at `pages::settings.*`
> Volt pages that don't exist yet — `/settings` will error until you add them.
> Nothing links there, so it doesn't affect the library.

---

## Old → new mapping

| Old (`sdlabs_old`) | New (`sdlabs_new`) |
|--------------------|--------------------|
| `Driver` | `Component` |
| `DesignDriver` pivot | `ComponentDesign` pivot |
| `user_id` ownership | polymorphic `owner_*` |
| `frd_files`/`*_files` JSON columns | Spatie media collections |
| DB `enum` columns | string columns + PHP enums (`app/Enums`) |
| Filament admin | lean Livewire admin + Chart.js |
| Chatify messaging | dropped (use the forum) |
| Wave SaaS (billing/plans/blog/pages) | dropped |
| Scout search | `LIKE` query scopes |

---

## Run it

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan storage:link        # so uploaded images resolve
npm install
npm run dev
```

Demo logins (password `password`):

- **admin@sdlabs.test** — admin (see `/admin`)
- **designer@sdlabs.test** — owns the seeded designs/components

Open `/designs` → "Overnight Sensations" to see the summed two-way response.
