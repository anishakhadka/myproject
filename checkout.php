<?php
session_start();

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$cart = $_SESSION['cart'];
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_order'])) {
    // Save total to session to use in payment.php
    $_SESSION['order_total'] = $total;
    header("Location: payment.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Checkout</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        padding: 30px;
        background: #f4f4f4;
        text-align: center;
    }

    .container {
        background: white;
        max-width: 600px;
        margin: auto;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px #ccc;
    }

    h2 {
        margin-bottom: 20px;
    }

    .total {
        font-size: 1.5rem;
        margin-bottom: 30px;
    }

    button {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 14px 30px;
        font-size: 1.2rem;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #218838;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Checkout Summary</h2>
        <p class="total">Total Amount: Rs. <?php echo number_format($total, 2); ?></p>

        <form method="POST">
            <button type="submit" name="process_order">Process Order</button>
        </form>
    </div>
</body>

</html>