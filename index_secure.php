<?php

// Database configuration, names omitted for security
$servername = ""; // Server name
$username = ""; // Username
$password = ""; // Password
$database = ""; // Specify your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Validate and sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate required fields
    $required_fields = ['name', 'age', 'gender', 'description', 'phone', 'email'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        die("Error: Please fill in all required fields: " . implode(', ', $missing_fields));
    }
    
    // Sanitize and validate inputs
    $name = sanitize_input($_POST['name']);
    $age = filter_var($_POST['age'], FILTER_VALIDATE_INT);
    $gender = sanitize_input($_POST['gender']);
    $description = sanitize_input($_POST['description']);
    $phone = sanitize_input($_POST['phone']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    
    // Validate specific fields
    if ($age === false || $age < 1 || $age > 120) {
        die("Error: Please enter a valid age between 1 and 120");
    }
    
    if ($email === false) {
        die("Error: Please enter a valid email address");
    }
    
    // Use prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($conn, "INSERT INTO fullform (Name, Age, Gender, Description, Phone, Email, Date) VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
    
    if ($stmt === false) {
        die("Error preparing statement: " . mysqli_error($conn));
    }
    
    // Bind parameters (s = string, i = integer)
    mysqli_stmt_bind_param($stmt, "sissss", $name, $age, $gender, $description, $phone, $email);
    
    // Execute the statement
    if (mysqli_stmt_execute($stmt)) {
        echo "Form submitted successfully!";
    } else {
        echo "Error: " . mysqli_stmt_error($stmt);
    }
    
    // Close the statement
    mysqli_stmt_close($stmt);
}

// Close connection
mysqli_close($conn);

?>
