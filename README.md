# Laravel API Project - Setup & Developer Guide

Welcome to the Laravel API project. Follow the step-by-step guide below to clone, configure, and execute the application locally.

================================================================================
1. PREREQUISITES
================================================================================
Ensure your local environment meets the following requirement standards:
- PHP >= 8.2 (with pdo, mbstring, openssl, xml, curl extensions enabled)
- Composer >= 2.x
- Database Server: MySQL 8.0+ / PostgreSQL 14+ / SQLite 3
- Git

================================================================================
2. INSTALLATION STEPS
================================================================================

Step 1: Clone the Repository
----------------------------------
$ git clone https://github.com/swasthyamaxxing/sahaara-backend.git
$ cd sahaara-backend

Step 2: Install Composer Dependencies
----------------------------------
$ composer install

Step 3: Environment Configuration
----------------------------------
Duplicate the environment template file:
$ cp .env.example .env

Open `.env` in your text editor and update database configurations:

  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=sahara
  DB_USERNAME=root
  DB_PASSWORD=your_secure_password

Step 4: Generate Application Encryption Key
----------------------------------
$ php artisan key:generate

Step 5: Run Database Migrations and Database Seeding
----------------------------------
Ensure the database (`laravel_api_db`) is created in your local database server, then execute:
$ php artisan migrate --seed

================================================================================
3. RUNNING THE APPLICATION
================================================================================

Start the internal Laravel development server:
$ php artisan serve

By default, the server runs on:
  http://127.0.0.1:8000

All API endpoints are accessible via the `/api` route prefix:
  http://127.0.0.1:8000/api/v1/

================================================================================
4. TESTING & REQUEST HEADERS
================================================================================

When interacting with endpoints via Postman, Bruno, or cURL, ensure the following HTTP headers are included in every request:

  Accept: application/json
  Content-Type: application/json

Example Request:
$ curl -X GET "http://127.0.0.1:8000/api/v1/status" \
       -H "Accept: application/json"