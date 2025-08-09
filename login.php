<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connect to the database "ak-store"
$conn = new mysqli("localhost", "root", "", "ak-store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if email and password are provided
if (empty($_POST['email']) || empty($_POST['password'])) {
    die("Email or password missing.");
}

$email = trim($_POST['email']);
$password = $_POST['password'];

// Prepare statement to prevent SQL injection
$sql = "SELECT id, firstname, lastname, email, phone, password, role FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo "User not found. Please check your email.";
    $stmt->close();
    $conn->close();
    exit;
}

$stmt->bind_result($id, $firstname, $lastname, $email_db, $phone, $hashed_password, $role);
$stmt->fetch();

// Check password
if (password_verify($password, $hashed_password)) {
    // Override role to admin if email & password match your special admin
    if ($email === "anukdk123@gmail.com" && $password === "12345") {
        $role = "admin";
    }

    // Set session variables
    $_SESSION['id'] = $id;
    $_SESSION['user_id'] = $id;          // For notifications or other purposes
    $_SESSION['email'] = $email_db;
    $_SESSION['role'] = $role;
    $_SESSION['username'] = $firstname;

    // Redirect based on role
    if ($role === 'admin') {
        header("Location: admin-dashboard.php");
        exit;
    } else {
        header("Location: index.php");
        exit;
    }
} else {
    echo "Incorrect password.";
}

$stmt->close();
$conn->close();
?>
