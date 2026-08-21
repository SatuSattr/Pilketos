# Pilketos

A web-based school student council election system (Pemilihan Ketua OSIS). It has two surfaces:

- **Voting page** (`/`) — touch-friendly candidate cards for students, protected by a per-booth display key
- **Admin panel** (`/admin`) — single-user dashboard for managing candidates, voters, booth keys, and monitoring live results

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13, PHP 8.4 |
| Database | SQLite |
| CSS | Tailwind CSS v4 |
| JS | Alpine.js, Vite 8 |
| Charts | Chart.js + chartjs-plugin-zoom |
| Icons | Lucide (tree-shaken, no CDN) |
| Alerts | SweetAlert2 (no CDN) |
| Fonts | Montserrat via Bunny Fonts (self-hosted at build) |

## Deploy with Docker

### Requirements

- Docker + Docker Compose installed on the VPS
- A subdomain DNS A record pointing to your VPS IP

### First deploy

> **Important:** The `.env` file must exist before running `docker compose up` — the container reads it at startup. Create it first, then build.

```bash
# 1. Clone the repo
git clone <your-repo> pilketos
cd pilketos

# 2. Create the SQLite database file (required before Docker mounts it)
touch database/database.sqlite

# 3. Create your .env from the production template
cp .env.production .env

# 4. Edit .env — fill in at minimum:
#    APP_KEY, APP_URL, APP_NAME
nano .env

# 5. Generate APP_KEY (paste the output into .env as APP_KEY=...)
docker run --rm php:8.4-fpm-alpine php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"

# 6. Now build and start
docker compose up -d --build
```

Minimum required values in `.env`:

```env
APP_NAME=Pilketos
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-subdomain.yourdomain.com
APP_KEY=base64:...        # paste the generated key here
DB_DATABASE=/var/www/html/database/database.sqlite
```

The app will be available at `http://VPS-IP:8085`. Point your reverse proxy (nginx/caddy on the host) to `localhost:8085` for the subdomain.

On first start the entrypoint automatically runs migrations and seeds the admin user.

Default admin credentials: `admin@pilketos.local` / `admin123` — **change the password immediately after first login.**

### Update workflow

```bash
git pull
docker compose up -d --build
```

Migrations run automatically on every container start.

### Useful commands

```bash
# View logs
docker compose logs -f

# Run artisan commands
docker compose exec pilketos php artisan <command>

# Open a shell inside the container
docker compose exec pilketos sh
```

## Local development

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install
composer run dev   # starts php, queue worker, and vite concurrently
```
