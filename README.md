# StudentHub — Student Management System

A beginner-friendly B.Tech 2nd-year web development project built with HTML, CSS, JavaScript, PHP and MySQL.

## Features
- Dashboard statistics (total students, courses and semesters)
- Create, read, update and delete student records
- Search by name, roll number, email or course
- Server-side validation and browser-side form checks
- Prepared PDO statements, HTML output escaping and CSRF tokens for POST actions
- Responsive layout for desktop and mobile

## Requirements
- PHP 8.1+ with PDO MySQL and `mbstring` extensions
- MySQL 8+ or compatible MariaDB
- A browser and code editor (VS Code recommended)

## Setup on macOS

### Option A: Homebrew
1. Install Homebrew from https://brew.sh if it is not already installed.
2. In Terminal, run:
   ```bash
   brew install php mysql
   brew services start mysql
   ```
3. Go into this project folder:
   ```bash
   cd student-management-system
   ```
4. Create/import the database:
   ```bash
   mysql -u root < database.sql
   ```
   If your MySQL root account requires a password, use `mysql -u root -p < database.sql`.
5. Open `config.php`. Set `$username` and `$password` to match your local MySQL account.
6. Start PHP's local development server:
   ```bash
   php -S 127.0.0.1:8000
   ```
7. Visit http://127.0.0.1:8000 in your browser.

### Option B: XAMPP
1. Install XAMPP for macOS and start Apache and MySQL from its manager.
2. Copy this folder into XAMPP's `htdocs` directory.
3. Open phpMyAdmin, import `database.sql`, and verify the database is named `student_management`.
4. Update `config.php` with your MySQL username/password. Common local XAMPP defaults are username `root` and a blank password, but verify your own setup.
5. Visit `http://localhost/student-management-system/`.

## Test checklist
1. Open the dashboard; three sample students should appear after importing `database.sql`.
2. Add a student using a new roll number and email.
3. Try submitting an empty form; required-field validation should appear.
4. Try an invalid email address and invalid phone number.
5. Search using a name, roll number, email or course.
6. Edit a student and verify the changed details appear.
7. Delete a demo/test student and confirm the browser asks for confirmation.
8. Try adding an existing roll number or email; the application should show a duplicate-record message.
9. Refresh the page and confirm saved records remain in MySQL.
10. Test on a narrow browser window to check the responsive layout.

## Folder structure
```text
student-management-system/
├── assets/
│   ├── app.js
│   └── style.css
├── config.php
├── database.sql
├── index.php
├── README.md
├── PROJECT_REPORT.md
└── VIVA_QUESTIONS.md
```

## Important notes
- This is a local academic demo, not a production-ready hosted service.
- The current version has no real authentication. The “Administrator” label is a demo label, not a login system.
- Do not expose database credentials or upload production secrets to GitHub.
- For a public deployment, add authentication/authorization, backups, rate limiting, HTTPS and production error logging.
