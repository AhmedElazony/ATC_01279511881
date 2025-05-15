# Event Booking Platform

## Overview

This is an event booking platform that allows users to browse, search, and book tickets for various events. The platform features a modern, responsive UI for users and a powerful admin panel for event and user management.

## Key Features

- **User Authentication**: Register, login, email verification, and password reset
- **Event Management**: Browse, search, filter, and view detailed information about events
- **Booking System**: Reserve events
- **Booking History**: View Booking History of the user
- **User Profiles**: View personal information and user stats
- **Admin Panel**: Comprehensive tools for administrators to manage events, bookings, and users
- **Responsive Design**: Fully responsive UI that works on mobile, tablet, and desktop devices
- **Dark/Light Mode**: Support for both dark and light themes

## Tech Stack

### Frontend

- Vue.js 3 with Composition API
- Pinia for state management
- Vue Router for navigation
- Tailwind CSS for styling
- Axios for API communication

### Backend

- Laravel 12
- MySQL database
- Sanctum for API authentication
- Laravel's validation, migration, and ORM systems
- Postman for API Testing

## Project Structure

The project is divided into two main directories:

- `frontend/` - Contains the Vue.js application
- `backend/` - Contains the Laravel API

Each directory has its own detailed documentation:

- [Frontend Documentation](./frontend/README.md)
- [Backend Documentation](./backend/READMEw.md)

## Getting Started

### Prerequisites

- PHP 8.2 or higher
- PHP extensions should be installed:
  - pdo, mysql, bcmatch, curl, exif, gd, gmp, iconv, intl, sodium, zip
- Composer
- Node.js 23 or higher
- npm
- MySQL

### Installation

#### 1. Clone the repository:

```shell
git clone https://github.com/AhmedElazony/ATC_01279511881
```

#### 2. Set Up the Backend

1. **Navigate to the backend directory:**

   ```shell
   cd backend
   ```

2. **Install PHP dependencies using Composer:**

   ```shell
   composer install
   ```

3. **Configure the Environment:**

   Copy the example environment file and customize it:

   ```shell
   cp .env.example .env
   ```

4. **Generate an Application Key:**

   ```shell
   php artisan key:generate
   ```

5. **Configure Database Settings:**

   Open the `.env` file and update the following variables:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_db_name
   DB_USERNAME=your_db_username
   DB_PASSWORD=your_db_password
   ```

6. **Migrate and Seed the Database:**
   `to seed the Super Admin and some events.`

   ```shell
   php artisan migrate --seed
   ```

7. **(Optional) Run the Backend Development Server:**

   ```shell
   php artisan serve
   ```

   The backend API will run on [http://localhost:8000](http://localhost:8000).

#### 3. Set Up the Frontend

1. **Navigate to the frontend directory:**

   Open a new terminal window and run:

   ```shell
   cd frontend
   ```

2. **Install Node.js Dependencies:**

   ```shell
   npm install
   ```

3. **Configure the Frontend Environment:**

   (If provided) Copy the example environment file and update variables like the API URL:

   ```shell
   cp .env.example .env
   ```

   Ensure the `VITE_API_URL` (or similar) points to your backend, e.g., `http://localhost:8000/api/v1`.
   **note: there should be the postfix `/api/v1` after the backend url.`**

4. **Start the Frontend Development Server:**

   ```shell
   npm run dev
   ```

   The frontend should be available on [http://localhost:5173](http://localhost:5173) (or the port indicated by Vite).

#### 4. Testing

- **Backend Tests:**

  From the `backend` directory, run:

  ```shell
  php artisan test
  ```

### Deployment

---

### AI Contributions

Used Github Copilot in this project in a lot of aspects:

- architecture recommendation.
- debugging.
- frontend styling.
- documenting the project.
- deploying the project.
