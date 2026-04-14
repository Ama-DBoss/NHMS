# NHMS Production Deployment Guide

This guide provides step-by-step instructions for deploying the National Birth & Death Management System to a production environment.

## Pre-Deployment Checklist

- [ ] All code changes have been tested locally
- [ ] Database backup created
- [ ] SSL/TLS certificate obtained
- [ ] Production server is ready (PHP 7.4+, MySQL 5.7+)
- [ ] Domain name configured
- [ ] Email service configured (optional)
- [ ] Monitoring and logging services configured

## Step 1: Prepare the Production Server

### 1.1 Install Dependencies

```bash
# Update system
sudo apt-get update && sudo apt-get upgrade -y

# Install PHP and extensions
sudo apt-get install -y php php-mysql php-cli php-gd php-curl php-json php-mbstring

# Install MySQL/MariaDB
sudo apt-get install -y mysql-server

# Install web server (Apache)
sudo apt-get install -y apache2
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### 1.2 Create System User

```bash
# Create a dedicated user for the application
sudo useradd -m -d /home/nhms -s /bin/bash nhms
sudo usermod -aG www-data nhms
```

### 1.3 Configure Firewall

```bash
# Enable firewall
sudo ufw enable

# Allow HTTP and HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 22/tcp

# Verify rules
sudo ufw status
```

## Step 2: Deploy Application Files

### 2.1 Clone Repository

```bash
cd /var/www/
sudo -u nhms git clone <repository-url> nhms
cd nhms
```

### 2.2 Set File Permissions

```bash
# Set ownership
sudo chown -R nhms:www-data /var/www/nhms

# Set directory permissions
find /var/www/nhms -type d -exec chmod 755 {} \;

# Set file permissions
find /var/www/nhms -type f -exec chmod 644 {} \;

# Make PHP files readable by web server
chmod 755 /var/www/nhms/NHMS
chmod 755 /var/www/nhms/NHMS/includes
chmod 755 /var/www/nhms/NHMS/admin

# Create writable directories for logs and uploads
mkdir -p /var/log/nhms
mkdir -p /var/www/nhms/NHMS/uploads
sudo chown -R www-data:www-data /var/log/nhms
sudo chown -R www-data:www-data /var/www/nhms/NHMS/uploads
sudo chmod 755 /var/log/nhms
sudo chmod 755 /var/www/nhms/NHMS/uploads
```

## Step 3: Configure Database

### 3.1 Create Database and User

```bash
# Connect to MySQL as root
mysql -u root -p

# Execute these commands:
CREATE DATABASE IF NOT EXISTS nigerian_hospital_registry CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'nhms_user'@'localhost' IDENTIFIED BY 'strong_password_here';

GRANT ALL PRIVILEGES ON nigerian_hospital_registry.* TO 'nhms_user'@'localhost';

FLUSH PRIVILEGES;

EXIT;
```

### 3.2 Initialize Database Schema

```bash
mysql -u nhms_user -p nigerian_hospital_registry < /var/www/nhms/NHMS/setup.sql
```

### 3.3 Verify Tables

```bash
mysql -u nhms_user -p nigerian_hospital_registry
SHOW TABLES;
DESC hospitals;
EXIT;
```

## Step 4: Configure Environment

### 4.1 Create .env File

```bash
sudo -u nhms cat > /var/www/nhms/.env << EOF
DB_HOST=localhost
DB_PORT=3306
DB_NAME=nigerian_hospital_registry
DB_USER=nhms_user
DB_PASS=strong_password_here
APP_ENV=production
SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_USER=your-email@example.com
SMTP_PASS=your-email-password
FROM_EMAIL=noreply@yourdomain.com
EOF

# Secure the .env file
sudo chmod 600 /var/www/nhms/.env
sudo chown nhms:www-data /var/www/nhms/.env
```

### 4.2 Create config.php

The `NHMS/includes/config.php` file includes configuration. Update it based on your environment.

## Step 5: Configure Web Server

### 5.1 Apache Configuration

```bash
# Create virtual host configuration
sudo cat > /etc/apache2/sites-available/nhms.conf << 'EOF'
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/nhms

    # Redirect HTTP to HTTPS
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    <Directory /var/www/nhms>
        AllowOverride All
        Require all granted
    </Directory>

    # Logs
    ErrorLog /var/log/apache2/nhms-error.log
    CustomLog /var/log/apache2/nhms-access.log combined
</VirtualHost>

<VirtualHost *:443>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/nhms

    SSLEngine on
    SSLCertificateFile /path/to/your/certificate.crt
    SSLCertificateKeyFile /path/to/your/private.key
    SSLCertificateChainFile /path/to/your/chain.crt

    <Directory /var/www/nhms>
        AllowOverride All
        Require all granted
    </Directory>

    # Security Headers
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"

    # Logs
    ErrorLog /var/log/apache2/nhms-error.log
    CustomLog /var/log/apache2/nhms-access.log combined
</VirtualHost>
EOF

# Enable the site
sudo a2ensite nhms.conf
sudo a2enmod ssl
sudo a2enmod rewrite
sudo a2enmod headers

# Test configuration
sudo apache2ctl configtest

# Restart Apache
sudo systemctl restart apache2
```

### 5.2 Nginx Configuration (Alternative)

```bash
sudo cat > /etc/nginx/sites-available/nhms << 'EOF'
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;

    root /var/www/nhms;
    index index.php;

    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;

    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    add_header X-Content-Type-Options "nosniff";
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOF

# Enable the site
sudo ln -s /etc/nginx/sites-available/nhms /etc/nginx/sites-enabled/
sudo systemctl restart nginx
```

## Step 6: Configure HTTPS/SSL

### 6.1 Using Let's Encrypt (Recommended)

```bash
# Install Certbot
sudo apt-get install -y certbot python3-certbot-apache

