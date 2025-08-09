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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);

    if ($title && $content) {
      $conn->query("INSERT INTO posts (title, content) VALUES ('$title', '$content')");
      $message = "Post added!";
    } else {
      $message = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Posts</title>
    <style>
    body {
        background-color: #fff8e1;
        /* cream */
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }
      .sidebar {
        width: 220px;
        background: #2c3e50;
        color: white;
        height: 100vh;
        position: fixed;
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
        padding: 20px;
        width: 100%;
    }

    h1 {
        text-align: center;
        font-size: 2.8rem;
        font-weight: bold;
        margin: 40px 0 30px;
        color: #333;
    }

    .container {
        max-width: 500px;
        margin: 0 auto 60px;
        background: white;
        padding: 30px 40px;
        border: 2px solid #d2b48c;
        /* tan-ish border */
        border-radius: 10px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }

    form {
        display: flex;
        flex-direction: column;
    }

    input[type="text"],
    textarea {
        font-size: 1.1rem;
        padding: 12px 15px;
        margin-bottom: 20px;
        border: 2px solid #ccc;
        border-radius: 6px;
        resize: vertical;
        transition: border-color 0.3s;
    }

    input[type="text"]:focus,
    textarea:focus {
        border-color: #a67c00;
        /* golden brown */
        outline: none;
    }

    textarea {
        min-height: 120px;
        font-family: Arial, sans-serif;
    }

    input[type="submit"] {
        background-color: #4caf50;
        /* green */
        color: white;
        font-size: 1.3rem;
        padding: 14px 0;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        width: 50%;
        margin: 0 auto;
        font-weight: bold;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
    }

    input[type="submit"]:hover {
        background-color: #7e57c2;
        /* purple */
    }

    .message {
        text-align: center;
        margin-bottom: 25px;
        font-weight: 600;
        color: green;
        font-size: 1.1rem;
    }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="profile">
            <img src="assets/images/admin.jpg" alt="Admin" class="profile-pic">
            <h3>AK store</h3>
            <p class="online">● Online</p>
        </div>
        <ul class="menu">
            <li><a href="index.php"><i class="fa fa-home"></i> Dashboard</a></li>
            <li><a href="manage_user.php"><i class="fa fa-users"></i> Manage Users</a></li>
            <li><a href="upload_notification.php"><i class="fa fa-file-alt"></i> notification upload</a></li>
            <li><a href="manage_posts.php"><i class="fa fa-file-alt"></i> Manage Posts</a></li>
            <li><a href="view_order.php"><i class="fa fa-shopping-cart"></i> View Orders</a></li>
            <li><a href="upload_clothes.php"><i class="fa fa-upload"></i> Upload Clothes</a></li>
             <li><a href="manage_clothes.php"><i class="fa fa-upload"></i>Manage Clothes</a></li> 
              <li><a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
            <li><a href="#"><i class="fa fa-cog"></i> Settings</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h2 style="text-align:center;">All Posts</h2>
    <div class="container" style="max-width: 700px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr style="background-color: #f0e68c;">
                <th style="padding: 10px;">ID</th>
                <th style="padding: 10px;">Title</th>
                <th style="padding: 10px;">Content</th>
                <th style="padding: 10px;">Delete</th>
            </tr>
            <?php
    $posts = $conn->query("SELECT * FROM posts ORDER BY id ASC");

    while ($row = $posts->fetch_assoc()):
    ?>
            <tr style="background-color: #fffbe6;">
                <td style="padding: 10px;"><?php echo $row['id']; ?></td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($row['title']); ?></td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($row['content']); ?></td>
                <td style="padding: 10px;">
                    <a href="delete_post.php?id=<?php echo $row['id']; ?>"
                        onclick="return confirm('Are you sure you want to delete this post?');"
                        style="color: white; background: crimson; padding: 6px 12px; text-decoration: none; border-radius: 6px;">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
    </div>

    


</body>

</html>