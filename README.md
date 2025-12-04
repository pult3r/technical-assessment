# Technical Assessment — Laravel + Quasar + Docker  
## ⭐ FINAL README (Working 100% After Clean Installation)

This guide describes **exactly** how to install and run the project step‑by‑step.  
Follow it precisely — everything will work on first run.

---

# 📦 1. Requirements

Install on your **host machine**:

- Docker + Docker Compose  
- Node.js ≥ 18  
- npm  
- (optional) Quasar CLI (not required)

---

# 🚀 2. Clone the project

```bash
git clone https://github.com/pult3r/technical-assessment.git
cd technical-assessment
```

---

# 🚀 3. Backend installation (Laravel)

```bash
cd backend
composer install
cp .env.example .env
```

Your preconfigured `.env` should already contain:

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=technical
DB_USERNAME=root
DB_PASSWORD=root

SESSION_DRIVER=database
```

All required migrations (including *sessions*) exist in the project.

---

# 🚀 4. Start Docker environment

From the **root directory**:

```bash
cd ..
docker compose down -v
docker compose up -d --build
```

### Services now running:

| Service     | URL / Info                  |
|-------------|------------------------------|
| Backend API | http://localhost:8080        |
| phpMyAdmin  | http://localhost:8081        |
| MySQL       | Port 3307 → 3306 inside      |

---

# 🚀 5. Run Laravel migrations

Enter the PHP container:

```bash
docker exec -it tech-php bash
```

Inside the container:

```bash
php artisan key:generate
php artisan migrate -v
```

> ✔ All tables including **sessions** will be created.

Now **EXIT the container** — this is important:

```bash
exit
```

---

# 🚀 6. Frontend installation (Quasar) — run on HOST, not in Docker

```bash
cd frontend
npm install
```

Run the dev server on the correct port (Vite default):

```bash
npx quasar dev --port 5173 --hostname 0.0.0.0
```

If you have global Quasar CLI:

```bash
quasar dev --port 5173 --hostname 0.0.0.0
```

Frontend is available at:

👉 **http://localhost:5173/**

---

# 🌐 7. phpMyAdmin access

Visit:

👉 http://localhost:8081

Credentials:

```
Host: mysql
User: root
Pass: root
Database: technical
```

---

# 🧪 8. Common issues

### ❌ `npm: command not found`
You are inside Docker.  
Run frontend commands **only on host**.

### ❌ `cd frontend: No such file`
You are inside `tech-php`.  
Run:

```bash
exit
```

### ❌ `SQLSTATE[HY000] [2002] Connection refused`
You ran artisan on host.  
Run it **inside tech-php** only.

### ❌ Frontend not available on port 9000
Use:

👉 `http://localhost:5173`

---

# 🎉 9. Everything is ready!

You now have a stable environment:

- Laravel backend (Dockerized)  
- Quasar frontend (local dev server)  
- MySQL + phpMyAdmin  
- Fully working migrations (including sessions)  
- Clear separation of commands host vs container  

