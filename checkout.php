<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Calculate total price from cart items
$totalPrice = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Payment Details</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; color: #333; }
        .navbar { background-color: #2c3e50; padding: 15px 20px; color: white; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: #ecf0f1; text-decoration: none; margin: 0 10px; padding: 8px 12px; border: 1px solid transparent; border-radius: 4px; transition: background-color 0.3s; }
        .navbar a:hover { background-color: #34495e; }
        .container { max-width: 600px; margin: 40px auto; background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 20px; color: #2c3e50; }
        .summary { text-align: center; margin-bottom: 20px; }
        form { width: 100%; }
        .payment-methods { margin-bottom: 20px; }
        .payment-methods p { font-size: 16px; font-weight: 500; }
        .payment-methods label { margin-right: 20px; font-weight: 500; }
        .payment-section { display: none; margin-bottom: 20px; }
        .payment-section.active { display: block; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 6px; font-weight: 500; }
        input[type=\"text\"], input[type=\"email\"], input[type=\"tel\"], input[type=\"number\"] { width: 100%; padding: 10px; border: 1px solid #bdc3c7; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #27ae60; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; transition: background-color 0.3s; }
        button:hover { background-color: #219150; }
        footer { background-color: #2c3e50; color: white; padding: 15px 20px; text-align: center; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="navbar">
        <div><a href="cart.php">← Back to Cart</a></div>
        <div>Checkout</div>
        <div><a href="logout.php">Logout</a></div>
    </div>
    <div class="container">
        <h2>Checkout</h2>
        <div class="summary">
            <p><strong>Total Price:</strong> $<?php echo number_format($totalPrice, 2); ?></p>
        </div>
        <form action="process_payment.php" method="post" id="paymentForm">
            <div class="payment-methods">
                <p>Select Payment Method:</p>
                <label><input type="radio" name="payment_method" value="credit_card" checked> Credit Card</label>
                <label><input type="radio" name="payment_method" value="paypal"> PayPal</label>
                <label><input type="radio" name="payment_method" value="other"> Other</label>
            </div>
            <!-- Credit Card Section -->
            <div id="credit_card_section" class="payment-section active">
                <div class="form-group">
                    <label for="cardholder_name">Cardholder Name</label>
                    <input type="text" id="cardholder_name" name="cardholder_name" placeholder="John Doe" required>
                </div>
                <div class="form-group">
                    <label for="card_number">Card Number</label>
                    <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" required>
                </div>
                <div class="form-group">
                    <label for="expiration_date">Expiration Date (MM/YY)</label>
                    <input type="text" id="expiration_date" name="expiration_date" placeholder="MM/YY" required>
                </div>
                <div class="form-group">
                    <label for="cvv">CVV</label>
                    <input type="number" id="cvv" name="cvv" placeholder="123" required>
                </div>
            </div>
            <!-- PayPal Section -->
            <div id="paypal_section" class="payment-section">
                <div class="form-group">
                    <label for="paypal_email">PayPal Email Address</label>
                    <input type="email" id="paypal_email" name="paypal_email" placeholder="email@example.com" required>
                </div>
            </div>
            <!-- Other Payment Section -->
            <div id="other_section" class="payment-section">
                <div class="form-group">
                    <label for="other_payment_details">Payment Details</label>
                    <input type="text" id="other_payment_details" name="other_payment_details" placeholder="e.g., Bank Transfer Reference" required>
                </div>
            </div>
            <button type="submit">Submit Payment</button>
        </form>
    </div>
    <footer>
        <p>&copy; 2025 ToolHub Marketplace. All Rights Reserved.</p>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const radios = document.getElementsByName('payment_method');
            const ccSection = document.getElementById('credit_card_section');
            const paypalSection = document.getElementById('paypal_section');
            const otherSection = document.getElementById('other_section');
            function updateSections() {
                ccSection.classList.remove('active');
                paypalSection.classList.remove('active');
                otherSection.classList.remove('active');
                document.querySelectorAll('#credit_card_section input').forEach(i => i.required = false);
                document.querySelectorAll('#paypal_section input').forEach(i => i.required = false);
                document.querySelectorAll('#other_section input').forEach(i => i.required = false);
                const selected = document.querySelector('input[name=\"payment_method\"]:checked').value;
                if (selected === 'credit_card') {
                    ccSection.classList.add('active');
                    document.querySelectorAll('#credit_card_section input').forEach(i => i.required = true);
                } else if (selected === 'paypal') {
                    paypalSection.classList.add('active');
                    document.querySelectorAll('#paypal_section input').forEach(i => i.required = true);
                } else if (selected === 'other') {
                    otherSection.classList.add('active');
                    document.querySelectorAll('#other_section input').forEach(i => i.required = true);
                }
            }
            radios.forEach(r => r.addEventListener('change', updateSections));
            updateSections();
        });
    </script>
</body>
</html>
