# Backend Documentation

## Overview

This backend is built with **Laravel 12** and provides a RESTful API for the Areeb Event Booking Platform. It includes user authentication with **Laravel Sanctum**, database migrations, seeding, and an organized MVC structure.

---

## Requirements

-   **PHP** 8.2 or higher
-   **Composer**
-   **MySQL** or compatible database

Make sure you have the necessary PHP extensions installed (pdo, mysql, curl, exif, gd, gmp, iconv, intl, etc.).

---

## Directory Structure

```plaintext
backend/
├── app/ // Controllers, Models, etc.
├── config/ // Laravel configuration files
├── database/
│ ├── factories/
│ ├── migrations/
│ └── seeders/
├── public/ // Publicly accessible files
├── routes/ // API & web routes files
├── tests/ // Test files
└── ...
```

---

## API Endpoints

The backend exposes RESTful endpoints. Here are a few examples:

-   **Authentication**

    -   POST /api/v1/auth/login
    -   POST /api/v1/auth/register
    -   DELETE /api/v1/auth/logout
    -   POST /api/v1/auth/verify-email
    -   POST /api/v1/auth/forgot-password
    -   POST /api/v1/auth/reset-password
    -   POST /api/v1/auth/send-verification-otp

-   **Events**

    -   GET /api/v1/events
    -   GET /api/v1/events/{id}/show
    -   GET /api/v1/events/categories
    -   GET /api/v1/home (the website home page)
    -   GET /api/v1/events/booked (events booked by auth user)
    -   POST /api/v1/events/{id}/book (book an event)

-   **Admin**
    -   POST /api/v1/admin/events/store
    -   PUT /api/v1/admin/events/{id}/update
    -   DELETE /api/v1/admin/events/{id}/destroy
    -   GET /api/v1/admin/events
    -   GET /api/v1/admin/bookings
    -   GET /api/v1/admin/users
    -   POST /api/v1/admin/users

**View the API documentation in postman: [API Documentation](https://documenter.getpostman.com/view/24857412/2sB2qWF3jk)**

---

## Testing

Run automated tests with:

```shell
php artisan test
```

Output includes pass/fail details, code coverage, and any relevant error messages.

---

## Security & Authentication

-   **Sanctum** is used for API token authentication.
-   **CSRF** protection is active on stateful web routes.

---
