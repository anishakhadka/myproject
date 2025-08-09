<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['cart']) || empty($_SESSION['cart']) || !isset($_SESSION['order_total'])) {
    header("Location: cart.php");
    exit;
}

$total = $_SESSION['order_total'];
$cart = $_SESSION['cart'];

$selected_method = '';
$success_message = '';
$redirect = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['payment_method'])) {
        $selected_method = $_POST['payment_method'];
    } elseif (isset($_POST['confirm_payment'])) {
        $selected_method = $_POST['confirm_payment'];
        $purpose = trim($_POST['purpose'] ?? '');

        if (!empty($purpose)) {
            // Connect to DB
            $conn = new mysqli("localhost", "root", "", "ak-store");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $user_id = $_SESSION['id'] ?? null;

            if ($user_id && !empty($cart)) {
                foreach ($cart as $item) {
                    $name = $conn->real_escape_string($item['name']);
                    $quantity = (int)$item['quantity'];
                    $price = (float)$item['price'];
                    $total_price = $price * $quantity;

                    // Insert into orders table with current date and default status 'Pending'
                    $insert = $conn->query("
                        INSERT INTO orders (user_id, product_name, quantity, total_price, order_date, status)
                        VALUES ('$user_id', '$name', '$quantity', '$total_price', CURDATE(), 'Pending')
                    ");

                    if (!$insert) {
                        die("Insert failed: " . $conn->error);
                    }
                }
            }

            $conn->close();

            $success_message = ucfirst($selected_method) . " payment successful for purpose: \"$purpose\".";
            unset($_SESSION['cart']);
            unset($_SESSION['order_total']);
            $redirect = true;
        } else {
            $success_message = "Please enter a purpose.";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Choose Payment Method</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
            text-align: center;
        }

        .option-box,
        .form-box {
            background: #fff;
            border: 2px solid #ccc;
            border-radius: 10px;
            padding: 25px;
            margin: 20px auto;
            width: 320px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        input[type="radio"] {
            margin-right: 10px;
        }

        button,
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 10px 22px;
            border: none;
            border-radius: 5px;
            font-size: 15px;
            margin-top: 15px;
            cursor: pointer;
        }

        input[type="text"],
        input[type="number"] {
            width: 90%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 6px;
            border: 1px solid #aaa;
            font-size: 1rem;
        }

        .success {
            color: green;
            font-weight: bold;
            font-size: 1.2rem;
            margin-top: 30px;
        }

        h2,
        h3 {
            margin-bottom: 15px;
        }
    </style>

    <?php if ($redirect): ?>
    <script>
        setTimeout(function () {
            window.location.href = "index.php";
        }, 2000);
    </script>
    <?php endif; ?>
</head>

<body>

    <h2>Choose Payment Method</h2>
    <p>Total Amount: <strong>Rs. <?php echo number_format($total, 2); ?></strong></p>

    <?php if (!$selected_method && !$success_message): ?>
    <form method="POST" action="">
        <div class="option-box">
            <label>
                <input type="radio" name="payment_method" value="esewa" required> Pay with eSewa
            </label><br><br>
            <label>
                <input type="radio" name="payment_method" value="khalti" required> Pay with Khalti
            </label><br><br>
            <button type="submit">Proceed</button>
        </div>
    </form>
    <?php endif; ?>

    <?php if ($selected_method && !$success_message): ?>
    <div class="form-box">
        <h3><?php echo ucfirst($selected_method); ?> Payment</h3>
        <form method="POST" action="">
            <p><strong>Send to ID:</strong> 9765630974</p>
            <input type="hidden" name="confirm_payment" value="<?php echo htmlspecialchars($selected_method); ?>">
            <input type="text" name="Number" placeholder="Your Number" required pattern="\d{10}" maxlength="10" title="Enter a 10-digit phone number">
            <input type="text" name="purpose" placeholder="Purpose of sending money" required>
            <br>
            <input type="submit" value="Send">
        </form>
    </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
    <div class="success">
        Payment successful<br>
        <?php echo htmlspecialchars($success_message); ?>
    </div>
    <?php endif; ?>

</body>

</html>
