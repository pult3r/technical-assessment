# Technical Assessment — Laravel + Quasar + Docker

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

# 🚀 4. Start Docker Environment

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

Contents:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
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

# 🚀 8. Frontend Setup (Quasar)

```bash
cd frontend
npm install
```

## Install Quasar CLI globally:

```
npm install -g @quasar/cli
```

Verify:

```
quasar --version
```

Run dev server:

```
quasar dev
```

### If Quasar CLI is not available globally:

Use local runner:

```
npx quasar dev
```

---

# 🧪 9. Troubleshooting

### ❌ `zsh: command not found: quasar`
Install CLI:

```
npm install -g @quasar/cli
```

---

### ❌ `SQLSTATE[HY000] [2002] Connection refused`
You executed `php artisan` **on host**, not in Docker.

Run in container:

```
docker exec -it tech-php bash
```

---

### ❌ `Table 'technical.sessions' doesn't exist`
Not applicable anymore — the migration is included in repo.

---

# 🎉 10. Done

You now have:

- Fully working Laravel backend  
- Fully working Quasar SPA  
- Dockerized Nginx + PHP-FPM  
- MySQL with all migrations  
- phpMyAdmin  
- Clean reproducible install steps  

---

Need additional improvements?

I can add:

- Makefile automation  
- Production docker-compose  
- CI/CD config  
- Frontend–Backend auth with Laravel Sanctum  

Just ask!  
