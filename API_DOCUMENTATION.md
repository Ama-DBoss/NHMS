# NHMS API Documentation

## Overview

This document provides technical API documentation for the National Birth & Death Management System (NHMS). The API is session-based and accessed through standard HTTP POST/GET requests.

## Authentication

### Session-Based Authentication

All API endpoints require an active session. Users must authenticate through the login endpoint first.

**Login Endpoint:**
```
POST /NHMS/index.php
```

**Parameters:**
- `email` (string, required): User email address
- `password` (string, required): User password
- `user_type` (string, required): Either "hospital" or "admin"
- `csrf_token` (string, required): CSRF token from form

**Response (Success):**
- HTTP 302 redirect to dashboard
- Session ID set in cookie

**Response (Failure):**
- HTTP 200 with error message displayed

### Session Validation

All protected endpoints check for:
```php
if (!is_hospital()) {
    redirect('index.php');
}
```

## Endpoints

### Authentication Endpoints

#### Login
```
POST /NHMS/index.php
```

**Description:** Authenticates a user and creates a session

**Parameters:**
```json
{
  "email": "hospital@example.com",
  "password": "secure_password",
  "user_type": "hospital",
  "csrf_token": "token_from_form"
}
```

**Success Response (302):**
- Redirects to `/NHMS/dashboard.php` for hospitals
- Redirects to `/NHMS/admin/dashboard.php` for admins

**Error Response (200):**
```html
<div class="alert alert-danger">Invalid email or password</div>
```

#### Register Hospital
```
POST /NHMS/register.php
```

**Description:** Register a new hospital in the system

**Parameters:**
```json
{
  "name": "General Hospital",
  "address": "123 Main Street",
  "state": "Lagos",
  "email": "info@hospital.com",
  "password": "securePassword123",
  "confirm_password": "securePassword123",
  "csrf_token": "token_from_form"
}
```

**Validations:**
- Email: Must be valid email format
- Password: Minimum 8 characters
- All fields: Required
- Email: Must be unique

**Success Response (302):**
- Redirects to `/NHMS/index.php`
- Sets flash message: "Registration successful! You can now log in."

**Error Response (200):**
```html
<div class="alert alert-danger">
  <ul>
    <li>Email already registered</li>
    <li>Password must be at least 8 characters</li>
  </ul>
</div>
```

#### Logout
```
GET /NHMS/logout.php
```

**Description:** Destroys session and logs out user

**Response (302):**
- Redirects to `/NHMS/index.php`

---

### Hospital Dashboard Endpoints

#### Get Hospital Dashboard
```
GET /NHMS/dashboard.php
```

**Description:** Retrieve dashboard with certificate counts and recent records

**Required:** Hospital session authentication

**Response Data:**
```json
{
  "hospital_name": "General Hospital",
  "birth_count": 45,
  "death_count": 12,
  "recent_births": [
    {
      "id": 1,
      "child_name": "John Doe",
      "date_of_birth": "2026-01-15",
      "certificate_number": "BIRTH-abc123-202601"
    }
  ],
  "recent_deaths": [
    {
      "id": 1,
      "deceased_name": "Jane Smith",
      "date_of_death": "2026-01-10",
      "certificate_number": "DEATH-def456-202601"
    }
  ]
}
```

---

### Certificate Generation Endpoints

#### Generate Birth Certificate
```
POST /NHMS/generate_certificate.php?type=birth
```

**Description:** Create a new birth certificate record

**Required:** Hospital session authentication

**Parameters:**
```json
{
  "child_name": "Baby John",
  "date_of_birth": "2026-01-20",
  "time_of_birth": "14:30",
  "place_of_birth": "General Hospital",
  "gender": "Male",
  "weight": "3.5",
  "blood_group": "O+",
  "genotype": "AA",
  "father_name": "John Doe",
  "mother_name": "Jane Doe",
  "parents_address": "123 Main Street, Lagos",
  "csrf_token": "token_from_form"
}
```

**Validations:**
- All fields required
- Weight: Numeric value
- Gender: "Male" or "Female"
- Blood Group: Valid blood type
- Genotype: AA, AS, SS, or AC

