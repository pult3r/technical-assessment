# Technical Assessment — Laravel + Quasar + Docker

## 📘 Overview

This project contains a complete development environment based on Docker, running:

- Laravel 11 (backend)
- Quasar (frontend)
- Nginx
- PHP-FPM (PHP 8.4)
- MySQL 8
- phpMyAdmin

---

## 📦 Project Structure

```
technical-assessment/
 ├── backend/       → Laravel API
 ├── frontend/      → Quasar SPA
 ├── docker/        → Docker config files
 └── docker-compose.yml
```

---

## 🚀 1. Clone and Setup

```bash
git clone https://github.com/pult3r/technical-assessment.git
cd technical-assessment
```

---

## 🚀 2. Backend Setup (Laravel)

### Install dependencies:

```bash
cd backend
composer install
cp .env.example .env
```

### Set correct DB configuration in `.env`:

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

## 🚀 3. Docker Setup

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

## 🚀 4. Laravel Commands

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
php artisan migrate
```

If sessions table is missing:

```bash
php artisan make:migration create_sessions_table
php artisan migrate
```

---

## 🚀 5. phpMyAdmin

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

## 🚀 6. Frontend (Quasar)

```bash
cd frontend
npm install
quasar dev
```

---

## 🧪 7. Common Issues

### ❌ `SQLSTATE[HY000] [2002] Connection refused`

You ran artisan **outside** Docker.  
Use:

```bash
docker exec -it tech-php bash
```

### ❌ `Table 'technical.sessions' doesn't exist`

Create migration manually:

```bash
php artisan make:migration create_sessions_table
php artisan migrate
```

---

## 🛠 8. Recommended Adjustments

### Add MySQL user in docker-compose:

```
environment:
  MYSQL_ROOT_PASSWORD: root
  MYSQL_DATABASE: technical
  MYSQL_USER: app
  MYSQL_PASSWORD: secret
```

And update `.env`:

```
DB_USERNAME=app
DB_PASSWORD=secret
```

### Add sessions migration to the repo for all developers.

---

## 🎉 Finished!

Your environment should now run flawlessly.

