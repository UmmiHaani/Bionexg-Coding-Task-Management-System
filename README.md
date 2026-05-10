# BIONEXG CODING TASK MANAGEMENT SYSTEM

Date: 19 november 2024

The user interface (UI) design in this project is minimal, as the primary focus of this lab task is on back-end development and functionality. The current UI serves only as a basic interface to demonstrate the system's features.

## Project Overview

This is a PHP and MySQL task management system with two roles:
- Admin
- Employee

## Requirements

- PHP installed (CLI/web server)
- MySQL server running

## How to Run

1. Open a terminal in the project folder:

   ```bash
   cd "/Users/mac/Desktop/LET'S FIX GITHUB/BIONEXG/BACK-END LEARNING"
   ```

2. Create and seed the database:

   ```bash
   mysql -u root < "-- Create the database.sql"
   ```

3. Start the PHP development server:

   ```bash
   php -S 127.0.0.1:8000
   ```

4. Open in browser:

   - Login page: `http://127.0.0.1:8000/login.php`
   - Or index: `http://127.0.0.1:8000/index.html`

## Default Accounts

- Admin: `admin` / `123`
- Employee: `employee` / `123`
