<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.html");
  exit;
}

$conn = new mysqli("localhost", "root", "", "ak-store");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $conn->real_escape_string($_POST['name']);
  $price = $conn->real_escape_string($_POST['price']);
  $gender = $_POST['gender'];
  $category = $_POST['category'];

  $image = $_FILES['image']['name'];
  $target = "uploads/" . basename($image);

  if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
    $sql = "INSERT INTO clothes (name, image, price, gender, category) 
            VALUES ('$name', '$image', '$price', '$gender', '$category')";
    if ($conn->query($sql)) {
      $message = "Clothes uploaded successfully!";
    } else {
      $message = "Upload failed: " . $conn->error;
    }
  } else {
    $message = "Image upload failed.";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Upload Clothes</title>
  <style>
    body {
      margin: 0;
      font-family: sans-serif;
      background: #f2f2f2;
    }

    .container {
      display: flex;
      height: 100vh;
    }

    .sidebar {
      width: 220px;
      background: #2c3e50;
      color: white;
      height: 100%;
      flex-shrink: 0;
      position: fixed;
      left: 0;
      top: 0;
      bottom: 0;
      overflow-y: auto;
    }

    .sidebar .profile {
      text-align: center;
      padding: 20px;
    }

    .profile-pic {
      width: 80px;
      border-radius: 50%;
    }

    .online {
      color: limegreen;
    }

    .sidebar .menu {
      list-style: none;
      padding: 0;
    }

    .sidebar .menu li a {
      display: block;
      padding: 12px 20px;
      color: white;
      text-decoration: none;
    }

    .sidebar .menu li a:hover {
      background-color: #34495e;
    }

    .main-content {
      margin-left: 220px;
      padding: 40px;
      flex-grow: 1;
    }

    form {
      background: white;
      padding: 20px 30px;
      border-radius: 8px;
      width: 100%;
      max-width: 500px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      margin: auto;
    }

    h2 {
      text-align: center;
    }

    input, select {
      width: 100%;
      margin-bottom: 12px;
      padding: 10px;
      font-size: 16px;
    }

    input[type="submit"] {
      background: #007bff;
      color: white;
      border: none;
      cursor: pointer;
      font-size: 16px;
    }

    input[type="submit"]:hover {
      background: #0056b3;
    }

    p {
      text-align: center;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="profile">
        <img src="assets/images/admin.jpg" alt="Admin" class="profile-pic">
        <h3>AK store</h3>
        <p class="online">● Online</p>
      </div>
      <ul class="menu">
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="manage_user.php">Manage Users</a></li>
        <li><a href="upload_notification.php">Notification Upload</a></li>
        <li><a href="manage_posts.php">Manage Posts</a></li>
        <li><a href="view_order.php">View Orders</a></li>
        <li><a href="upload_clothes.php">Upload Clothes</a></li>
        <li><a href="manage_clothes.php">Manage Clothes</a></li>
        <li><a href="logout.php">Logout</a></li>
        <li><a href="#">Settings</a></li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <form method="POST" enctype="multipart/form-data">
        <h2>Upload Clothes</h2>
        <?php if ($message) echo "<p>$message</p>"; ?>
        
        <input type="text" name="name" placeholder="Product Name" required>
        <input type="text" name="price" placeholder="Price" required>

        <select name="gender" required>
          <option value="">-- Select Gender --</option>
          <option value="men">Men</option>
          <option value="women">Women</option>
          <option value="kids">Kids</option>
        </select>

        <select name="category" required>
          <option value="">-- Select Category --</option>
          <option value="regular">Regular</option>
          <option value="sale">Sale</option>
        </select>

        <input type="file" name="image" accept="image/*" required>
        <input type="submit" value="Upload">
      </form>
    </div>
  </div>
</body>
</html>
