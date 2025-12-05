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
# 1. Clone repository
# -----------------------------------------------
if [ ! -d "technical-assessment" ]; then
    echo "👉 Cloning repository..."
    git clone https://github.com/pult3r/technical-assessment.git
else
    echo "✔ Repository already exists, skipping clone."
fi

cd technical-assessment

# -----------------------------------------------
# 2. Backend setup (Laravel)
# -----------------------------------------------
echo "----------------------------------------------"
echo " INSTALLING BACKEND"
echo "----------------------------------------------"

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
# 3. Start Docker environment
# -----------------------------------------------
echo "----------------------------------------------"
echo " STARTING DOCKER ENVIRONMENT"
echo "----------------------------------------------"

docker compose down -v || true
docker compose up -d --build

echo "⏳ Waiting for containers to start..."
sleep 5

# -----------------------------------------------
# 4. Run Laravel commands in PHP container
# -----------------------------------------------
echo "----------------------------------------------"
echo " RUNNING LARAVEL MIGRATIONS"
echo "----------------------------------------------"

PHP_CONTAINER="tech-php"

echo "👉 Generating APP_KEY..."
docker exec -it $PHP_CONTAINER php artisan key:generate

echo "👉 Running migrations..."
docker exec -it $PHP_CONTAINER php artisan migrate -v

# -----------------------------------------------
# 5. Frontend setup (Quasar)
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

echo "🎉 Frontend can be started manually with:"
echo "   npx quasar dev --port 5173 --hostname 0.0.0.0"

cd ..

# -----------------------------------------------
# DONE
# -----------------------------------------------
echo ""
echo "=============================================="
echo "🎉 PROJECT SETUP COMPLETE!"
echo ""
echo " Backend:     http://localhost:8080"
echo " Frontend:    http://localhost:5173 (start manually)"
echo " phpMyAdmin:  http://localhost:8081"
echo "=============================================="
