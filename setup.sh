#!/bin/bash
set -e

echo "🔥 START - cleaning environment and creating project technical-assessment"

# -------------------------
# 0. Kill processes blocking ports
# -------------------------
echo "🛑 Killing potential processes blocking ports..."
pkill -f "python3 -m http.server" 2>/dev/null || true
pkill -f "python -m http.server" 2>/dev/null || true
pkill -f python 2>/dev/null || true
pkill -f node 2>/dev/null || true

# -------------------------
# 1. Remove old project directory
# -------------------------
echo "🗑 Removing technical-assessment directory..."
rm -rf technical-assessment || true
mkdir -p technical-assessment
cd technical-assessment

# -------------------------
# 2. Full Docker cleanup
# -------------------------
echo "🧨 Cleaning Docker..."
docker kill $(docker ps -q) 2>/dev/null || true
docker rm -f $(docker ps -aq) 2>/dev/null || true
docker rmi -f $(docker images -aq) 2>/dev/null || true
docker volume rm $(docker volume ls -q) 2>/dev/null || true
docker network prune -f 2>/dev/null || true
docker system prune -af --volumes || true

# -------------------------
# 3. Folder structure
# -------------------------
echo "📁 Creating folder structure..."
mkdir -p backend frontend docker/nginx

# -------------------------
# 4. Install Laravel backend
# -------------------------
echo "📦 Installing Laravel backend..."

if ! command -v composer >/dev/null 2>&1; then
  echo "❗ Composer missing"
  exit 1
fi

composer create-project laravel/laravel backend --ignore-platform-reqs

echo "📦 Installing backend dependencies..."
cd backend
composer require firebase/php-jwt
composer require barryvdh/laravel-dompdf
composer require endroid/qr-code
cd ..

# -------------------------
# 5. Install Quasar frontend
# -------------------------
echo "⚡ Installing Quasar frontend..."
cd frontend

if ! command -v node >/dev/null 2>&1; then
  echo "❗ Node.js missing"
  exit 1
fi

npm create quasar@latest . --yes --template clean
npm install

echo "📦 Installing frontend dependencies..."
npm install axios
npm install jwt-decode
npm install @vueuse/core

echo "🧹 Removing .git inside frontend (twice – Quasar bug)..."


rm -rf .git || true

cd ..

# -------------------------
# REMOVE ALL GIT REPOSITORIES to allow initializing ONE git repo later
# -------------------------
echo "🔨 Removing ALL git repositories in project..."

rm -rf .git || true
rm -rf backend/.git || true
rm -rf frontend/.git || true

find . -type d -name ".git" -exec rm -rf {} + 2>/dev/null || true

echo "✔️ All .git directories removed."

# -------------------------
# 6. Create NGINX config
# -------------------------
echo "📝 Creating nginx config..."
cat > docker/nginx/default.conf <<'NGINX'
server {
    listen 80;
    resolver 127.0.0.11;

    root /var/www/html/public;
    index index.php index.html;

    location / {
        try_files $uri /index.php?$query_string;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass tech-php:9000;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
NGINX

# -------------------------
# 7. docker-compose
# -------------------------
echo "📜 Creating docker-compose.yml..."
cat > docker-compose.yml <<'YAML'
services:
  php:
    image: php:8.4-fpm
    container_name: tech-php
    working_dir: /var/www/html
    volumes:
      - ./backend:/var/www/html
    networks:
      - appnet

  nginx:
    image: nginx:alpine
    container_name: tech-nginx
    ports:
      - "8080:80"
    volumes:
      - ./backend:/var/www/html
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - php
    networks:
      - appnet

  mysql:
    image: mysql:8
    container_name: tech-mysql
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: technical
    ports:
      - "3307:3306"
    networks:
      - appnet

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    container_name: tech-pma
    environment:
      PMA_HOST: mysql
    ports:
      - "8081:80"
    networks:
      - appnet

networks:
  appnet:
    driver: bridge
YAML

# -------------------------
# 8. Run Docker
# -------------------------
echo "🚀 Starting backend (Docker)..."
docker compose up -d --build

# -------------------------
# 9. Final info
# -------------------------
echo ""
echo "✅ Done."
echo "Backend:    http://localhost:8080"
echo "phpMyAdmin: http://localhost:8081"
echo ""
echo "Now INIT your git repo:"
echo "  cd $(pwd)"
echo "  git init"
echo ""
echo "Run frontend:"
echo "  cd frontend"
echo "  npm run dev -- --host 0.0.0.0 --port 5173"
echo ""
echo "NOTE:"
echo "- ALL .git folders were removed so you can initialize ONE repo cleanly."
echo "- Backend: JWT + PDF + QR libraries installed."
echo "- Frontend: axios + jwt-decode + vueuse installed."
