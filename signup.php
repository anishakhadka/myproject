<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start output buffer
ob_start();

$conn = new mysqli("localhost", "root", "", "ak-store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (
    empty($_POST['firstname']) || empty($_POST['lastname']) ||
    empty($_POST['email']) || empty($_POST['phone']) ||
    empty($_POST['password']) || empty($_POST['repassword'])
) {
    die("Please fill all required fields.");
}

$firstname = trim($_POST['firstname']);
$lastname = trim($_POST['lastname']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$password = $_POST['password'];
$repassword = $_POST['repassword'];

if ($password !== $repassword) {
    die("Passwords do not match.");
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$role = 'user';

$sql = "INSERT INTO users (firstname, lastname, email, phone, password, role) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("ssssss", $firstname, $lastname, $email, $phone, $hashed_password, $role);

if ($stmt->execute()) {
    // Silent 1-second redirect
    echo "
    <html>
        <head>
            <script>
                setTimeout(function() {
                    window.location.href = 'login.html'; // Change to login.php if needed
                }, 1000); // 1 second
            </script>
        </head>
        <body></body>
    </html>
    ";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
ob_end_flush();
?>
