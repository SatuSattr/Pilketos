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

```bash
git clone <your-repo> pilketos
cd pilketos

# SQLite file must exist before mounting as a volume
touch database/database.sqlite

# Create your .env from the production template
cp .env.production .env
nano .env
```

Minimum required values in `.env`:

```env
APP_NAME=Pilketos
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-subdomain.yourdomain.com
APP_KEY=             # generate with the command below
DB_DATABASE=/var/www/html/database/database.sqlite
```

Generate `APP_KEY`:

```bash
docker run --rm php:8.4-fpm-alpine php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
```

Start the container:

```bash
docker compose up -d --build
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
