# Development Environment Setup

## 1. Prerequisites

**Python 3.11+**
- python --version
- pip --version

**XAMPP (for MySQL)**
- Download: https://www.apachefriends.org/download.html
- Start Apache and MySQL services from XAMPP Control Panel
- Access phpMyAdmin at http://localhost/phpmyadmin

**GIT**
- git --version

## 2. Environment Variables
- Copy .env.example into .env
- Update values based on your machine

## 3. Backend Setup
- cd backend
- python -m venv venv (Create virtual enivronment)
- venv\Scripts\activate (Activate virtual environment)
- pip install -r requirements.txt
- python app.py
- Visitng http://localhost:5000/health should offer a basic health check.

## 4. Database Setup
### Migrations
- flask db init
- flask db migrate -m "initial"
- flask db upgrade
### Seeding
- python seed.py

## 5. Frontend Setup

## 6. Lint/format/test commands
- black backend/
- flake8 backend/
- pytest

## 7. Branching Strategy
- main - finished working ccode
- production - active development of combined pieces
- feature/ - specific feature

## 8. Commit Conventions
- feat: new features
- fix: bug fixes
- docs: added documentation
- chore: simple tasks

