# Development Environment Setup

## 1. Prerequisites

**Python 3.11+**
- ```bash python --version```
- ```bash pip --version```

**XAMPP (for MySQL)**
- Download: https://www.apachefriends.org/download.html
- Start Apache and MySQL services from XAMPP Control Panel
- Access phpMyAdmin at http://localhost/phpmyadmin
- Go to Databases->Create database
- Enter "symvan_db"->Create
- Go to User Accounts->Add user account
- User name: "symvan_admin"
- Host name: "localhost"
- Password: (your unique password)
- Under "Global privileges" click "Check all"

**GIT**
- ```bash git --version```

## 2. Environment Variables
- Copy .env.example into .env
- Update db_password to your unique password.
- If you decide to use OPENAI, this is where you can add your API key.

## 3. Database Setup
### Create Tables
- Go to phpMyAdmin at http://localhost/phpmyadmin
- Find the SQL tab
- Paste the most recent SQL file from setup/ into SQL editor
- Click "Go"
- Check that tables have been created

## 4. Backend Setup
### Composer setup in order to read from .env file
- Open a new command prompt and navigate to "C:\xampp\htdocs\"
- ```bash composer require vlucas/phpdotenv```

## 5. Frontend Setup
- In file explorer, find "C:\xampp\htdocs\"
- Paste all contents from frontend\ into htdocs folder.
- With XAMPP still running navigate to http://localhost/index.php

## 6. Branching Strategy
- main - finished working ccode
- production - active development of combined pieces
- feature/ - specific feature

## 7. Commit Conventions
- feat: new features
- fix: bug fixes
- docs: added documentation
- chore: simple tasks

