<?php
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

try {
    $db = new PDO('sqlite:market.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Remove item from cart if requested
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_product_id'])) {
    $removeId = (int) $_POST['remove_product_id'];
    if (isset($_SESSION['cart'][$removeId])) {
        unset($_SESSION['cart'][$removeId]);
    }
    header("Location: cart.php");
    exit();
}

// Calculate total price
$totalPrice = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #ffebee; margin: 0; padding: 20px; }
        .navbar { background-color: #333; color: white; padding: 15px; text-align: center; }
        .navbar a { color: white; text-decoration: none; margin: 10px; padding: 10px 20px; background-color: #4CAF50; border-radius: 5px; }
        .navbar a:hover { background-color: #45a049; }
        .cart-container { padding: 20px; text-align: center; }
        .cart-item { background-color: #fff; border: 1px solid #ddd; padding: 10px; margin: 10px auto; width: 80%; text-align: left; }
        button { padding: 10px; background-color: #f44336; color: white; border: none; cursor: pointer; border-radius: 5px; }
        button:hover { background-color: #e53935; }
        footer { background-color: #333; color: white; padding: 15px; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h3><a href="index.php">Back to Shop</a> | View Cart</h3>
    </div>
    <div class="cart-container">
        <h2>Your Shopping Cart</h2>
        <?php if (empty($_SESSION['cart'])): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                <div class="cart-item">
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    <p>Price: $<?php echo number_format($item['price'], 2); ?> | Quantity: <?php echo $item['quantity']; ?></p>
                    <p><?php echo htmlspecialchars($item['description']); ?></p>
                    <form method="post">
                        <input type="hidden" name="remove_product_id" value="<?php echo $id; ?>">
                        <button type="submit">Remove</button>
                    </form>
                </div>
            <?php endforeach; ?>
            <h3>Total Price: $<?php echo number_format($totalPrice, 2); ?></h3>
            <form action="checkout.php" method="post" onsubmit="return validateCart()">
                <button type="submit" style="padding:10px 20px; background-color:#27ae60; color:white; border:none; border-radius:4px;">Proceed to Checkout</button>
            </form>
        <?php endif; ?>
    </div>
    <script>
        function validateCart() {
            if (<?php echo count($_SESSION['cart']); ?> === 0) {
                alert("Your cart is empty. Please add items before proceeding.");
                return false;
            }
            return true;
        }
    </script>
    <footer>
        <p>&copy; 2025 ToolHub Marketplace. All Rights Reserved.</p>
    </footer>
</body>
</html>
