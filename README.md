# Employee Daily Work Update & Performance Evaluation System

A PHP and MySQL web application for tracking employees' daily work updates and supporting manager-led task management, reviews, and performance reporting.

## Features

- Employee and manager login
- Employee dashboard
- Daily work updates
- Employee task list
- Manager dashboard
- Task management
- Performance reviews
- Reports

## Tech Stack

- **Backend:** PHP
- **Database:** MySQL
- **Frontend:** HTML, CSS
- **Local development:** XAMPP
- **Database access:** PHP PDO

## Project Structure

```text
employee-work-system/
├── assets/
│   └── style.css
├── auth/
│   ├── login.php
│   └── logout.php
├── config/
│   └── db.php
├── employee/
│   ├── daily-update.php
│   ├── dashboard.php
│   └── my-tasks.php
├── includes/
│   ├── auth.php
│   └── header.php
├── manager/
│   ├── dashboard.php
│   ├── reports.php
│   ├── reviews.php
│   └── tasks.php
└── README.md
```

## Requirements

- XAMPP (Apache and MySQL)
- PHP version supported by your installed XAMPP
- A web browser
- Git (optional, for version control)

## Setup Instructions (Local XAMPP)

1. **Clone or download the repository**

   ```bash
   git clone https://github.com/sonu6260/employee-work-system.git
   ```

2. **Place the project in XAMPP's `htdocs` directory**

   Example path:

   ```text
   C:\xampp\htdocs\employee-work-system
   ```

3. **Start XAMPP services**

   Open the XAMPP Control Panel and start **Apache** and **MySQL**.

4. **Create the database**

   Open phpMyAdmin at `http://localhost/phpmyadmin` and create a database named:

   ```text
   employee_work_system
   ```

5. **Import the database schema**

   If the repository contains a `.sql` file, select the `employee_work_system` database in phpMyAdmin and import it.

   **Note:** The current repository structure does not show a SQL export file. To make setup reproducible for another developer, export the schema and safe demo data from your local database and add the `.sql` file to the repository.

6. **Check database configuration**

   Open `config/db.php` and configure the connection for your local MySQL setup. The current code is configured for a local XAMPP-style connection (`localhost`, database `employee_work_system`, user `root`, blank password). Change these values if your credentials differ.

7. **Open the application**

   Visit:

   `http://localhost/employee-work-system/auth/login.php`

## Login Credentials

Use the employee and manager accounts configured in your local database. Do not publish real passwords, personal information, or production credentials in this README. If you want to share demo credentials, create dedicated test accounts with non-sensitive passwords.

## Security Notes

- Do not commit production database passwords, API keys, or other secrets.
- Use prepared statements for database queries.
- Use secure password hashing and verify passwords using PHP's password APIs.
- Protect pages with authentication and role-based authorization.
- For deployment, use HTTPS and a database user with only the permissions the application needs.
- Avoid showing database error details to end users.

## Future Improvements

- Add a database schema export and safe demo seed data.
- Add automated tests and input validation.
- Add pagination and filters for reports.
- Add deployment instructions for a hosted PHP/MySQL environment.

## Repository

GitHub: https://github.com/sonu6260/employee-work-system

---

**Project:** Employee Daily Work Update & Performance Evaluation System  
**Developer:** Sonu Ahirwar
