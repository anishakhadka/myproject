<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
  header("Location: login.html");
  exit;
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>User Homepage</title>
</head>

<body>
    <h1>Welcome User!</h1>
    <button onclick="logout()">Logout</button>

    <script>
    if (localStorage.getItem("role") !== "user") {
        alert("You must login first!");
        window.location.href = "login.html";
    }

    function logout() {
        localStorage.removeItem("role");
        window.location.href = "login.html";
    }
    </script>
</body>

</html>