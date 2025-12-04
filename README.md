# Technical Assessment — Laravel + Quasar + Docker

## 📘 Overview

This project contains a complete development environment based on Docker, running:

- Laravel 11 (backend)
- Quasar (frontend)
- Nginx
- PHP-FPM (PHP 8.4)
- MySQL 8
- phpMyAdmin

The backend includes **all required migrations**, including the `sessions` table.

---

## 📦 Project Structure

```
technical-assessment/
 ├── backend/       → Laravel API
 │    └── database/migrations/
 │         ├── 0001_01_01_000000_create_users_table.php
 │         ├── 2024_01_02_000000_create_audit_log_table.php
 │         ├── 2024_01_03_000001_create_users_triggers.php
 │         ├── 2024_01_04_000000_create_pdf_logs_table.php
 │         ├── 2024_01_04_000001_create_pdf_logs_trigger.php
 │         └── 2024_01_05_000000_create_sessions_table.php   ← NEW (included in repo)
 ├── frontend/      → Quasar SPA
 ├── docker/        → Docker config files
 └── docker-compose.yml
```

---

# 🚀 1. Clone and Setup

```bash
git clone https://github.com/pult3r/technical-assessment.git
cd technical-assessment
```

---

# 🚀 2. Backend Setup (Laravel)

```bash
cd backend
composer install
cp .env.example .env
```

### `.env` contains correct DB configuration:

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=technical
DB_USERNAME=root
DB_PASSWORD=root

SESSION_DRIVER=database
```

---

# 🚀 3. Docker Setup

From project root:

```bash
cd ..
docker compose down -v
docker compose up -d --build
```

### Containers:

| Service      | URL / Name                  |
|--------------|------------------------------|
| php          | tech-php                     |
| nginx        | http://localhost:8080        |
| mysql        | tech-mysql (3307 → 3306)     |
| phpMyAdmin   | http://localhost:8081        |

---

# 🚀 4. Laravel Commands

Enter PHP container:

```bash
docker exec -it tech-php bash
```

### Generate app key:

```bash
php artisan key:generate
```

### Run migrations (sessions table included automatically):

```bash
php artisan migrate -v
```

After executing this, all tables including:

- users  
- audit_log  
- pdf_logs  
- sessions  

will be created.

No manual migration creation is required.

---

# 🚀 5. phpMyAdmin

```
http://localhost:8081
```

Login:

```
Host: mysql
User: root
Pass: root
DB: technical
```

---

# 🚀 6. Frontend (Quasar)

```bash
cd frontend
npm install
quasar dev
```

---

# 🧪 7. Common Issues

### ❌ `SQLSTATE[HY000] [2002] Connection refused`

This occurs only when artisan is run outside Docker.

Run it inside:

```bash
docker exec -it tech-php bash
```

### ❌ `Table 'technical.sessions' doesn't exist`

Not applicable anymore —  
the migration **is included in the repo** and runs automatically.

---

# 🛠 8. Recommended Adjustments

### Add MySQL user in docker-compose:

```
environment:
  MYSQL_ROOT_PASSWORD: root
  MYSQL_DATABASE: technical
  MYSQL_USER: app
  MYSQL_PASSWORD: secret
```

Then set in `.env`:

```
DB_USERNAME=app
DB_PASSWORD=secret
```

---

# 🎉 Finished!

Your environment should now run flawlessly with **NO manual session migration creation required**.
