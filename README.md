# Technical Assessment — Laravel + Quasar + Docker

This guide describes the installation and setup process for the project, including:

- Laravel backend
- Quasar frontend
- MySQL database
- Nginx
- phpMyAdmin
- Docker Compose

Follow each step to configure the environment properly.

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

The `.env` file is already configured for Docker:

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

From the project root:

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

Inside the container:

```bash
php artisan key:generate
php artisan migrate -v
```

Then exit:

```bash
exit
```

---

## 6. Frontend setup (Quasar)

All frontend commands must be run on the **host**, not inside Docker.

```bash
cd frontend
npm install
```

Start the development server:

```bash
npx quasar dev --port 5173 --hostname 0.0.0.0
```

Frontend is available at:

```
http://localhost:5173
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

### Database connection errors
Ensure the backend commands are executed inside the PHP container:

```bash
docker exec -it tech-php bash
```

---

## 9. Project structure

```
technical-assessment/
 ├── backend/         Laravel API
 ├── frontend/        Quasar frontend
 ├── docker/          Nginx configuration
 └── docker-compose.yml
```

---

## 10. Development URLs

| Component | URL |
|----------|------|
| Backend API | http://localhost:8080 |
| Frontend | http://localhost:5173 |
| phpMyAdmin | http://localhost:8081 |

