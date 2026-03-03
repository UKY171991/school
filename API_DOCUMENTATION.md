# School Management System - API Documentation

## Overview
This API allows schools to access their data using their custom domain names. Each school can retrieve information about students, teachers, grades, attendance, and more.

## Base URL
```
https://school.thewebbrain.in/api
```

## Authentication
The API uses domain-based authentication. You need to provide your school's domain name in one of the following ways:

### Method 1: Header (Recommended)
```
X-School-Domain: school.developer.space
```

### Method 2: Query Parameter
```
?domain=school.developer.space
```

### Method 3: Host-based (Automatic)
If you're accessing from your custom domain, it will be detected automatically.

---

## API Endpoints

### 1. School Information

#### Get School Info
```http
GET /api/public-api/info
```

**Headers:**
```
X-School-Domain: school.developer.space
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 2,
    "name": "Testing School",
    "address": "Jaunpur Rd",
    "phone": "09453619260",
    "email": "umakant171991@gmail.com",
    "domain_name": "school.developer.space",
    "logo_url": "https://school.thewebbrain.in/storage/schools/logos/..."
  }
}
```

---

### 2. Statistics

#### Get School Statistics
```http
GET /api/public-api/statistics
```

**Headers:**
```
X-School-Domain: school.developer.space
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total_students": 150,
    "total_teachers": 25,
    "total_grades": 10,
    "total_sections": 15,
    "total_exams": 8,
    "today_attendance": 145,
    "present_today": 138,
    "total_fees_collected": 450000
  }
}
```

---

### 3. Students

#### Get All Students
```http
GET /api/public-api/students
```

**Query Parameters:**
- `per_page` (optional): Number of results per page (default: 15)
- `page` (optional): Page number

**Headers:**
```
X-School-Domain: school.developer.space
```

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "roll_number": "1001",
        "dob": "2010-05-15",
        "grade": {
          "id": 1,
          "name": "Class 10"
        },
        "section": {
          "id": 1,
          "name": "A"
        },
        "branch": {
          "id": 1,
          "name": "Main Campus"
        }
      }
    ],
    "per_page": 15,
    "total": 150
  }
}
```

#### Get Single Student
```http
GET /api/public-api/students/{id_or_roll_number}
```

**Example:**
```
GET /api/public-api/students/1001
GET /api/public-api/students/1
```

**Headers:**
```
X-School-Domain: school.developer.space
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "roll_number": "1001",
    "dob": "2010-05-15",
    "father_name": "Mr. Doe",
    "mother_name": "Mrs. Doe",
    "photo_url": "https://...",
    "grade": {
      "id": 1,
      "name": "Class 10"
    },
    "section": {
      "id": 1,
      "name": "A"
    }
  }
}
```

---

### 4. Teachers

#### Get All Teachers
```http
GET /api/public-api/teachers
```

**Query Parameters:**
- `per_page` (optional): Number of results per page (default: 15)

**Headers:**
```
X-School-Domain: school.developer.space
```

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Jane Smith",
        "email": "jane@example.com",
        "phone": "1234567890",
        "specialization": "Mathematics",
        "photo_url": "https://...",
        "branch": {
          "id": 1,
          "name": "Main Campus"
        }
      }
    ],
    "per_page": 15,
    "total": 25
  }
}
```

---

### 5. Grades/Classes

#### Get All Grades
```http
GET /api/public-api/grades
```

**Headers:**
```
X-School-Domain: school.developer.space
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Class 10",
      "students_count": 45,
      "sections": [
        {
          "id": 1,
          "name": "A"
        },
        {
          "id": 2,
          "name": "B"
        }
      ],
      "teacher": {
        "id": 1,
        "name": "Jane Smith"
      }
    }
  ]
}
```

---

### 6. Sections

#### Get All Sections
```http
GET /api/public-api/sections
```

**Headers:**
```
X-School-Domain: school.developer.space
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "A",
      "grade": {
        "id": 1,
        "name": "Class 10"
      },
      "branch": {
        "id": 1,
        "name": "Main Campus"
      }
    }
  ]
}
```

