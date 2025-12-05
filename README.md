# Technical Assessment — Laravel + Quasar + Docker

This document describes how to install and run the project environment, including:

- Laravel backend  
- Quasar frontend  
- MySQL  
- Nginx  
- phpMyAdmin  
- Docker Compose  

---

## 1. Requirements

Install on your host machine:

- Docker & Docker Compose  
- Node.js 18+  
- npm  

---

## 2. Clone the repository

```bash
git clone https://github.com/pult3r/technical-assessment.git
cd technical-assessment
```

Project structure:

```
technical-assessment/
 ├── backend/      
 ├── frontend/     
 ├── docker/       
 └── docker-compose.yml
```

---

## 3. Backend setup (Laravel)

```bash
cd backend
composer install
cp .env.example .env
```

Default `.env` is configured for Docker:

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

## 4. Start Docker environment

Run from the project root:

```bash
cd ..
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

Run on host machine:

```bash
cd frontend
npm install
```

Start dev server:

```bash
npx quasar dev --port 5173 --hostname 0.0.0.0
```

Frontend URL:

```
http://localhost:5173
```

---

## 7. phpMyAdmin

URL:

```
http://localhost:8081
```

Credentials:

```
Server: mysql
User: root
Password: root
Database: technical
```

---

## 8. Troubleshooting

### npm or quasar not found
Make sure commands are executed on the host machine, not inside Docker.

### "cd frontend" shows "No such file"
You are inside the PHP container. Exit it:

```bash
exit
```

### Database connection errors ("Connection refused")
MySQL may not be ready.

```bash
docker compose up -d --build
docker compose logs -f mysql
```

Then retry migrations:

```bash
docker exec -it tech-php bash
php artisan migrate -v
```

### PDF or storage URL returns 403
Possible causes:

- Missing symlink → run:

  ```bash
  php artisan storage:link
  ```

- File permissions → run inside container:

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

### Docker

```bash
docker compose down -v
docker compose up -d --build
docker exec -it tech-php bash
```

### Laravel (inside container)

```bash
php artisan key:generate
php artisan migrate -v
php artisan storage:link
```

### Frontend (host machine)

```bash
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

