# Running SDLabs with Docker

Everything is driven by environment variables — no machine-specific paths, IPs, or
secrets are baked in. You need only Docker (with Compose v2).

The image is self-bootstrapping: on first boot it creates `.env` if missing,
installs dependencies (dev only), generates `APP_KEY`, creates the sqlite
database, links storage, and runs migrations.

---

## 1. Portable run (baked image)

```bash
cp .env.example .env            # required once — holds APP_KEY & config
docker compose up -d --build
```

App: **http://localhost:8080** (change with `APP_PORT`). Seed demo data:

```bash
docker compose exec app php artisan db:seed
```

Data (uploads + sqlite db) persists in named volumes across rebuilds. This mode
bakes code, `vendor/`, and built assets into the image, so it runs the same on
any host.

## 2. Local development (live code + HMR)

```bash
cp .env.example .env
docker compose -f compose.dev.yml up --build
```

- App (Apache): **http://localhost:8080**
- Vite dev server / HMR: **http://localhost:5173**

Source is bind-mounted, so edits are live; the app container installs Composer
deps on first boot and the `vite` container hot-reloads assets.

For remote/LAN dev, set the host the browser uses for HMR:

```bash
VITE_HMR_HOST=192.168.1.5 docker compose -f compose.dev.yml up
```

(If bind-mount file changes aren't detected on your OS, set `VITE_USE_POLLING=true`.)

---

## Optional services (Compose profiles)

Off by default so the core app runs anywhere. Enable per run:

### Garage (S3 object storage)

```bash
cp docker/garage.toml.example docker/garage.toml   # then fill in the secrets it describes
docker compose --profile storage up -d
# initialise the single-node cluster ONCE:
docker compose exec garage /garage status
docker compose exec garage /garage layout assign -z dc1 -c 1G <NODE_ID_FROM_STATUS>
docker compose exec garage /garage layout apply --version 1
```

Then create buckets/keys and point media at Garage in `.env`:
`MEDIA_DISK=garage_uploads` + the `GARAGE_*` vars.

### Flarum forum

```bash
cp docker/forum.env.example docker/forum.env       # edit URL + admin creds
docker compose --profile forum up -d
```

Forum: **http://localhost:8180**. It ships with its own MariaDB (`forum-db`).
Create a 40-char API key in Flarum and mirror it into the app's `.env`
(`FORUM_URL`, `FORUM_API_KEY`) to enable SSO.

Run both profiles together: `docker compose --profile storage --profile forum up -d`.

---

## Handy commands

```bash
docker compose exec app php artisan migrate:fresh --seed   # reset + reseed
docker compose exec app php artisan tinker
docker compose logs -f app
docker compose down                # stop (keeps volumes/data)
docker compose down -v             # stop and wipe volumes (fresh start)
docker compose build --no-cache app
```

## Notes

- **Database:** sqlite by default (a named volume). To use MySQL/Postgres, set
  `DB_*` in `.env` and add a db service.
- **APP_KEY** is generated on first boot into your `.env`; keep that file.
- Compose reads this project's `.env` for `${...}` substitution too, so
  `APP_PORT`, `PHP_VERSION`, `VITE_HMR_HOST`, `FORUM_DB_*` etc. can live there.
- If you previously ran dev, delete `public/hot` before relying on baked assets
  (it tells Laravel to load from the Vite dev server). The baked image never
  includes it.
