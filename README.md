# Technical Assessment — Laravel + Quasar + Docker (FINAL v3)

## 📘 Overview

This documentation provides a clear, step-by-step installation guide  
for running:

- Laravel 11 (backend)
- Quasar / Vue 3 (frontend)
- MySQL 8
- PHP-FPM 8.4
- Nginx
- phpMyAdmin
- Docker Compose

ALL required migrations (including `sessions`) are included in the repo.

---

# 🚀 1. Requirements

Install on your host machine:

- **Docker** + **Docker Compose**
- **Node.js ≥ 18**
- **npm**
- (optional) **Quasar CLI** — can also use `npx`

---

# 🚀 2. Clone the project

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

### ✔️ Configure `.env` (already correct in repo)

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

# 🚀 4. Start Docker (backend + DB + nginx)

From the project root:

```bash
cd ..
docker compose down -v
docker compose up -d --build
```

### Running services:

| Container     | Role              | URL                     |
|---------------|-------------------|--------------------------|
| tech-php      | Laravel backend   | internal only            |
| tech-nginx    | Web server        | http://localhost:8080    |
| tech-mysql    | MySQL 8           | 3307 → 3306              |
| tech-pma      | phpMyAdmin        | http://localhost:8081    |

---

# 🚀 5. Run Laravel migrations

Enter PHP container:

```bash
docker exec -it tech-php bash
```

Inside container:

```bash
php artisan key:generate
php artisan migrate -v
```

ALL tables including **sessions** will be created.

### IMPORTANT — leave the container now:

```bash
exit
```

If you don't exit, you will NOT be able to run frontend commands!

---

# 🚀 6. Frontend Setup (Quasar)

On your **host machine** (NOT inside container):

```bash
cd frontend
npm install
```

### Start dev server (explicit port 9000 to avoid conflicts):

Using local Quasar CLI:

```bash
quasar dev --port 9000 --hostname 0.0.0.0
```

If you don't have Quasar CLI installed globally:

```bash
npx quasar dev --port 9000 --hostname 0.0.0.0
```

👉 Frontend will be available at:

```
http://localhost:9000
```

---

# 🚀 7. phpMyAdmin

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

# 📄 8. Sessions migration included in repo

`backend/database/migrations/2024_01_05_000000_create_sessions_table.php`  
(Already applied by `php artisan migrate`)

---

# 🧪 9. Troubleshooting

### ❌ Error: `cd frontend` inside container
You're still inside tech-php.  
Solution:

```bash
exit
cd frontend
```

### ❌ Error: `npm: command not found`
You are still inside Docker.  
Node is only installed on host.

### ❌ Error: SQLSTATE[HY000] [2002] Connection refused
You ran Artisan **outside Docker**.  
Always run inside:

```bash
docker exec -it tech-php bash
```

### ❌ Sessions table missing
Not applicable — migration is included.

---

# 🎉 10. Everything is ready

You now have:

✔ Fully working backend (Laravel)  
✔ Fully working frontend (Quasar)  
✔ Dockerized services  
✔ No manual migrations needed  
✔ Clean and consistent installation workflow  

If you'd like:

- a `frontend` service added to docker-compose,  
- Makefile automation (`make up`, `make migrate`, etc.),  
- production docker-compose setup,  
- CI/CD pipeline,  

just tell me!
