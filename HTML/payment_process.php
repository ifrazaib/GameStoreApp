<?php
session_start();

if (!isset($_SESSION['order_ready']) || !isset($_SESSION['order_total'])) {
    echo "<h3>Invalid payment session.</h3><a href='products.php'>Return to Store</a>";
    exit();
}

$pdo = new PDO("mysql:host=localhost;dbname=game_store", "root", "");

$paymentMethod = $_POST['payment_method'];
$address = $_POST['address'] ?? null;
$bankNumber = $_POST['bank_number'] ?? null;
$total = $_SESSION['order_total'];

// Save order details
$stmt = $pdo->prepare("INSERT INTO order_details (payment_method, address, bank_number, total_amount) VALUES (?, ?, ?, ?)");
$stmt->execute([$paymentMethod, $address, $bankNumber, $total]);

// Clear all order sessions
unset($_SESSION['cart']);
unset($_SESSION['order_ready']);
unset($_SESSION['order_total']);

echo "<h3>✅ Payment Successful! Your order is complete.</h3><a href='products.php'>Return to Store</a>";
?>
