# Employee Attendance System

A robust Laravel-based web application for managing employee check-ins and check-outs with photo verification and role-based dashboards for managers and employees.

## 🚀 Features

-   **Role-Based Access Control (RBAC):** Separate dashboards for Employees and Managers.
-   **Photo Verification:** Employees must take a picture for both check-in and check-out.
-   **Manager Notifications:** Managers receive real-time notifications when employees clock in/out.
-   **Attendance History:** Managers can view their team's attendance records for any given day.
-   **Secure Authentication:** Built on Laravel Breeze with session authentication, CSRF protection, and password hashing.
-   **Docker Support:** Ready for containerized development and deployment.

## 🛠️ Built With

-   **Backend Framework:** [Laravel 12](https://laravel.com)
-   **Authentication:** [Laravel Breeze](https://laravel.com/docs/starter-kits#breeze)
-   **Frontend:** Blade Templating, Tailwind CSS
-   **Database:** MySQL
-   **Containerization:** Docker

## 📋 Prerequisites

Before you begin, ensure you have the following installed on your machine:
-   **Docker** and **Docker Compose**
-   **Git**
-   **Composer** (if not using Docker for PHP)

## ⚡ Quick Installation

1.  **Clone the repository:**
    ```bash
    git clone <your-repository-url>
    cd employee-attendance-system
    ```

2.  **Copy the environment file:**
    ```bash
    cp .env.example .env
    ```
    *Edit the `.env` file to set your database credentials (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) and application `APP_KEY`.*

3.  **Start the Docker containers:**
    ```bash
    ./vendor/bin/sail up -d
    ```
    *or if using local Composer:*
    ```bash
    docker-compose up -d
    ```

4.  **Install PHP dependencies:**
    ```bash
    ./vendor/bin/sail composer install
    ```

5.  **Generate the application key:**
    ```bash
    ./vendor/bin/sail artisan key:generate
    ```

6.  **Run database migrations & seeders:**
    ```bash
    ./vendor/bin/sail artisan migrate --seed
    ```

7.  **Visit the application:**
    Open your browser and go to `http://localhost`.

## 👥 Default Users

After seeding, the following test users are created:

1.  **Manager Account:**
    -   **Email:** manager@example.com
    -   **Password:** password
    -   **Role:** Manager (Can view all employee dashboards)

2.  **Employee Account:**
    -   **Email:** employee@example.com
    -   **Password:** password
    -   **Role:** Employee (Can check-in/out, reports to the manager)

## 🗄️ Database Schema

### Key Tables:
-   **`users`:** Stores all users (employees and managers). Managers have a `null` `manager_id`.
-   **`attendance_records`:** Stores each check-in/out event with a timestamp and photo path.
-   **`notifications`:** Stores in-app notifications for managers.

### Relationships:
-   A `User` (manager) `hasMany` `User` (employees).
-   An `Employee` `belongsTo` a `User` (manager).
-   A `User` `hasMany` `AttendanceRecord`s.
-   An `AttendanceRecord` `belongsTo` a `User`.

## 🧪 Running Tests

Execute the test suite with the following command:
```bash
./vendor/bin/sail test
# or
./vendor/bin/sail artisan test