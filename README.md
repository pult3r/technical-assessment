# Technical Assessment – Laravel + Quasar + Docker

This project is a full-stack application built using:

- Laravel 12 (PHP 8.4)
- Quasar Framework (Vue 3 SPA)
- MySQL 8
- Docker & Docker Compose
- Dompdf – PDF generator
- Simple QrCode – QR code encoder
- MySQL triggers – audit logging

The environment is fully dockerized — no local PHP/MySQL required.

---

# 1. Requirements

Install locally:

- Docker 25+
- Docker Compose v2+
- Node.js 18+
- npm 9+

Works on:

- macOS
- Linux
- Windows (WSL2 recommended)

---

# 2. Project Setup (from zero)

Clone repository:

```bash
git clone https://github.com/pult3r/technical-assessment.git
cd technical-assessment
```

---

# 3. Start Docker Environment

```bash
docker compose up -d --build
```

Running services:

| Service | URL |
|--------|------|
| Backend API (Laravel) | http://localhost:8080 |
| phpMyAdmin | http://localhost:8081 |
| MySQL | local port 3307 → container 3306 |
| Quasar frontend | http://localhost:5173 |

---

# 4. Backend Setup (Laravel)

Enter the PHP container:

```bash
docker exec -it tech-php bash
cd /var/www/html
```

Install composer dependencies (if needed):

```bash
composer install
```

Create storage link:

```bash
php artisan storage:link
```

---

# 5. Configure Environment (.env)

Create `.env`:

```bash
cp .env.example .env
```

Or manually:

```
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_URL=http://localhost:8080
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=technical
DB_USERNAME=root
DB_PASSWORD=root

FILESYSTEM_DISK=public

LOGIN_USERNAME=admin
LOGIN_PASSWORD=password123

APP_JWT_SECRET=supersecret123456
APP_JWT_EXP=3600

QR_TARGET_URL=https://student-cribs.com/
QR_SIZE=300
QR_MARGIN=1
QR_ECC=H

PDF_DIR=pdf
PDF_PAPER=A4
PDF_ORIENT=portrait
```

Generate Laravel key:

```bash
php artisan key:generate
```

---

# 6. Migrations

Inside the container:

```bash
php artisan migrate
```

This creates:

- users
- audit_log
- pdf_logs
- MySQL triggers for user actions and PDF logs

---

# 7. Frontend Setup (Quasar)

Open a new terminal:

```bash
cd frontend
npm install
npm run dev -- --host 0.0.0.0 --port 5173
```

Frontend runs at:

```
http://localhost:5173
```

---

# 8. Authentication

Default login (from .env):

```
username: admin
password: password123
```

You can also register new users.

JWT token is stored automatically by the frontend (Pinia).

---

# 9. PDF Generation

Use the frontend or call API:

```
POST /api/generate-pdf
Authorization: Bearer <JWT>
```

Response contains:

```
pdf_url: http://localhost:8080/storage/pdf/xxxx.pdf
```

PDF includes:

- user text
- embedded QR code
- A4 layout
- saved to storage/app/public/pdf/

---

# 10. Audit Log (Triggers)

Two audit systems:

| Table | Description |
|--------|------------|
| audit_log | Tracks INSERT, UPDATE, DELETE on users |
| pdf_logs | Logs every generated PDF |

Laravel middleware:

- jwt.auth → validates JWT  
- mysql.user → injects @user_id into MySQL session  

Triggers use @user_id to record the user performing actions.

---

# 11. Reset Environment

If something breaks:

```bash
docker compose down
docker compose up -d --build
```

Refresh Laravel:

```bash
docker exec -it tech-php bash
cd /var/www/html

php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan migrate
```

---

# 12. API Testing Examples

Login:

```bash
curl -X POST http://localhost:8080/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"password123"}'
```

Generate PDF:

```bash
curl -X POST http://localhost:8080/api/generate-pdf \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"text":"Hello world"}'
```

---

# 13. Project Structure

```
backend/
  app/
  config/
  routes/
  database/
  storage/

frontend/
  src/

docker/
  php/
  nginx/

docker-compose.yml
```

---

# 14. Summary

This project provides:

- Fully dockerized Laravel + Quasar stack  
- JWT-secured API  
- PDF generation with QR code  
- MySQL-based audit logging with triggers  
- Clean configuration via .env and config files  
- Production-ready architecture  
- Easy installation on any system (Linux/macOS/Windows)  

Ideal for technical assessments, portfolio, real applications.