**Success Response (302):**
- Redirects to `/NHMS/dashboard.php`
- Flash message: "Birth certificate generated successfully."
- Certificate ID assigned in database

**Error Response (200):**
```html
<div class="alert alert-danger">All fields are required.</div>
```

**Generated Certificate Number Format:**
```
BIRTH-[RANDOM_HEX_16]-[YYYYMM]
```

#### Generate Death Certificate
```
POST /NHMS/generate_certificate.php?type=death
```

**Description:** Create a new death certificate record

**Required:** Hospital session authentication

**Parameters:**
```json
{
  "deceased_name": "John Smith",
  "date_of_death": "2026-01-15",
  "time_of_death": "09:45",
  "place_of_death": "General Hospital",
  "cause_of_death": "Pneumonia",
  "age_at_death": "65",
  "gender": "Male",
  "occupation": "Teacher",
  "marital_status": "Married",
  "next_of_kin": "Jane Smith",
  "next_of_kin_relationship": "Daughter",
  "csrf_token": "token_from_form"
}
```

**Validations:**
- All fields required
- Age: Numeric value
- Gender: "Male" or "Female"
- Marital Status: Single, Married, Divorced, or Widowed

**Success Response (302):**
- Redirects to `/NHMS/dashboard.php`
- Flash message: "Death certificate generated successfully."

**Error Response (200):**
- Displays validation errors

---

### Certificate View/Download Endpoints

#### View Birth Records
```
GET /NHMS/view_births.php
```

**Description:** List all birth certificates for hospital

**Required:** Hospital session authentication

**Response Data:**
```json
[
  {
    "id": 1,
    "child_name": "Baby John",
    "date_of_birth": "2026-01-20",
    "certificate_number": "BIRTH-abc123-202601",
    "father_name": "John Doe",
    "mother_name": "Jane Doe"
  }
]
```

#### View Death Records
```
GET /NHMS/view_deaths.php
```

**Description:** List all death certificates for hospital

**Required:** Hospital session authentication

**Response Data:**
```json
[
  {
    "id": 1,
    "deceased_name": "John Smith",
    "date_of_death": "2026-01-15",
    "certificate_number": "DEATH-def456-202601",
    "cause_of_death": "Pneumonia",
    "age_at_death": 65
  }
]
```

#### Download Certificate
```
GET /NHMS/download_certificate.php?type=birth&id=1
```

**Description:** Generate and download certificate as PDF

**Parameters:**
- `type` (string, required): "birth" or "death"
- `id` (integer, required): Certificate ID

**Required:** Hospital or Admin session authentication

**Response:**
- Content-Type: application/pdf
- File download with certificate number in filename

**Example Filename:** `birth_cert_BIRTH-abc123-202601.pdf`

**Error Responses:**
- 400: Invalid certificate ID or type
- 401: Unauthorized access
- 404: Certificate not found
- 500: PDF generation error

---

### Admin Dashboard Endpoints

#### Admin Dashboard
```
GET /NHMS/admin/dashboard.php
```

**Description:** System-wide statistics and recent activity

**Required:** Admin session authentication

**Response Data:**
```json
{
  "hospital_count": 25,
  "birth_count": 450,
  "death_count": 120,
  "recent_hospitals": [
    {
      "id": 1,
      "hospital_id": "HOSP_ABC1_1234567890",
      "name": "General Hospital",
      "state": "Lagos",
      "registration_date": "2026-01-01"
    }
  ]
}
```

#### View All Hospitals
```
GET /NHMS/admin/view_hospitals.php?page=1
```

**Description:** List all registered hospitals with pagination

**Required:** Admin session authentication

**Parameters:**
- `page` (integer, optional): Page number (default: 1)

**Response Data:**
```json
{
  "hospitals": [
    {
      "id": 1,
      "hospital_id": "HOSP_ABC1_1234567890",
      "name": "General Hospital",
      "address": "123 Main Street",
      "state": "Lagos",
      "email": "info@hospital.com",
      "registration_date": "2026-01-01"
    }
  ],
  "pagination": {
    "current_page": 1,
    "total_pages": 3,
    "records_per_page": 10
  }
}
```

