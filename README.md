<h1 align="center">Simple Orders API</h1>

## About
The project is a simple API for redeeming items with points, likes, and give ratings. Also available for managing users by super admin.


## Tech Specs
- Environtment / Deploying : **Docker** (`Docker Compose`)
- Programming Language : **PHP 8** (`8.1`)
- Framerwork : **Laravel 8** (`8.83`)
- Database : **MySQL** (`5.7`)
- Database Panel : **phpMyAdmin** (`5.1`)
- API Docs : **Swagger** (`8.3`)
- RBAC (Role Base Access Control) : **Laravel Entrust** (`v2.2`)

## Requirements
Before setup the project, you need to install this : 
- **Docker Compose**

## Installation Guide
- Go to root project
- Run `docker-compose up -d --build`
- Every Laravel command Run after command `docker-compose exec app` (example : `composer install` to `docker-compose exec app composer install`)
- Run `docker-compose exec app composer install`
- Run `docker-compose exec app npm install`
- Copy `env.example` to `.env` and fill with your database credentials (for `db` and `phpmyadmin` containers in `docker-compose-yml`)
- Copy `src/.env.example` to `src/.env` and fill with your app / Laravel credentials
- Copy `src/.env.example` to `src/.env.testing` for unit testing
- Create database via `db` or `phpmyadmin` container
- Run `docker-compose exec app php artisan migrate:refresh --seed`
- Run `docker-compose exec app php artisan key:generate`
- Run `docker-compose exec app php artisan storage:link`

## URL 
- Web : `http://localhost:8080` (For change the port, update in `docker-compose.yml` file in `app` container)
- MySQL : `http://localhost:3037` (For change the port, update in `docker-compose.yml` file in `db` container)
- phpMyAdmin : `http://localhost:8082` (For change the port, update in `docker-compose.yml` file in `phpmyadmin` container)
- After change the port you can run again `docker-compose up -d --build`

## API Docs
- API Docs URL : `/api/v1/documentation`
- Generate / Regenerate Swagger docs : `docker-compose exec app php artisan l5-swagger:generate`

## Dummy Users
- Super Admin : `super@mail.com/password123`
- User : `user1@mail.com/password123` and `user2@mail.com/password123`
