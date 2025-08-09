<?php
session_start();

// Protect page for logged-in users with role 'user'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.html");
    exit;
}

$conn = new mysqli("localhost", "root", "", "ak-store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get selected category (gender)
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Fetch clothes based on category
if ($category) {
    $stmt = $conn->prepare("SELECT * FROM clothes WHERE gender = ? ORDER BY id DESC");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $clothes_result = $stmt->get_result();
    $stmt->close();
} else {
    $clothes_result = $conn->query("SELECT * FROM clothes ORDER BY id DESC");
}

// Fetch posts
$posts_result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>User Homepage</title>
    <style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
    }

    body {
        background-color: #867a84;
    }

    .navbar ul {
        list-style: none;
        display: flex;
        justify-content: center;
        padding: 0;
        margin: 0;
        background-color: #343a40;
    }

    .navbar li {
        margin: 0 15px;
    }

    .navbar a {
        color: white;
        text-decoration: none;
        font-weight: bold;
        padding: 15px 10px;
        display: block;
    }

    .navbar a:hover {
        background-color: #495057;
        border-radius: 5px;
    }

    .gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        padding: 20px;
        justify-content: center;
    }

    .item {
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 10px;
        width: 200px;
        text-align: center;
        background: #fdf6e3;
    }

    .item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 5px;
    }

    h3 {
        margin: 10px 0 5px;
    }

    p {
        margin: 0;
    }

    .posts {
        padding: 20px 40px;
        max-width: 900px;
        margin: 0 auto 40px auto;
    }

    .post {
        border: 1px solid #ccc;
        background: #fff;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
    }
    </style>
</head>

<body>

    <nav id="nav">
        <div class="navbar">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="recommendation.html">Recommend</a></li>
                <li><a class="nav-link" href="men.php">Men</a></li>
                <li><a class="nav-link" href="women.php">Women</a></li>
                <li><a class="nav-link active" href="kids.php">Kids</a></li>
                <li><a class="nav-link" href="sale.php">Sale</a></li>
                <li><a class="nav-link" href="cart.php">Cart 🛒</a></li>
                <li><a class="nav-link" href="notification.php">🔔</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <h1 style="text-align:center; margin: 20px 0;">
        Welcome, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User'; ?>!
    </h1>

    <h2 style="text-align:center;">📰 Latest Posts</h2>
    <div class="posts">
        <?php
        if ($posts_result && $posts_result->num_rows > 0) {
            while ($row = $posts_result->fetch_assoc()) {
                echo "<div class='post'>";
                echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                echo "<p>" . nl2br(htmlspecialchars($row['content'] ?? "No content.")) . "</p>";
                echo "<small>Posted on: " . htmlspecialchars($row['created_at']) . "</small>";
                echo "</div>";
            }
        } else {
            echo "<p style='text-align:center;'>No posts yet.</p>";
        }
        ?>
    </div>
    <div id="notification-alert" style="display:none; background: #fffae6; color: #444; padding: 10px; margin: 20px; border: 1px solid #ccc; text-align:center;">
</div>

<script>c
setInterval(function() {
    fetch("check_notification.php")
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                const alertBox = document.getElementById("notification-alert");
                alertBox.innerText = data.message;
                alertBox.style.display = "block";
                setTimeout(() => {
                    alertBox.style.display = "none";
                }, 5000); // hide after 5 seconds
            }
        });
}, 10000); // check every 10 seconds
</script>


</body>

</html>