#### View All Birth Records
```
GET /NHMS/admin/view_births.php?page=1
```

**Description:** List all birth certificates system-wide

**Required:** Admin session authentication

**Parameters:**
- `page` (integer, optional): Page number (default: 1)

**Response Data:**
```json
[
  {
    "id": 1,
    "hospital_id": 1,
    "child_name": "Baby John",
    "date_of_birth": "2026-01-20",
    "certificate_number": "BIRTH-abc123-202601",
    "father_name": "John Doe",
    "mother_name": "Jane Doe",
    "issue_date": "2026-01-21"
  }
]
```

#### View All Death Records
```
GET /NHMS/admin/view_deaths.php?page=1
```

**Description:** List all death certificates system-wide

**Required:** Admin session authentication

**Response Data:**
```json
[
  {
    "id": 1,
    "hospital_id": 1,
    "deceased_name": "John Smith",
    "date_of_death": "2026-01-15",
    "certificate_number": "DEATH-def456-202601",
    "cause_of_death": "Pneumonia",
    "age_at_death": 65,
    "issue_date": "2026-01-16"
  }
]
```

---

## Error Handling

### HTTP Status Codes

| Code | Meaning | Example |
|------|---------|---------|
| 200 | OK | Request successful |
| 302 | Found | Redirect (successful form submission) |
| 400 | Bad Request | Invalid parameters |
| 401 | Unauthorized | Not authenticated |
| 404 | Not Found | Resource doesn't exist |
| 500 | Server Error | Database connection failed |

### Error Response Format

Most errors are returned as HTML alerts:
```html
<div class="alert alert-danger" role="alert">
  Error message here
</div>
```

---

## Rate Limiting

The system implements basic rate limiting:
- **Max Login Attempts:** 5 attempts per 15 minutes
- **Session Timeout:** 1 hour of inactivity

---

## Data Formats

### Date Format
```
YYYY-MM-DD (e.g., 2026-01-20)
```

### Time Format
```
HH:MM (24-hour format, e.g., 14:30)
```

### Certificate Number Format
```
[TYPE]-[RANDOM_HEX_16]-[YYYYMM]
E.g., BIRTH-a1b2c3d4e5f6g7h8-202601
```

---

## Database Relations

### Hospital to Certificates
```
One Hospital → Many Birth Certificates
One Hospital → Many Death Certificates
```

### Foreign Key Constraints
- `birth_certificates.hospital_id` → `hospitals.id`
- `death_certificates.hospital_id` → `hospitals.id`

---

## Examples

### Example: Register Hospital and Get Dashboard

```bash
# 1. Register
curl -X POST http://localhost/NHMS/register.php \
  -d "name=Test Hospital" \
  -d "address=123 Main St" \
  -d "state=Lagos" \
  -d "email=test@hospital.com" \
  -d "password=SecurePass123" \
  -d "confirm_password=SecurePass123"

# 2. Login
curl -X POST http://localhost/NHMS/index.php \
  -c cookies.txt \
  -d "email=test@hospital.com" \
  -d "password=SecurePass123" \
  -d "user_type=hospital"

# 3. Access Dashboard
curl -b cookies.txt http://localhost/NHMS/dashboard.php
```

### Example: Generate Birth Certificate

```bash
curl -X POST "http://localhost/NHMS/generate_certificate.php?type=birth" \
  -b cookies.txt \
  -d "child_name=Baby John" \
  -d "date_of_birth=2026-01-20" \
  -d "time_of_birth=14:30" \
  -d "place_of_birth=Hospital" \
  -d "gender=Male" \
  -d "weight=3.5" \
  -d "blood_group=O+" \
  -d "genotype=AA" \
  -d "father_name=John Doe" \
  -d "mother_name=Jane Doe" \
  -d "parents_address=123 Main St"
```

---

## Support

For API questions or issues, contact the development team.

---

**Last Updated:** 2026
**API Version:** 1.0
