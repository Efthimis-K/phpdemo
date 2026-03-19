<?php

// Database configuration, names omitted for security
$servername = ""; // Server name
$username = ""; // Username
$password = ""; // Password


$info = mysqli_connect($servername, $username, $password);

if (!$info) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "form submitted successfully";

$Name = $_POST['Name'];
$Age = $_POST['Age'];
$Gender = $_POST['Gender'];
$Description = $_POST['Description'];
$Phone = $_POST['Phone'];
$Email = $_POST['Email'];

$mysql = "
INSERT INTO fullform (Name, Age, Gender, Description, Phone, Email, Date) VALUES ('$Name', '$Age', '$Gender', '$Description', '$Phone', '$Email', CURRENT_TIMESTAMP)
";


// mysqli_query($info, $mysql);

?>