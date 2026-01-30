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



Остановить все контейнеры
docker stop $(docker ps -aq)
docker ps -aq — список всех контейнеров (включая неактивные).
docker stop — останавливает их.
На Windows PowerShell может потребоваться так:
docker ps -aq | ForEach-Object { docker stop $_ }
Удалить все контейнеры
docker rm $(docker ps -aq)
Или в PowerShell:
docker ps -aq | ForEach-Object { docker rm $_ }
Удалить все неиспользуемые образы, сети и тома (чисто для полной уборки)
docker system prune -a --volumes
