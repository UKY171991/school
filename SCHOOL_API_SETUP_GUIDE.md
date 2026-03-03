# School API Setup Guide

## What Has Been Created

A complete REST API system that allows schools to access their data through their custom domain names.

## Files Created

### 1. **API Controller**
- `app/Http/Controllers/Api/SchoolApiController.php`
- Handles all API requests for school data

### 2. **Middleware**
- `app/Http/Middleware/ApiDomainMiddleware.php`
- Validates school domain and ensures security

### 3. **Routes**
- `routes/api.php` - Updated with all API endpoints

### 4. **Documentation**
- `API_DOCUMENTATION.md` - Complete API documentation

---

## Available API Endpoints

### Base URL
```
https://school.thewebbrain.in/api/public-api
```

### Endpoints List

1. **GET /info** - Get school information
2. **GET /statistics** - Get school statistics
3. **GET /branches** - Get all branches
4. **GET /students** - Get all students (paginated)
5. **GET /students/{id}** - Get single student
6. **GET /teachers** - Get all teachers
7. **GET /grades** - Get all classes/grades
8. **GET /sections** - Get all sections
9. **GET /exams** - Get all exams
10. **GET /attendance** - Get attendance records
11. **GET /fee-payments** - Get fee payment records

---

## How to Use

### Step 1: Add Domain to School
1. Go to Schools Management
2. Edit a school
3. Add domain name (e.g., `school.developer.space`)
4. Save

### Step 2: Make API Request

#### Using cURL:
```bash
curl -X GET "https://school.thewebbrain.in/api/public-api/info" \
  -H "X-School-Domain: school.developer.space"
```

#### Using JavaScript:
```javascript
fetch('https://school.thewebbrain.in/api/public-api/students', {
  headers: {
    'X-School-Domain': 'school.developer.space'
  }
})
.then(response => response.json())
.then(data => console.log(data));
```

#### Using Postman:
1. Set URL: `https://school.thewebbrain.in/api/public-api/info`
2. Add Header: `X-School-Domain: school.developer.space`
3. Send GET request

---

## Authentication Methods

The API supports 3 ways to provide the school domain:

### Method 1: Header (Recommended)
```
X-School-Domain: school.developer.space
```

### Method 2: Query Parameter
```
?domain=school.developer.space
```

### Method 3: Automatic (Host-based)
If accessing from the custom domain directly, it's detected automatically.

---

## Example Responses

### Get School Info
```json
{
  "success": true,
  "data": {
    "id": 2,
    "name": "Testing School",
    "address": "Jaunpur Rd",
    "phone": "09453619260",
    "email": "umakant171991@gmail.com",
    "domain_name": "school.developer.space"
  }
}
```

### Get Students
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "John Doe",
        "roll_number": "1001",
        "grade": {
          "name": "Class 10"
        }
      }
    ],
    "total": 150
  }
}
```

### Get Statistics
```json
{
  "success": true,
  "data": {
    "total_students": 150,
    "total_teachers": 25,
    "total_grades": 10,
    "today_attendance": 145,
    "present_today": 138
  }
}
```

---

## Testing the API

### Test with your school domain:

1. **Get School Info:**
```
https://school.thewebbrain.in/api/public-api/info?domain=school.developer.space
```

2. **Get Students:**
```
https://school.thewebbrain.in/api/public-api/students?domain=school.developer.space
```

3. **Get Statistics:**
```
https://school.thewebbrain.in/api/public-api/statistics?domain=school.developer.space
```

---

## Security Features

1. **Domain Validation**: Only registered domains can access the API
2. **School Isolation**: Each school can only access their own data
3. **No Cross-School Access**: Schools cannot see other schools' data
4. **Automatic Filtering**: All queries are automatically filtered by school_id

---

## Integration Examples

### Website Integration
```html
<script>
async function getSchoolInfo() {
  const response = await fetch('https://school.thewebbrain.in/api/public-api/info', {
    headers: {
      'X-School-Domain': 'school.developer.space'
    }
  });
  const data = await response.json();
  document.getElementById('school-name').textContent = data.data.name;
}
</script>
```

### Mobile App Integration
```javascript
// React Native / Expo
const API_BASE = 'https://school.thewebbrain.in/api/public-api';
const SCHOOL_DOMAIN = 'school.developer.space';

const fetchStudents = async () => {
  const response = await fetch(`${API_BASE}/students`, {
    headers: {
      'X-School-Domain': SCHOOL_DOMAIN
    }
  });
  return await response.json();
};
```

---

## Next Steps

1. ✅ API is ready to use
2. ✅ All endpoints are functional
3. ✅ Domain-based authentication is working
4. ✅ Documentation is complete

### To Start Using:
1. Add domain name to your school in the system
2. Use the API endpoints with your domain
3. Integrate into your website/app
4. Refer to `API_DOCUMENTATION.md` for detailed info

---

## Support

For questions or issues:
- Check `API_DOCUMENTATION.md` for detailed documentation
- Test endpoints using Postman or cURL
- Contact: support@thewebbrain.in

---

## Features

✅ Domain-based authentication
✅ Complete CRUD operations
✅ Pagination support
✅ Filtering capabilities
✅ Relationship loading
✅ Error handling
✅ Security measures
✅ Comprehensive documentation
✅ Multiple authentication methods
✅ Easy integration

---

**API Version:** 1.0
**Last Updated:** February 27, 2026