# Obtain certificate
sudo certbot certonly --apache -d yourdomain.com -d www.yourdomain.com

# Auto-renewal (already configured by certbot)
sudo systemctl enable certbot.timer
```

## Step 7: Database Backup Strategy

### 7.1 Create Backup Script

```bash
sudo cat > /usr/local/bin/nhms-backup.sh << 'EOF'
#!/bin/bash
BACKUP_DIR="/var/backups/nhms"
DATE=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="$BACKUP_DIR/nhms_backup_$DATE.sql.gz"

mkdir -p $BACKUP_DIR

mysqldump -u nhms_user -p"$DB_PASS" nigerian_hospital_registry | gzip > $BACKUP_FILE

# Keep only last 30 days of backups
find $BACKUP_DIR -type f -mtime +30 -delete

echo "Backup completed: $BACKUP_FILE"
EOF

sudo chmod +x /usr/local/bin/nhms-backup.sh
```

### 7.2 Schedule Daily Backups

```bash
# Add to crontab
sudo crontab -e

# Add this line (runs at 2 AM daily)
0 2 * * * /usr/local/bin/nhms-backup.sh >> /var/log/nhms-backup.log 2>&1
```

## Step 8: Logging and Monitoring

### 8.1 Configure Application Logging

```bash
# Create logs directory
sudo mkdir -p /var/log/nhms
sudo chown www-data:www-data /var/log/nhms
sudo chmod 755 /var/log/nhms
```

### 8.2 Monitor Log Files

```bash
# View PHP errors
tail -f /var/log/nhms/php-error.log

# View Apache errors
tail -f /var/log/apache2/nhms-error.log

# View Apache access logs
tail -f /var/log/apache2/nhms-access.log
```

### 8.3 Set Up Log Rotation

```bash
sudo cat > /etc/logrotate.d/nhms << 'EOF'
/var/log/nhms/*.log {
    daily
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
    postrotate
        /bin/kill -SIGUSR1 $(cat /var/run/syslogd.pid 2>/dev/null) 2>/dev/null || true
    endscript
}
EOF
```

## Step 9: Security Hardening

### 9.1 PHP Configuration

```bash
# Edit php.ini
sudo nano /etc/php/7.4/apache2/php.ini

# Recommended settings:
display_errors = Off
log_errors = On
error_log = /var/log/nhms/php-error.log
session.cookie_secure = On
session.cookie_httponly = On
session.cookie_samesite = Strict
upload_tmp_dir = /var/tmp
max_upload_filesize = 5M
post_max_size = 5M
```

### 9.2 MySQL Security

```bash
# Run MySQL security script
sudo mysql_secure_installation

# Change default admin password immediately
mysql -u root -p

ALTER USER 'admin'@'%' IDENTIFIED BY 'strong_password_here';
FLUSH PRIVILEGES;
```

### 9.3 File Permissions

```bash
# Prevent direct file access
sudo find /var/www/nhms -type f -name "*.php" ! -path "*/NHMS/index.php" ! -path "*/NHMS/register.php" -exec chmod 640 {} \;

# Protect sensitive files
sudo chmod 600 /var/www/nhms/.env
sudo chmod 600 /var/www/nhms/NHMS/setup.sql
```

## Step 10: Post-Deployment Verification

### 10.1 Test Application

```bash
# Test login page
curl -v https://yourdomain.com/NHMS/

# Check PHP info
php -v

# Verify MySQL connection
mysql -u nhms_user -p -e "SELECT VERSION();"
```

### 10.2 Verify Security

```bash
# Check HTTPS
curl -I https://yourdomain.com/NHMS/

# Verify security headers
curl -I https://yourdomain.com/NHMS/ | grep -i "X-Frame-Options\|X-Content-Type\|Strict-Transport"

# Test SSL configuration
sudo ssl-test yourdomain.com
```

### 10.3 Change Default Credentials

1. Log in with admin account
2. Navigate to admin settings
3. Change default admin password to a strong password
4. Log out and test with new credentials

## Troubleshooting

### Issue: Database Connection Failed
```bash
# Check MySQL is running
sudo systemctl status mysql

# Verify credentials
mysql -u nhms_user -p -h localhost

# Check firewall
sudo ufw status
```

### Issue: Permission Denied Errors
```bash
# Fix file permissions
sudo chown -R www-data:www-data /var/www/nhms
find /var/www/nhms -type d -exec chmod 755 {} \;
find /var/www/nhms -type f -exec chmod 644 {} \;
```

### Issue: SSL Certificate Error
```bash
# Renew certificate
sudo certbot renew --force-renewal

# Check certificate validity
sudo openssl x509 -in /etc/letsencrypt/live/yourdomain.com/cert.pem -text -noout
```

## Performance Optimization

### 1. Database Optimization

```sql
-- Add indexes for faster queries
ALTER TABLE birth_certificates ADD INDEX idx_hospital_date (hospital_id, date_of_birth);
ALTER TABLE death_certificates ADD INDEX idx_hospital_date (hospital_id, date_of_death);
```

### 2. Caching

Consider implementing PHP opcode caching:
```bash
sudo apt-get install php-opcache
```

### 3. Compression

Enable gzip compression in Apache:
```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>
```

## Maintenance

### Regular Tasks

- **Daily**: Monitor log files for errors
- **Weekly**: Verify backups are completing
- **Monthly**: Review security logs, update packages
- **Quarterly**: Test disaster recovery procedures

### Update Procedure

```bash
# Pull latest code
cd /var/www/nhms
sudo -u nhms git pull origin main

# Clear any caches
sudo systemctl restart apache2

# Verify application is working
curl -I https://yourdomain.com/NHMS/
```

## Support

For deployment issues or questions, contact the development team.
