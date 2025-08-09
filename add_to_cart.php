<?php
session_start();
include 'db_connect.php'; // Include your DB connection file

// Create cart array if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    if ($quantity < 1) $quantity = 1; // Minimum quantity is 1

    if ($product_id > 0) {
        // Fetch product info from DB
        $stmt = $conn->prepare("SELECT name, price, image FROM clothes WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($product = $result->fetch_assoc()) {
            // If product already in cart, increase quantity
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                // Add new product with image info
                $_SESSION['cart'][$product_id] = [
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity,
                    'image' => $product['image'], // store image filename
                ];
            }
            // Redirect to cart with success message
            header("Location: cart.php?added=1");
            exit;
        } else {
            // Product not found in DB
            header("Location: index.php?error=ProductNotFound");
            exit;
        }
    } else {
        // Invalid product ID
        header("Location: index.php?error=InvalidProduct");
        exit;
    }
}
?>
