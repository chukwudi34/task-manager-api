# Task Manager API

> A RESTful API built with **Laravel** and **PostgreSQL**, providing task management capabilities including CRUD operations, filtering mechanism.

---

## 🚀 About Laravel

**Laravel** is a web application framework with expressive, elegant syntax. It makes development enjoyable and provides powerful features out of the box such as:

-   Simple and fast routing
-   Powerful dependency injection
-   Robust job queues and event broadcasting
-   Elegant database ORM (Eloquent)
-   Database-agnostic schema migrations
-   Modular session and cache systems

---

## 🧰 Technologies Used

-   **Laravel** 10+
-   **PostgreSQL** as the database
-   **PHP** 8.1+
-   **Composer**
-   **Artisan** for CLI commands

---

## ⚙️ Installation & Setup

### Prerequisites

-   PHP 8.1+
-   Composer
-   PostgreSQL
-   Git

### Clone the repository

```bash
git clone git@github.com:chukwudi34/task-manager-api.git
cd task-manager-api

composer install

cp .env.example .env

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

php artisan key:generate

php artisan migrate --seed

php artisan serve

```

API Endpoints (Sample)

| Method | Endpoint          | Description       |
| ------ | ----------------- | ----------------- |
| GET    | `/api/tasks`      | List all tasks    |
| POST   | `/api/tasks`      | Create a new task |
| GET    | `/api/tasks/{id}` | Get task by ID    |
