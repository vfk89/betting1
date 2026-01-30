git clone https://github.com/vfk89/betting1.git .
docker compose down -v
cp .env.example .env
docker compose up -d --build
docker compose exec php composer install
docker compose exec php php bin/migrate.php
docker compose exec php php bin/seed.php
docker compose up -d
docker compose ps

Главная страница: http://localhost:8000/
Клиентская страница: http://localhost:8000/client
Админская страница: http://localhost:8000/admin