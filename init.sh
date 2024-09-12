#!/bin/bash

docker compose build
docker compose up -d

# Installation de symfony
docker compose exec symfony-php composer create-project symfony/skeleton:"6.4.*" .
docker compose exec symfony-php composer require symfony/orm-pack -n
docker compose exec symfony-php composer require --dev symfony/maker-bundle -n
docker compose exec symfony-php composer require --dev symfony/profiler-pack -n
docker compose exec symfony-php composer require symfony/security-bundle -n
docker compose exec symfony-php composer require --dev orm-fixtures -n