---

### 7. Exams

#### Get All Exams
```http
GET /api/public-api/exams
```

**Query Parameters:**
- `per_page` (optional): Number of results per page (default: 15)

**Headers:**
```
X-School-Domain: school.developer.space
```

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Mid Term Exam",
        "date": "2026-03-15",
        "type": "Mid Term",
        "grade_id": 1
      }
    ],
    "per_page": 15,
    "total": 8
  }
}
```

---

### 8. Attendance

#### Get Attendance Records
```http
GET /api/public-api/attendance
```

**Query Parameters:**
- `date` (optional): Filter by date (YYYY-MM-DD)
- `student_id` (optional): Filter by student ID
- `per_page` (optional): Number of results per page (default: 15)

**Headers:**
```
X-School-Domain: school.developer.space
```

**Example:**
```
GET /api/public-api/attendance?date=2026-02-27
GET /api/public-api/attendance?student_id=1
```

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "date": "2026-02-27",
        "status": "present",
        "remarks": null,
        "student": {
          "id": 1,
          "name": "John Doe",
          "roll_number": "1001"
        }
      }
    ],
    "per_page": 15,
    "total": 145
  }
}
```

---

### 9. Fee Payments

#### Get Fee Payments
```http
GET /api/public-api/fee-payments
```

**Query Parameters:**
- `student_id` (optional): Filter by student ID
- `status` (optional): Filter by status (pending, paid, etc.)
- `per_page` (optional): Number of results per page (default: 15)

**Headers:**
```
X-School-Domain: school.developer.space
```

**Example:**
```
GET /api/public-api/fee-payments?student_id=1
GET /api/public-api/fee-payments?status=pending
```

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "amount_paid": 5000,
        "payment_date": "2026-02-15",
        "status": "paid",
        "student": {
          "id": 1,
          "name": "John Doe",
          "roll_number": "1001"
        }
      }
    ],
    "per_page": 15,
    "total": 120
  }
}
```

---

### 10. Branches

#### Get All Branches
```http
GET /api/public-api/branches
```

**Headers:**
```
X-School-Domain: school.developer.space
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Main Campus",
      "code": "MC",
      "address": "123 Main Street",
      "phone": "1234567890",
      "email": "main@school.com",
      "is_main": true,
      "is_active": true,
      "students_count": 100,
      "teachers_count": 15
    }
  ]
}
```

---

## Error Responses

### 404 - School Not Found
```json
{
  "error": "School not found"
}
```

### 403 - Invalid Domain
```json
{
  "error": "Invalid school domain",
  "message": "The provided domain is not registered in the system"
}
```

### 404 - Resource Not Found
```json
{
  "error": "Student not found"
}
```

---

## Usage Examples

### cURL Example
```bash
curl -X GET "https://school.thewebbrain.in/api/public-api/info" \
  -H "X-School-Domain: school.developer.space"
```

### JavaScript (Fetch) Example
```javascript
fetch('https://school.thewebbrain.in/api/public-api/students', {
  headers: {
    'X-School-Domain': 'school.developer.space'
  }
})
.then(response => response.json())
.then(data => console.log(data));
```

### PHP Example
```php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://school.thewebbrain.in/api/public-api/info');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-School-Domain: school.developer.space'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);
```

### Python Example
```python
import requests

headers = {
    'X-School-Domain': 'school.developer.space'
}

response = requests.get(
    'https://school.thewebbrain.in/api/public-api/students',
    headers=headers
)

data = response.json()
print(data)
```

---

## Rate Limiting
Currently, there are no rate limits. However, please use the API responsibly.

## Support
For API support, contact: support@thewebbrain.in

## Changelog

### Version 1.0 (February 2026)
- Initial API release
- Domain-based authentication
- Student, Teacher, Grade, Section endpoints
- Attendance and Fee Payment endpoints
- Statistics endpoint
- Branch management endpoints
