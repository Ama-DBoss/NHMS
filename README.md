# National Birth & Death Management System (NHMS)

A comprehensive web-based system for managing birth and death records at the national level. This platform enables hospitals to issue official birth and death certificates, while administrators can oversee the entire system.

## Features

- **Hospital Management**: Register and manage hospitals
- **Birth Certificate Issuance**: Create and manage birth certificates with complete record details
- **Death Certificate Issuance**: Create and manage death certificates with comprehensive information
- **Admin Dashboard**: Centralized dashboard for system administrators to monitor all records
- **PDF Export**: Generate and download official certificates in PDF format
- **Secure Authentication**: Role-based access control (Hospital & Admin roles)
- **Audit Logging**: Complete activity logging for security and compliance
- **Session Management**: Secure session handling with CSRF protection

## System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher / MariaDB 10.2 or higher
- Web server (Apache, Nginx, etc.)
- HTTPS support (strongly recommended for production)

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd NHMS
```

### 2. Configure Database

Create a MySQL database:

```bash
mysql -u root -p < setup.sql
```

This will:
- Create the `nigerian_hospital_registry` database
- Create all required tables
- Insert a default admin account

### 3. Configure Environment Variables

Create a `.env` file in the project root:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=nigerian_hospital_registry
DB_USER=root
DB_PASS=your_password
```

For production, ensure you use strong database credentials.

### 4. Set File Permissions

```bash
chmod 755 NHMS/
chmod 755 NHMS/includes/
chmod 755 NHMS/css/
chmod 755 NHMS/js/
chmod 755 NHMS/admin/
```

### 5. Configure Web Server

**For Apache**, ensure `.htaccess` support is enabled.

**For Nginx**, configure the server block to handle PHP files properly.

### 6. Access the Application

Navigate to your web server:
```
http://localhost/NHMS/
```

## Default Credentials

**Admin Account:**
- Email: `admin@nhms.gov`
- Password: `admin@123` (change this immediately in production)

**Hospital Registration:**
Hospitals can self-register through the registration page.

## User Roles

### Hospital User
- Issue birth certificates
- Issue death certificates
- View their own records
- Download certificates

### Administrator
- View all hospitals
- View all birth and death records
- Manage hospital accounts
- Monitor system activity

## Security Features

✓ CSRF Token Protection
✓ Secure Password Hashing (bcrypt)
✓ SQL Injection Prevention (Prepared Statements)
✓ XSS Protection (Input Sanitization)
✓ Session Security (HTTP-only cookies, Secure flag)
✓ Activity Logging for Audit Trail
✓ Security Headers (X-Frame-Options, X-Content-Type-Options, etc.)
✓ Input Validation and Sanitization
✓ Role-Based Access Control

## Production Deployment Checklist

- [ ] Change default admin password
- [ ] Enable HTTPS/SSL
- [ ] Configure strong database credentials
- [ ] Set up regular database backups
- [ ] Enable error logging to file (not display)
- [ ] Set appropriate file permissions
- [ ] Configure firewall rules
- [ ] Set up monitoring and alerting
- [ ] Configure automated certificate expiration handling (if applicable)
- [ ] Enable application logging
- [ ] Test certificate generation and download functionality

## API Endpoints

### Authentication
- `POST /index.php` - Login
- `POST /register.php` - Hospital registration
- `GET /logout.php` - Logout

### Hospital Operations
- `GET /dashboard.php` - Hospital dashboard
- `GET/POST /generate_certificate.php?type=birth` - Issue birth certificate
- `GET/POST /generate_certificate.php?type=death` - Issue death certificate
- `GET /view_births.php` - View birth records
- `GET /view_deaths.php` - View death records
- `GET /download_certificate.php?type=birth&id=ID` - Download certificate

### Admin Operations
- `GET /admin/dashboard.php` - Admin dashboard
- `GET /admin/view_hospitals.php` - View all hospitals
- `GET /admin/view_births.php` - View all birth records
- `GET /admin/view_deaths.php` - View all death records

## Database Schema

### tables
- `hospitals` - Registered hospital organizations
- `admins` - System administrators
- `birth_certificates` - Birth record certificates
- `death_certificates` - Death record certificates
- `activity_logs` - Audit trail of system activities

## Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check database credentials in `.env`
- Ensure database and tables are created

### Certificate Download Not Working
- Verify TCPDF library is installed
- Check file permissions in `/tmp` directory
- Ensure adequate disk space

### Login Issues
- Clear browser cookies and cache
- Verify database connection
- Check if hospital/admin account exists

## Support

For issues, questions, or contributions, please contact the development team or create an issue in the repository.

## License

This project is developed for national vital statistics management.

## Version

Current Version: 1.0 (Production Ready)

Last Updated: 2026
