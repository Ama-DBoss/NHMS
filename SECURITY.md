# Security Policy and Implementation Guide

## Security Overview

The National Birth & Death Management System (NHMS) implements comprehensive security measures to protect sensitive vital records data. This document outlines the security features and best practices.

## Security Features

### 1. Authentication & Authorization

**Login Security:**
- Secure password hashing using bcrypt with cost factor 12
- CSRF token validation on all forms
- Session regeneration after successful login
- Failed login attempt tracking
- Account lockout after 5 failed attempts (15-minute cooldown)

**Role-Based Access Control:**
- Hospital users: Can only access their own records
- Admin users: Can access all records system-wide
- Automatic permission checking on protected pages

**Example Code:**
```php
if (!is_hospital()) {
    redirect('index.php');
}
```

### 2. Data Protection

**SQL Injection Prevention:**
- All database queries use prepared statements
- Parameter binding prevents SQL injection attacks
- Type casting for numeric parameters

**Example:**
```php
$stmt = $pdo->prepare("SELECT * FROM hospitals WHERE email = ? AND id = ?");
$stmt->execute([$email, $id]);
```

**Input Validation & Sanitization:**
- All user inputs are validated before processing
- HTML special characters are escaped
- String trimming and type checking
- Maximum length enforcement on all fields

**Example:**
```php
$name = sanitize_input($_POST['name']); // Removes tags, escapes HTML
if (strlen($name) > 255) {
    $error = 'Name exceeds maximum length';
}
```

### 3. Session Security

**HTTP-Only Cookies:**
```php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => !empty($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);
```

**Session Features:**
- Session timeout after 1 hour of inactivity
- Session ID regeneration on login
- Secure cookie transmission over HTTPS only
- SameSite attribute to prevent CSRF

### 4. CSRF Protection

**Token Generation:**
```php
// Generate unique token per session
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
```

**Validation:**
```php
// Verify token matches session token
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die('CSRF token validation failed');
}
```

**Implementation:**
All POST forms include CSRF tokens:
```html
<form method="post">
    <?= csrf_token_field() ?>
    <!-- form fields -->
</form>
```

### 5. HTTP Security Headers

**Security Headers Set:**
```
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: geolocation=(), microphone=(), camera=()
```

**Purpose:**
- Prevent clickjacking attacks
- Block MIME type sniffing
- Prevent XSS attacks
- Limit referrer information
- Restrict browser features

### 6. Database Security

**Connection Security:**
```php
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
];
```

**Features:**
- Parameterized queries
- Exception-based error handling
- No prepared statement emulation
- Character set specification (UTF-8)

**Database User Privileges:**
```sql
-- Create limited database user
CREATE USER 'nhms_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT SELECT, INSERT, UPDATE, DELETE ON nigerian_hospital_registry.* 
TO 'nhms_user'@'localhost';
FLUSH PRIVILEGES;
```

### 7. File Handling Security

**Upload Restrictions:**
- Maximum file size: 5MB
- Only specific file types allowed
- Uploaded files stored outside web root
- Random filename generation on upload

**Configuration:**
```php
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/');
```

### 8. Error Handling

**Development Environment:**
- Errors displayed on screen
- Debug logging enabled
- Full stack traces available

**Production Environment:**
```php
if (APP_ENV !== 'production') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0); // Hide errors from users
}
```

**Error Logging:**
```php
ini_set('error_log', LOG_DIR . 'php-error.log');
error_log('User login failed: ' . $e->getMessage());
```

### 9. Audit Logging

**Activity Tracking:**
```php
function log_activity($hospital_id, $action, $details) {
    // Log all important actions
    INSERT INTO activity_logs (hospital_id, action, details, created_at)
    VALUES (?, ?, ?, NOW())
}
```

**Logged Events:**
- User login/logout
- Certificate generation
- Data modifications
- Admin actions

### 10. Password Security

**Password Requirements:**
- Minimum 8 characters
- Hashed using bcrypt (cost factor 12)
- No maximum length restriction (prevents hash DoS)

