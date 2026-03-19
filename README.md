# SQL Injection Prevention Project

## Overview
This project demonstrates SQL injection vulnerabilities and secure implementation using PHP prepared statements.

## Files
- `index.php` - **Vulnerable version** with SQL injection risks
- `index_secure.php` - **Secure version** with prepared statements and input validation
- `project.html` - HTML form for submitting data
- `test_sql_injection.php` - Test suite demonstrating security differences
- `style.css` - Styling for the form and test interface

## Security Improvements in Secure Version

### SQL Injection Prevention
- **Prepared statements** with parameterized queries
- **Input sanitization** using `htmlspecialchars()` and `trim()`
- **Type validation** for age (integer) and email (email format)
- **Required field validation**

### Key Security Features
- Separates SQL logic from data using `mysqli_prepare()` and `mysqli_stmt_bind_param()`
- Validates input ranges (age 1-120)
- Sanitizes all user inputs to prevent XSS
- Proper error handling without exposing sensitive information

## Testing
Run the test suite to see security differences:
```
http://localhost/PHP/project/test_sql_injection.php
```

The test demonstrates:
- SQL DROP TABLE attacks
- OR-based bypass attempts
- UNION SELECT attacks
- XSS prevention
- Input validation

## Usage
1. Use `project.html` form to submit data
2. Form submits to `index_secure.php` (secure version)
3. Compare with vulnerable `index.php` to understand risks

## Database Setup (XAMPP)
- **XAMPP required**: Ensure Apache and MySQL are running in XAMPP Control Panel
- **Database name**: Update `$database` variable in `index_secure.php` 
- **Table name**: `fullform` with columns: Name, Age, Gender, Description, Phone, Email, Date
- **Default credentials**: Typically `localhost`, `root`, empty password for XAMPP MySQL

### Configuration Steps
1. Start XAMPP Control Panel
2. Start Apache and MySQL services
3. Update database credentials in both PHP files
4. Create database and table using phpMyAdmin or MySQL console
