#!/bin/bash

set -e

echo "=============================================="
echo " CLEANING EXISTING DOCKER RESOURCES"
echo "=============================================="

# Stop and remove containers related to this project
echo "👉 Stopping containers matching: tech, technical-assessment"
docker ps -a --format "{{.ID}} {{.Names}}" | grep -E "tech|technical" | awk '{print $1}' | xargs -r docker stop
docker ps -a --format "{{.ID}} {{.Names}}" | grep -E "tech|technical" | awk '{print $1}' | xargs -r docker rm -f

# Remove Docker images related to this project
echo "👉 Removing images matching: tech, technical-assessment"
docker images --format "{{.ID}} {{.Repository}}" | grep -E "tech|technical" | awk '{print $1}' | xargs -r docker rmi -f

# Remove networks
echo "👉 Removing Docker networks matching: tech, technical-assessment"
docker network ls --format "{{.ID}} {{.Name}}" | grep -E "tech|technical" | awk '{print $1}' | xargs -r docker network rm

# Remove volumes
echo "👉 Removing Docker volumes matching: tech, technical-assessment"
docker volume ls --format "{{.Name}}" | grep -E "tech|technical" | xargs -r docker volume rm

echo "✔ Docker cleanup completed."
echo ""
echo "=============================================="
echo " TECHNICAL ASSESSMENT – FULL PROJECT SETUP"
echo "=============================================="

# -----------------------------------------------
# 1. Backend setup (Laravel)
# -----------------------------------------------

echo "----------------------------------------------"
echo " INSTALLING BACKEND"
echo "----------------------------------------------"

if [ ! -d "backend" ]; then
    echo "❌ ERROR: Folder 'backend' not found!"
    echo "Musisz uruchomić ten skrypt z katalogu technical-assessment/"
    exit 1
fi

cd backend

if [ ! -d "vendor" ]; then
    echo "👉 Running composer install..."
    composer install
else
    echo "✔ Composer dependencies already installed."
fi

if [ ! -f ".env" ]; then
    echo "👉 Copying .env.example → .env..."
    cp .env.example .env
else
    echo "✔ .env already exists."
fi

cd ..

# -----------------------------------------------
# 2. Start Docker environment
# -----------------------------------------------

echo "----------------------------------------------"
echo " STARTING DOCKER ENVIRONMENT"
echo "----------------------------------------------"

docker compose down -v || true
docker compose up -d --build

echo "🚀 Docker started."

# -----------------------------------------------
# 3. WAIT FOR MYSQL
# -----------------------------------------------

PHP_CONTAINER="tech-php"

echo "----------------------------------------------"
echo " WAITING FOR MYSQL TO BE READY..."
echo "----------------------------------------------"

until docker exec $PHP_CONTAINER php -r "
try {
    new PDO('mysql:host=mysql;port=3306;dbname=technical','root','root');
    echo 'MySQL OK';
} catch (Exception \$e) {
    exit(1);
}
"; do
    echo "⏳ MySQL not ready yet... retrying in 2 seconds"
    sleep 2
done

echo "✔ MySQL is ready!"

# -----------------------------------------------
# 4. Laravel migrate
# -----------------------------------------------

echo "----------------------------------------------"
echo " RUNNING LARAVEL MIGRATIONS"
echo "----------------------------------------------"

docker exec -it $PHP_CONTAINER php artisan key:generate
docker exec -it $PHP_CONTAINER php artisan migrate -v

# -----------------------------------------------
# 5. Frontend setup
# -----------------------------------------------

echo "----------------------------------------------"
echo " INSTALLING FRONTEND"
echo "----------------------------------------------"

cd frontend

if [ ! -d "node_modules" ]; then
    echo "👉 Running npm install..."
    npm install
else
    echo "✔ Frontend dependencies already installed."
fi

echo "----------------------------------------------"
echo " STARTING FRONTEND (Quasar Dev Server)"
echo "----------------------------------------------"

if lsof -i :5173 >/dev/null 2>&1; then
    echo "✔ Frontend already running on http://localhost:5173"
else
    echo "👉 Starting Quasar Dev Server in background..."
    (npx quasar dev --port 5173 --hostname 0.0.0.0 >/dev/null 2>&1 &) 
    echo "✔ Frontend started on http://localhost:5173"
fi

cd ..

# -----------------------------------------------
# DONE
# -----------------------------------------------

echo ""
echo "=============================================="
echo "🎉 PROJECT SETUP COMPLETE!"
echo ""
echo " Backend:     http://localhost:8080"
echo " Frontend:    http://localhost:5173 (auto-started)"
echo " phpMyAdmin:  http://localhost:8081"
echo "=============================================="
