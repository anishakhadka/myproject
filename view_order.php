<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Only allow admin access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit;
}

// Connect to DB
$conn = new mysqli("localhost", "root", "", "ak-store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Approve/Delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = intval($_POST['order_id']);

    if (isset($_POST['approve'])) {
        // Approve order
        $conn->query("UPDATE orders SET status='Approved' WHERE id=$orderId");

        // Get user_id, product_id, and product_name for notification and rating update
        $getOrderData = $conn->query("SELECT user_id, product_id, product_name FROM orders WHERE id=$orderId");
        if ($getOrderData && $getOrderData->num_rows > 0) {
            $orderData = $getOrderData->fetch_assoc();
            $userId = $orderData['user_id'];
            $productId = (int)$orderData['product_id'];
            $productName = $conn->real_escape_string($orderData['product_name']);

            // Send notification to user
            $message = "✅ Your order #$orderId has been approved.";
            $stmt = $conn->prepare("INSERT INTO notification (user_id, message) VALUES (?, ?)");
            $stmt->bind_param("is", $userId, $message);
            $stmt->execute();
            $stmt->close();

            // Calculate total quantity sold for this product (using product_id if available)
            if ($productId > 0) {
                $orderCountResult = $conn->query("SELECT SUM(quantity) AS total_quantity FROM orders WHERE product_id=$productId AND status='Approved'");
            } else {
                // fallback to product_name if product_id is null or zero
                $orderCountResult = $conn->query("SELECT SUM(quantity) AS total_quantity FROM orders WHERE product_name='$productName' AND status='Approved'");
            }

            $orderCountRow = $orderCountResult->fetch_assoc();
            $totalQuantity = $orderCountRow['total_quantity'] ?? 0;

            // Calculate stars: 1 star for every 10 items sold, max 5 stars
            $stars = min(5, floor($totalQuantity / 10));

            // Update product rating in clothes table by product id or product name fallback
            if ($productId > 0) {
                $conn->query("UPDATE clothes SET rating=$stars WHERE id=$productId");
            } else {
                $conn->query("UPDATE clothes SET rating=$stars WHERE name='$productName'");
            }
        }
    } elseif (isset($_POST['delete'])) {
        // Delete order
        $conn->query("DELETE FROM orders WHERE id=$orderId");
    }
}

// Fetch orders with user info
$sql = "
    SELECT orders.*, users.firstname 
    FROM orders 
    JOIN users ON orders.user_id = users.id
    ORDER BY orders.id ASC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>View Orders</title>
    <style>
    body {
        background-color: #fff8e1;
        font-family: Arial, sans-serif;
        padding: 20px;
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
        color: #333;
        font-size: 2.5rem;
    }

    table {
        margin: 0 auto;
        width: 95%;
        border-collapse: collapse;
        font-size: 1rem;
        background: white;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    th,
    td {
        border: 1px solid #999;
        padding: 12px;
        text-align: center;
    }

    th {
        background-color: #f0e68c;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    form {
        display: inline;
    }

    button {
        padding: 6px 12px;
        margin: 2px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }

    .approve {
        background-color: #28a745;
        color: white;
    }

    .delete {
        background-color: #dc3545;
        color: white;
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

    <div class="main-content">
        <h1>🧾 All Orders</h1>

        <table>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['id'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['firstname'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['product_name'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['quantity'] ?? '') ?></td>
                <td>Rs. <?= htmlspecialchars($row['total_price'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['status'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['order_date'] ?? '') ?></td>
                <td>
                    <?php if (($row['status'] ?? '') !== 'Approved'): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($row['id'] ?? '') ?>">
                        <button type="submit" name="approve" class="approve">Approve</button>
                    </form>
                    <?php endif; ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($row['id'] ?? '') ?>">
                        <button type="submit" name="delete" class="delete"
                            onclick="return confirm('Delete this order?')">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>

</html>

<?php $conn->close(); ?>
