<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Women's Collection</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background: #f2f2f2;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 1200px;
        margin: auto;
        padding: 0 20px;
    }

    nav#nav {
        background: #333;
        padding: 10px 0;
    }

    nav#nav .navbar ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        gap: 20px;
    }

    nav#nav .navbar ul li {
        display: inline;
    }

    nav#nav .navbar ul li a {
        color: white;
        text-decoration: none;
        padding: 8px 12px;
        display: inline-block;
    }

    nav#nav .navbar ul li a.active,
    nav#nav .navbar ul li a:hover {
        background-color: #555;
        border-radius: 4px;
    }

    h2 {
        background-color: #222;
        color: white;
        padding: 15px 20px;
    }

    .product-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        padding: 20px 0;
        justify-content: flex-start;
        /* Align to left */
    }

    .product {
        background: white;
        border: 1px solid #ccc;
        border-radius: 8px;
        width: 220px;
        padding: 10px;
        text-align: center;
        box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.1);
    }

    .product img {
        max-width: 100%;
        height: 180px;
        object-fit: cover;
        border-radius: 5px;
    }

    .product h3 {
        margin: 10px 0 5px;
    }

    .product p {
        margin: 0;
        color: #555;
    }

    .product form {
        margin-top: 10px;
    }

    .product button {
        margin-top: 5px;
        padding: 10px 20px;
        background-color: #007bff;
        font-size: 16px;
        font-weight: bold;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        width: 100%;
    }

    .product button:hover {
        background-color: #0056b3;
    }
    </style>
</head>

<body>

    <nav id="nav">
        <div class="navbar">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a class="nav-link" href="recommendation.html">Recommend</a></li>
                <li><a class="nav-link" href="men.php">Men</a></li>
                <li><a class="nav-link active" href="women.php">Women</a></li>
                <li><a class="nav-link" href="kids.php">Kids</a></li>
                <li><a class="nav-link" href="sale.php">Sale</a></li>
                <li><a class="nav-link" href="cart.php">Cart 🛒</a></li>
                <li><a class="nav-link" href="notification.php">🔔</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h2>👗 Women's Collection</h2>

        <div class="product-grid">
            <?php
            $result = $conn->query("SELECT * FROM clothes WHERE gender = 'women'");
            while ($row = $result->fetch_assoc()):
            ?>

            <div class="product">
                <?php if (!empty($row['image'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image">
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                <p>Rs. <?php echo htmlspecialchars($row['price']); ?></p>
                <p>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <?php if ($i <= $row['rating']): ?>
                    ⭐
                    <?php else: ?>
                    ☆
                    <?php endif; ?>
                    <?php endfor; ?>
                </p>

                <form method="POST" action="add_to_cart.php">
                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($row['name']); ?>">
                    <input type="hidden" name="price" value="<?php echo $row['price']; ?>">

                    <button type="submit">Add to Cart</button>
                </form>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>

</html>