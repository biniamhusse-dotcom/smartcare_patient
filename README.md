# SmartCare Patient Management System

A PHP/MySQL patient management system for **Boru Meda General Hospital** with public MRN search, admin panel with CRUD operations, CSV import, live search, summary cards, and configurable facility name.

---

## Features

- **Public Search** - Search patients by MRN, name, sex, DOB, district, community, or mobile
- **Admin Panel** - Login-protected dashboard for managing patients
- **Live Search** - Auto-filters results as you type (300ms debounce)
- **Summary Cards** - Total patients, Male, Female, With DOB, With Mobile (updates live)
- **CSV Import** - Bulk import patients from CSV files with transaction batching
- **Facility Name** - Configurable via admin Settings page
- **Responsive Design** - Works on desktop and mobile devices

---

## Default Credentials

| Field | Value |
|-------|-------|
| Admin Username | `admin` |
| Admin Password | `admin123` |

---

## Project Structure

```
smartcare_patient/
├── docker-compose.yml
├── Dockerfile
├── sql/
│   └── init.sql
└── src/
    ├── index.php              # Public search page
    ├── config/
    │   └── db.php             # Database connection
    ├── includes/
    │   ├── auth.php           # Session authentication
    │   ├── search_logic.php   # AJAX search endpoint
    │   ├── get_stats.php      # Summary cards stats
    │   ├── import_csv.php     # CSV import handler
    │   └── settings.php       # Settings helper
    └── admin/
        ├── login.php          # Admin login
        ├── index.php          # Admin dashboard
        ├── add_patient.php    # Add patient form
        ├── edit_patient.php   # Edit patient form
        ├── delete_patient.php # Delete handler
        ├── import.php         # Admin CSV import
        ├── settings.php       # Facility name settings
        └── logout.php         # Session destroy
```

---

## Quick Setup (Docker)

### Prerequisites

- Docker and Docker Compose installed
- Git

### 1. Clone the Repository

```bash
git clone https://github.com/biniamhusse-dotcom/smartcare_patient.git
cd smartcare_patient
```

### 2. Build and Start

```bash
docker compose up -d --build
```

### 3. Verify Services

```bash
docker compose ps
```

Expected output:
| Service | Port |
|---------|------|
| App (PHP/Apache) | 8087 |
| MySQL | 3310 |
| phpMyAdmin | 8084 |

### 4. Access

| URL | Description |
|-----|-------------|
| http://localhost:8087 | Public search page |
| http://localhost:8087/admin/login.php | Admin login |
| http://localhost:8084 | phpMyAdmin |

---

## Manual Setup (Without Docker)

### Prerequisites

- PHP 8.0+ with PDO MySQL extension
- MySQL 8.0+
- Apache/Nginx web server

### 1. Import Database

```bash
mysql -u root -p boru_meda_hospital < sql/init.sql
```

### 2. Configure Database Connection

Edit `src/config/db.php`:

```php
$host = 'localhost';
$dbname = 'boru_meda_hospital';
$username = 'root';
$password = 'your_password';
```

### 3. Configure Web Server

Point your web server document root to the `src/` directory.

---

## Database Credentials

| Field | Value |
|-------|-------|
| Host | localhost (Docker) / 127.0.0.1 (manual) |
| Database | boru_meda_hospital |
| Username | root |
| Password | boru_meda_hospital |

---

## Docker Commands

### Start Services

```bash
docker compose up -d
```

### Stop Services

```bash
docker compose down
```

### Rebuild and Restart

```bash
docker compose down -v
docker compose up -d --build
```

### View Logs

```bash
# App logs
docker compose logs --tail=50 app

# MySQL logs
docker compose logs --tail=50 db

# All logs
docker compose logs --tail=50
```

### Access Container Shell

```bash
# App container
docker exec -it smartcare_app bash

# MySQL container
docker exec -it smartcare_db bash
```

### MySQL Commands

```bash
# Access MySQL CLI
docker exec -it smartcare_db mysql -uroot -pboru_meda_hospital boru_meda_hospital

# Show tables
docker exec smartcare_db mysql -uroot -pboru_meda_hospital boru_meda_hospital -e "SHOW TABLES;"

# Check admin users
docker exec smartcare_db mysql -uroot -pboru_meda_hospital boru_meda_hospital -e "SELECT username, password FROM admin_users;"

# Count patients
docker exec smartcare_db mysql -uroot -pboru_meda_hospital boru_meda_hospital -e "SELECT COUNT(*) FROM patients;"
```

---

## Deployment on Ubuntu

### 1. Install Docker

```bash
sudo apt update
sudo apt install -y docker.io docker-compose-plugin
sudo systemctl enable docker
sudo systemctl start docker
sudo usermod -aG docker $USER
```

### 2. Clone and Build

```bash
git clone https://github.com/biniamhusse-dotcom/smartcare_patient.git
cd smartcare_patient
git pull origin main
sudo docker compose down -v
sudo docker compose up -d --build
```

### 3. Pull Updates

```bash
cd ~/smartcare_patient
git pull origin main
sudo docker compose down -v
sudo docker compose up -d --build
```

---

## Troubleshooting

### Login Not Working

1. Clear browser cache: `Ctrl + Shift + Delete` → Select "Cached images and files" → Clear
2. Check logs: `docker compose logs --tail=30 app`
3. Verify password hash:
```bash
docker exec smartcare_db mysql -uroot -pboru_meda_hospital boru_meda_hospital -e "SELECT username, password FROM admin_users;"
```

### Services Not Starting

```bash
# Check port conflicts
sudo lsof -i :8087
sudo lsof -i :3310

# Check container status
docker compose ps

# Rebuild from scratch
docker compose down -v
docker compose up -d --build
```

### Database Connection Error

```bash
# Verify MySQL is running
docker compose ps db

# Test connection
docker exec smartcare_db mysql -uroot -pboru_meda_hospital boru_meda_hospital -e "SELECT 1;"
```

### PHP Fatal Error / White Page

```bash
# Check PHP error logs
docker compose logs --tail=50 app

# Check if settings table exists
docker exec smartcare_db mysql -uroot -pboru_meda_hospital boru_meda_hospital -e "SHOW TABLES LIKE 'settings';"
```

---

## Technology Stack

- **Backend**: PHP 8.2
- **Database**: MySQL 8.0
- **Frontend**: Bootstrap 5.3, Bootstrap Icons
- **Container**: Docker, Docker Compose
- **Web Server**: Apache 2.4

---

## License

Internal use for Boru Meda General Hospital.
