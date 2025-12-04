# Technical Assessment — Laravel + Quasar + Docker (Updated)

## 📘 Overview

Complete development environment using:

- Laravel 11 (backend)
- Quasar / Vue 3 (frontend)
- MySQL 8
- Nginx
- PHP 8.4
- phpMyAdmin
- Docker Compose

This README explains **step-by-step**, from cloning the repo to running backend & frontend successfully.
*(Updated: clarifies Quasar dev server port and how to run frontend locally or in Docker.)*

---

# 🚀 1. Requirements

Install:

- Docker + Docker Compose  
- Node.js ≥ 18  
- npm  
- (Later) Quasar CLI  

---

# 🚀 2. Clone Project

```bash
git clone https://github.com/pult3r/technical-assessment.git
cd technical-assessment
```

---

# 🚀 3. Backend Setup (Laravel)

```bash
cd backend
composer install
cp .env.example .env
```

### ✔️ `.env` MUST contain:

```
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=technical
DB_USERNAME=root
DB_PASSWORD=root

SESSION_DRIVER=database
SESSION_LIFETIME=120
```

Laravel uses MySQL inside Docker.  
Session storage uses the database.

---

# 🚀 4. Start Docker Environment (backend + db + nginx)

From project root:

```bash
cd ..
docker compose down -v
docker compose up -d --build
```

### Containers:

| Service      | Role               | URL / Notes                  |
|--------------|--------------------|-------------------------------|
| tech-php     | PHP-FPM + Laravel  | Internal                      |
| tech-nginx   | Web server         | http://localhost:8080         |
| tech-mysql   | MySQL 8            | Port 3307 → 3306              |
| tech-pma     | phpMyAdmin         | http://localhost:8081         |

---

# 🚀 5. Run Laravel Migrations

Enter PHP container:

```bash
docker exec -it tech-php bash
```

### Generate app key:

```bash
php artisan key:generate
```

### Run migrations:

```bash
php artisan migrate -v
```

This automatically creates tables:

- users  
- audit_log  
- pdf_logs  
- sessions (included in repo!)  
- triggers  

✔️ No need to generate sessions migration manually.

---

# 📄 6. Sessions Migration Included in Repo

File:

```
backend/database/migrations/2024_01_05_000000_create_sessions_table.php
```

---

# 🚀 7. phpMyAdmin Access

```
http://localhost:8081
```

Credentials:

```
Host: mysql
User: root
Pass: root
Database: technical
```

---

# 🚀 8. Frontend Setup (Quasar) — IMPORTANT: ports & where to run

**Key point:** The frontend must be run on your *host machine* (or in a dedicated Node container).  
**Do not** run `npm` / `quasar` commands inside the `tech-php` container.

Quasar dev server by default (when started inside the Docker example below) is started on **port 9000**. If you run it locally without specifying a port, it may choose a different port (often 8080). To avoid confusion, the README below uses **9000** for Docker-based frontend and **5174/8080/9000** options for local runs — you can set the port explicitly.

---

## Option A — Run frontend locally (recommended for development)

On your host machine (not inside any Docker container):

```bash
cd frontend
npm install
```

If you have Quasar CLI installed globally:

```bash
quasar dev --port 9000 --hostname 0.0.0.0
# or change port to 8080 if you prefer:
# quasar dev --port 8080 --hostname 0.0.0.0
```

If you don't have Quasar CLI globally:

```bash
npx quasar dev --port 9000 --hostname 0.0.0.0
```

**Frontend dev server will be available at:** `http://localhost:9000` (or `http://localhost:8080` if you chose 8080).

---

## Option B — Run frontend in a temporary Node Docker container (no local Node install)

From project root (host):

```bash
docker run --rm -it   -v "$PWD/frontend":/app   -w /app   -p 9000:9000   node:18-bullseye   bash -lc "npm install && npx quasar dev --host 0.0.0.0 --port 9000"
```

Frontend will be exposed at `http://localhost:9000`.

---

## Option C — Add frontend service to docker-compose (recommended for containerized dev)

Add this service to your `docker-compose.yml` under `services:`:

```yaml
  frontend:
    image: node:18-bullseye
    container_name: tech-frontend
    working_dir: /app
    volumes:
      - ./frontend:/app
      - /app/node_modules
    ports:
      - "9000:9000"
    command: bash -c "npm install && npx quasar dev --host 0.0.0.0 --port 9000"
    networks:
      - appnet
```

Then start it:

```bash
docker compose up -d frontend
```

Frontend will be available at `http://localhost:9000`.

---

# 🧪 9. Troubleshooting — Quasar port gotcha

- If you run `quasar dev` and the server reports a port (e.g. `9000`), make sure you're opening that port in the browser.  
- If port `9000` is already used, pick another port explicitly: `quasar dev --port 8080` or `npx quasar dev --port 5174`.  
- If running inside Docker, ensure `ports:` in `docker-compose.yml` maps the container port to the host (e.g. `"9000:9000"`).

---

# 🧪 10. Other Common Issues

### ❌ `zsh: command not found: quasar`
Install CLI:

```
npm install -g @quasar/cli
```

Or use `npx quasar ...`.

---

### ❌ `SQLSTATE[HY000] [2002] Connection refused`
You executed `php artisan` **on host**, not in Docker. Run artisan inside `tech-php`.

---

### ❌ `Table 'technical.sessions' doesn't exist`
Not applicable — migration included.

---

# 🎉 11. Done

You now have clear instructions about Quasar dev server port and where to run frontend commands.

---

If you want, I'll:

- update the actual `docker-compose.yml` with the `frontend` service and create a PR,  
- add a Makefile with `make frontend` shortcut,  
- or change Quasar dev port to 8080 in the template.

Tell me which you'd like me to do next.