**Hashing:**
```php
$password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
```

**Verification:**
```php
if (password_verify($user_password, $hash_from_db)) {
    // Password is correct
}
```

## Security Best Practices

### For Deployment

1. **Use HTTPS Only**
   ```apache
   # Force HTTPS
   RewriteEngine On
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

2. **Set Strong Environment Variables**
   ```bash
   # Strong, unique database password (generate: openssl rand -base64 32)
   DB_PASS=your_strong_password
   ```

3. **Secure File Permissions**
   ```bash
   chmod 600 .env          # Restrict to owner only
   chmod 755 NHMS/         # Standard directory permissions
   chmod 644 NHMS/*.php    # Web-readable files
   ```

4. **Configure PHP Securely**
   ```ini
   display_errors = Off
   log_errors = On
   expose_php = Off
   session.cookie_secure = On
   session.cookie_httponly = On
   ```

5. **Database Backups**
   - Daily automated backups
   - Store backups securely
   - Test restoration regularly

6. **Keep Software Updated**
   - PHP security patches
   - MySQL/MariaDB updates
   - Web server updates
   - PHP libraries updates

### For Development

1. **Never Commit Secrets**
   - Add `.env` to `.gitignore`
   - Never commit database passwords
   - Use environment variables

2. **Security Testing**
   - SQL injection tests
   - XSS vulnerability testing
   - CSRF token validation
   - Authentication bypass attempts

3. **Code Review**
   - Peer review before deployment
   - Security-focused code review
   - Check for common vulnerabilities

4. **Dependency Management**
   - Keep Composer dependencies updated
   - Check for known vulnerabilities: `composer audit`
   - Use composer.lock for consistency

## Vulnerability Reporting

If you discover a security vulnerability in NHMS:

1. **Do NOT** create a public issue
2. **Email** security concerns to: security@yourdomain.com
3. **Include** detailed information about the vulnerability
4. **Wait** for acknowledgment before disclosing publicly

We appreciate responsible disclosure and will work to fix issues promptly.

## Compliance & Standards

The NHMS system implements security controls aligned with:
- OWASP Top 10 Prevention
- CWE/SANS Top 25 Most Dangerous Software Errors
- NIST Cybersecurity Framework
- Data Protection Regulations

## Security Audit Checklist

Use this checklist for security audits:

- [ ] All forms have CSRF tokens
- [ ] All SQL queries use prepared statements
- [ ] User inputs are sanitized
- [ ] Authentication is enforced on protected pages
- [ ] Authorization checks user roles
- [ ] Error messages don't expose sensitive data
- [ ] Passwords are hashed with bcrypt
- [ ] HTTPS is enforced
- [ ] Security headers are present
- [ ] Session timeouts are configured
- [ ] Audit logs are enabled
- [ ] Database backups are tested
- [ ] File uploads are restricted
- [ ] API rate limiting is configured
- [ ] Monitoring and alerting is active

## Incident Response

### If a Security Incident Occurs

1. **Immediate Actions**
   - Isolate affected systems
   - Preserve logs and evidence
   - Notify relevant stakeholders

2. **Investigation**
   - Review audit logs
   - Check for data access
   - Identify root cause

3. **Remediation**
   - Patch vulnerabilities
   - Reset compromised passwords
   - Update access controls

4. **Recovery**
   - Restore from clean backups
   - Monitor for re-infection
   - Verify system integrity

5. **Post-Incident**
   - Document findings
   - Update security measures
   - Communicate with users if needed

## Security Resources

- **OWASP Guide**: https://owasp.org/www-project-top-ten/
- **PHP Security**: https://www.php.net/manual/en/security.php
- **MySQL Security**: https://dev.mysql.com/doc/refman/5.7/en/security.html
- **Web Security Academy**: https://portswigger.net/web-security

## Support

For security-related questions or concerns, contact your security administrator or the development team.

---

**Last Updated:** 2026
**Security Policy Version:** 1.0
