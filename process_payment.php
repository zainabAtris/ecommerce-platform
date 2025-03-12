<?php
session_start();
try {
    $db = new PDO('sqlite:market.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}

if (!isset($_SESSION['user']) || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: login.php");
    exit();
}

$payment_method = $_POST['payment_method'] ?? '';
$errors = [];

// Validate payment details based on selected method
switch($payment_method) {
    case 'credit_card':
        $cardholder = trim($_POST['cardholder_name'] ?? '');
        $card_number = trim($_POST['card_number'] ?? '');
        $expiration = trim($_POST['expiration_date'] ?? '');
        $cvv = trim($_POST['cvv'] ?? '');
        if(empty($cardholder) || empty($card_number) || empty($expiration) || empty($cvv)){
            $errors[] = "All credit card fields are required.";
        }
        break;
    case 'paypal':
        $paypal_email = trim($_POST['paypal_email'] ?? '');
        if(empty($paypal_email) || !filter_var($paypal_email, FILTER_VALIDATE_EMAIL)){
            $errors[] = "A valid PayPal email is required.";
        }
        break;
    case 'other':
        $other_details = trim($_POST['other_payment_details'] ?? '');
        if(empty($other_details)){
            $errors[] = "Payment details are required.";
        }
        break;
    default:
        $errors[] = "Invalid payment method selected.";
}

$totalPrice = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}

if(!empty($errors)){
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Payment Error</title>
        <style> body { font-family: Arial, sans-serif; padding: 20px; } .error { background-color: #ffdddd; padding: 15px; border: 1px solid #ff5c5c; margin-bottom: 20px; border-radius: 5px; } a { color: #0066cc; text-decoration: none; } </style>
    </head>
    <body>
        <h2>Payment Processing Error</h2>
        <div class="error">
            <?php foreach($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
        <p>Please <a href="checkout.php">go back</a> and correct the errors.</p>
    </body>
    </html>
    <?php
    exit();
}

// Begin transaction to update product stocks
$db->beginTransaction();
$canProcess = true;
foreach ($_SESSION['cart'] as $id => $item) {
    $stmt = $db->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $currentStock = $stmt->fetchColumn();
    if ($currentStock === false || $currentStock < $item['quantity']) {
        $canProcess = false;
        break;
    }
}

if($canProcess){
    foreach ($_SESSION['cart'] as $id => $item) {
        $stmt = $db->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt->execute([$item['quantity'], $id]);
    }
    $db->commit();
    $_SESSION['cart'] = [];
} else {
    $db->rollBack();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Payment Error</title>
        <style> body { font-family: Arial, sans-serif; padding: 20px; } a { color: #0066cc; text-decoration: none; } </style>
    </head>
    <body>
        <h2>Payment Processing Error</h2>
        <p>One or more items in your cart are not available in the requested quantity.</p>
        <p>Please <a href="cart.php">return to your cart</a> and adjust your order.</p>
    </body>
    </html>
    <?php
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Processed</title>
    <style>
        body { font-family: 'Roboto', sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; color: #333; }
        .navbar { background-color: #2c3e50; padding: 15px 20px; color: white; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: #ecf0f1; text-decoration: none; margin-left: 10px; padding: 8px 12px; border: 1px solid transparent; border-radius: 4px; transition: background-color 0.3s; }
        .navbar a:hover { background-color: #34495e; }
        .container { max-width: 600px; margin: 40px auto; background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); text-align: center; }
        .btn { display: inline-block; margin-top: 20px; padding: 12px 20px; background-color: #27ae60; color: white; text-decoration: none; border-radius: 4px; transition: background-color 0.3s; }
        .btn:hover { background-color: #219150; }
        footer { background-color: #2c3e50; color: white; padding: 15px 20px; text-align: center; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="navbar">
        <div>ToolHub Marketplace</div>
        <div>
            <a href="index.php">Home</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="container">
        <h2>Payment Processed Successfully!</h2>
        <p>Thank you, <?php echo htmlspecialchars($_SESSION['user']); ?>. Your payment of $<?php echo number_format($totalPrice,2); ?> has been processed.</p>
        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $payment_method))); ?></p>
        <?php if($payment_method === 'credit_card'): ?>
            <p><strong>Cardholder:</strong> <?php echo htmlspecialchars($cardholder); ?></p>
            <p><strong>Card Ending:</strong> **** <?php echo substr($card_number, -4); ?></p>
        <?php elseif($payment_method === 'paypal'): ?>
            <p><strong>PayPal Email:</strong> <?php echo htmlspecialchars($paypal_email); ?></p>
        <?php elseif($payment_method === 'other'): ?>
            <p><strong>Payment Details:</strong> <?php echo htmlspecialchars($other_details); ?></p>
        <?php endif; ?>
        <p><a href="index.php" class="btn">Continue Shopping</a></p>
    </div>
    <footer>
        <p>&copy; 2025 ToolHub Marketplace. All Rights Reserved.</p>
    </footer>
</body>
</html>
