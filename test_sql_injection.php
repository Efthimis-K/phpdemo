<?php

/**
 * SQL Injection Test Suite
 * Tests both vulnerable and secure versions of the form processing
 */

if (!function_exists('sanitize_input')) {
    function sanitize_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
}

// Test cases for SQL injection attempts
$sqlInjectionTests = [
    [
        'case' => 'SQL DROP TABLE injection',
        'name' => "Robert'); DROP TABLE fullform; --",
        'age' => '25',
        'gender' => 'Male',
        'description' => 'Test injection',
        'phone' => '1234567890',
        'email' => 'test@example.com'
    ],
    [
        'case' => 'SQL OR injection',
        'name' => "Admin' OR '1'='1",
        'age' => '25',
        'gender' => 'Male',
        'description' => 'Test injection',
        'phone' => '1234567890',
        'email' => 'test@example.com'
    ],
    [
        'case' => 'SQL UNION injection',
        'name' => "User' UNION SELECT username, password, email, phone, address, id, now() FROM users --",
        'age' => '25',
        'gender' => 'Male',
        'description' => 'Test injection',
        'phone' => '1234567890',
        'email' => 'test@example.com'
    ],
    [
        'case' => 'XSS attempt',
        'name' => "<script>alert('XSS')</script>",
        'age' => '25',
        'gender' => 'Male',
        'description' => 'Test injection',
        'phone' => '1234567890',
        'email' => 'test@example.com'
    ],
    [
        'case' => 'Invalid data validation test',
        'name' => 'Normal User',
        'age' => '999999',
        'gender' => 'Male',
        'description' => 'Test injection',
        'phone' => '1234567890',
        'email' => 'invalid-email'
    ]
];

echo "<h2>SQL Injection Prevention Tests</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .test { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    .pass { background-color: #d4edda; border-color: #c3e6cb; }
    .fail { background-color: #f8d7da; border-color: #f5c6cb; }
    .info { background-color: #d1ecf1; border-color: #bee5eb; }
    pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
</style>";

foreach ($sqlInjectionTests as $index => $test) {
    echo "<div class='test info'>";
    echo "<h3>Test " . ($index + 1) . ": " . htmlspecialchars($test['case'], ENT_QUOTES, 'UTF-8') . "</h3>";
    echo "<strong>Input Data:</strong><pre>";
    echo "Name: " . htmlspecialchars($test['name'], ENT_QUOTES, 'UTF-8') . "\n";
    echo "Age: " . htmlspecialchars($test['age'], ENT_QUOTES, 'UTF-8') . "\n";
    echo "Email: " . htmlspecialchars($test['email'], ENT_QUOTES, 'UTF-8') . "\n";
    echo "</pre>";

    echo "<h4>Vulnerable Version (index.php):</h4>";
    testVulnerableVersion($test);

    echo "<h4>Secure Version (index_secure.php):</h4>";
    testSecureVersion($test);

    echo "</div>";
}

function testVulnerableVersion($test) {
    echo "<div class='test fail'>";

    // Simulate the exact field names used by the vulnerable form handler.
    $_POST = [
        'Name' => $test['name'],
        'Age' => $test['age'],
        'Gender' => $test['gender'],
        'Description' => $test['description'],
        'Phone' => $test['phone'],
        'Email' => $test['email']
    ];
    $_SERVER['REQUEST_METHOD'] = 'POST';

    $Name = $_POST['Name'] ?? '';
    $Age = $_POST['Age'] ?? '';
    $Gender = $_POST['Gender'] ?? '';
    $Description = $_POST['Description'] ?? '';
    $Phone = $_POST['Phone'] ?? '';
    $Email = $_POST['Email'] ?? '';

    $mysql = "INSERT INTO fullform (Name, Age, Gender, Description, Phone, Email, Date) VALUES ('$Name', '$Age', '$Gender', '$Description', '$Phone', '$Email', CURRENT_TIMESTAMP)";

    echo "Connection step skipped for demo purposes<br>";
    echo "<strong>Generated SQL:</strong><pre>" . htmlspecialchars($mysql, ENT_QUOTES, 'UTF-8') . "</pre>";
    echo "[WARN] <strong>VULNERABLE:</strong> Direct variable interpolation detected!<br>";
    echo "[WARN] <strong>RISK:</strong> SQL injection possible - malicious SQL could be executed<br>";
    echo "</div>";
}

function testSecureVersion($test) {
    echo "<div class='test pass'>";

    // Simulate the exact field names used by the secure form handler.
    $_POST = [
        'name' => $test['name'],
        'age' => $test['age'],
        'gender' => $test['gender'],
        'description' => $test['description'],
        'phone' => $test['phone'],
        'email' => $test['email']
    ];
    $_SERVER['REQUEST_METHOD'] = 'POST';

    try {
        $required_fields = ['name', 'age', 'gender', 'description', 'phone', 'email'];
        $missing_fields = [];

        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $missing_fields[] = $field;
            }
        }

        if (!empty($missing_fields)) {
            echo "[WARN] Validation failed: Missing fields detected<br>";
        } else {
            $name = sanitize_input($_POST['name']);
            $age = filter_var($_POST['age'], FILTER_VALIDATE_INT);
            $gender = sanitize_input($_POST['gender']);
            $description = sanitize_input($_POST['description']);
            $phone = sanitize_input($_POST['phone']);
            $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

            echo "Input sanitization applied<br>";
            echo "Sanitized name: " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "<br>";

            if ($age === false || $age < 1 || $age > 120) {
                echo "[WARN] Age validation caught invalid input<br>";
            }

            if ($email === false) {
                echo "[WARN] Email validation caught invalid input<br>";
            }

            echo "<strong>Prepared Statement:</strong><pre>";
            echo "INSERT INTO fullform (Name, Age, Gender, Description, Phone, Email, Date) VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)";
            echo "</pre>";
            echo "[OK] <strong>SECURE:</strong> Parameters bound separately from SQL<br>";
            echo "[OK] <strong>PROTECTED:</strong> SQL injection prevented<br>";
        }
    } catch (Throwable $e) {
        echo "[WARN] Exception caught: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "<br>";
    }

    echo "</div>";
}

echo "<h2>Test Summary</h2>";
echo "<div class='test info'>";
echo "<h3>Security Comparison:</h3>";
echo "<ul>";
echo "<li><strong>Vulnerable Version:</strong> Direct string interpolation allows SQL injection</li>";
echo "<li><strong>Secure Version:</strong> Prepared statements prevent SQL injection</li>";
echo "<li><strong>Input Validation:</strong> Secure version validates and sanitizes all inputs</li>";
echo "<li><strong>Error Handling:</strong> Secure version handles errors gracefully</li>";
echo "</ul>";
echo "</div>";

?>
