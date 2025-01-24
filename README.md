
# Laravel Roster Management API

This is a Laravel-based API project for parsing and managing airline crew rosters. The application includes features for uploading roster files, retrieving flight schedules, and generating test coverage reports.

---

## Features

- Upload and parse HTML roster files.
- Retrieve flight schedules, standby duties, and events for specific timeframes.
- Query flights by start location.
- RESTful API endpoints for seamless integration.
- Test coverage report for API functionality.

---

## Prerequisites

- **PHP**: 8.2 or higher (tested with PHP 8.4)
- **Composer**: Dependency manager for PHP
- **SQLite**: Default database for testing and development
- **Node.js**: (Optional) For frontend assets or additional tooling
- **Xdebug**: For generating test coverage reports (optional)

---

## Installation

1. Clone the repository:
   ```bash
     git clone <repository-url>
     cd <repository-folder>


2. Install dependencies:
   ```bash  
     composer install


3. Copy the environment file:
    ```bash  
     cp .env.example .env


4. Configure the .env file: Update the database connection if necessary (default is SQLite):
    ```bash  
     DB_CONNECTION=sqlite
     DB_DATABASE=/full/path/to/database/database.sqlite


5. Create an SQLite database file:
    ```bash  
     touch database/database.sqlite


6. Run database migrations:
    ```bash  
     php artisan migrate


7. Generate an application key:
    ```bash  
     php artisan key:generate


  

