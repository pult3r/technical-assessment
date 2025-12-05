# Technical Assessment — Laravel + Quasar + Docker

This guide describes the installation and setup process for the project, including:

- Laravel backend  
- Quasar frontend  
- MySQL database  
- Nginx  
- phpMyAdmin  
- Docker Compose  

---

## 1. Requirements

Install on your host machine:

- Docker + Docker Compose  
- Node.js ≥ 18  
- npm  

---

## 2. Clone the repository

```bash
git clone https://github.com/pult3r/technical-assessment.git
cd technical-assessment
```

---

## 3. Backend setup (Laravel)

```bash
cd backend
composer install
cp .env.example .env
```

The `.env` file included is already configured for Docker (adjust if needed):

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=technical
DB_USERNAME=root
DB_PASSWORD=root

SESSION_DRIVER=database
```

If you need to change JWT secret or other values, edit `backend/.env` before starting containers.

---

## 4. Start Docker environment

From the project root:

```bash
docker compose down -v
docker compose up -d --build
```

Services:

| Service     | URL                     |
|-------------|--------------------------|
| Backend     | http://localhost:8080    |
| phpMyAdmin  | http://localhost:8081    |
| MySQL       | Host: mysql, Port: 3306  |

---

## 5. Run Laravel migrations

Enter the PHP container:

```bash
docker exec -it tech-php bash
```

Inside the container run:

```bash
php artisan key:generate
php artisan migrate -v
```

### 5.1 Create storage symlink (required for PDF access)

Laravel stores generated PDFs inside `storage/app/public`.  
To make them accessible via URL, create the public symlink:

```bash
php artisan storage:link
```

You should see:

```
The [public/storage] directory has been linked.
```

If you need to set filesystem permissions (usually inside container):

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

Then exit the container:

```bash
exit
```

---

## 6. Frontend setup (Quasar)

All frontend commands must be run on the **host**, not inside Docker.

From project root:

```bash
cd frontend
npm install
```

Start the development server (host machine):

```bash
npx quasar dev --port 5173 --hostname 0.0.0.0
```

Frontend is available at:

```
http://localhost:5173
```

If you prefer to run Quasar in background (non-blocking):

```bash
(npx quasar dev --port 5173 --hostname 0.0.0.0 &)
```

---

## 7. phpMyAdmin

Access via browser:

```
http://localhost:8081
```

Database login:

```
Host: mysql
User: root
Password: root
Database: technical
```

---

## 8. Troubleshooting

### npm or quasar not found
Ensure you are running commands on the **host machine**, not inside a Docker container.

### cd frontend shows "No such file"
Exit the PHP container:

```bash
exit
```

### Database connection errors (migrations)
If migrations fail with "Connection refused", MySQL might not be ready. Retry from host:

```bash
docker compose up -d --build
# wait a few seconds or check logs
docker compose logs -f mysql
```

Or run migrations inside PHP container after confirming MySQL up:

```bash
docker exec -it tech-php bash
php artisan migrate -v
```

### Storage / PDF returns 403 Forbidden
If generated PDF returns 403 when opening returned `storage/...` URL, likely causes:

- `public/storage` symlink missing → run `php artisan storage:link`
- filesystem permissions prevent nginx from reading files → run inside PHP container:
  ```bash
  chmod -R 775 storage bootstrap/cache
  chown -R www-data:www-data storage bootstrap/cache
  ```

### Authorization / Token issues
- If frontend redirects automatically to `/generator` after start, check `localStorage.token`. Remove it to force login:
  ```js
  localStorage.removeItem('token')
  ```
- Implement token validation on startup or in router guards as needed.

---

## 9. Helpful commands (summary)

From project root:

```bash
# rebuild and start
docker compose down -v
docker compose up -d --build

# enter PHP container
docker exec -it tech-php bash

# inside php container
php artisan key:generate
php artisan migrate -v
php artisan storage:link

# frontend (on host)
cd frontend
npm install
npx quasar dev --port 5173 --hostname 0.0.0.0
```

---

## 10. Project structure

```
technical-assessment/
 ├── backend/         Laravel API
 ├── frontend/        Quasar frontend
 ├── docker/          Nginx configuration
 └── docker-compose.yml
```

---

## 11. Development URLs

| Component | URL |
|-----------|-----|
| Backend API | http://localhost:8080 |
| Frontend | http://localhost:5173 |
| phpMyAdmin | http://localhost:8081 |

---

