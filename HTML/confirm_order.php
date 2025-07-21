<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

$host = 'localhost';
$dbname = 'game_store';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Start transaction
    $pdo->beginTransaction();

    // Fetch all cart items for the logged-in user
    $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE user_id = ?");
    $stmt->execute([$userId]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($cartItems)) {
        echo "<p>Your cart is empty. <a href='products.php'>Go shopping</a></p>";
        exit();
    }

    // Calculate total amount
$totalAmount = 0;
foreach ($cartItems as $item) {
    $totalAmount += $item['price'] * $item['quantity'];
}

// Insert each item along with total amount
$stmt = $pdo->prepare("INSERT INTO orders (user_id, game_id, title, price, quantity, total) VALUES (?, ?, ?, ?, ?, ?)");
foreach ($cartItems as $item) {
    $stmt->execute([
        $userId,
        $item['game_id'],
        $item['title'],
        $item['price'],
        $item['quantity'],
        $totalAmount // Same total for each row
    ]);
}


    // Clear the user's cart after inserting into orders
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Commit the transaction
    $pdo->commit();

} catch (PDOException $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation | GameStore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6c5ce7;
            --primary-dark: #5649c0;
            --secondary: #00cec9;
            --dark: #2d3436;
            --light: #f5f6fa;
            --success: #00b894;
            --warning: #fdcb6e;
            --danger: #d63031;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: var(--dark);
            line-height: 1.6;
        }
        
        .confirmation-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            animation: fadeIn 0.6s ease-out;
        }
        
        .confirmation-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 10px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        h1 {
            color: var(--primary);
            text-align: center;
            margin-bottom: 20px;
            font-size: 2.5rem;
            position: relative;
            padding-bottom: 15px;
        }
        
        h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-radius: 2px;
        }
        
        .confirmation-message {
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.1rem;
            color: #555;
        }
        
        .confirmation-icon {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .checkmark {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: var(--success);
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleIn 0.5s ease-out;
        }
        
        @keyframes scaleIn {
            0% { transform: scale(0); }
            80% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .checkmark i {
            color: white;
            font-size: 40px;
        }
        
        .confirmation-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 0.9em;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .confirmation-table thead tr {
            background: linear-gradient(90deg, var(--primary), var(--primary-dark));
            color: white;
            text-align: left;
            font-weight: 600;
        }
        
        .confirmation-table th,
        .confirmation-table td {
            padding: 15px 20px;
            text-align: left;
        }
        
        .confirmation-table tbody tr {
            border-bottom: 1px solid #eee;
            transition: all 0.2s;
        }
        
        .confirmation-table tbody tr:nth-of-type(even) {
            background-color: #f9f9f9;
        }
        
        .confirmation-table tbody tr:last-of-type {
            border-bottom: 2px solid var(--primary);
        }
        
        .confirmation-table tbody tr:hover {
            background-color: #f1f1f1;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f8f9fa !important;
        }
        
        .total-row td {
            color: var(--primary-dark);
        }
        
        .button-group {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        
        .payment-button, .back-button {
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            text-align: center;
            min-width: 200px;
        }
        
        .payment-button {
            background: linear-gradient(90deg, var(--primary), var(--primary-dark));
            color: white;
            box-shadow: 0 4px 15px rgba(108, 92, 231, 0.4);
        }
        
        .payment-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(108, 92, 231, 0.6);
        }
        
        .back-button {
            background: white;
            color: var(--primary);
            border: 2px solid var(--primary);
        }
        
        .back-button:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(108, 92, 231, 0.4);
        }
        
        .order-details {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid var(--primary);
        }
        
        .order-details h3 {
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .order-number {
            font-weight: bold;
            color: var(--primary-dark);
            font-size: 1.2rem;
        }
        
        .thank-you-message {
            text-align: center;
            margin-top: 30px;
            font-style: italic;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .confirmation-container {
                margin: 20px;
                padding: 20px;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .button-group {
                flex-direction: column;
                align-items: center;
            }
            
            .payment-button, .back-button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="confirmation-icon">
            <div class="checkmark">
                <i class="fas fa-check"></i>
            </div>
        </div>
        
        <h1>Order Confirmed!</h1>
        
        <p class="confirmation-message">
            Thank you for your purchase. Your order has been successfully processed and is being prepared.
        </p>
        
        <div class="order-details">
            <h3>Order Summary</h3>
            <p>Order Number: <span class="order-number">#<?= rand(100000, 999999) ?></span></p>
            <p>Date: <?= date('F j, Y') ?></p>
        </div>
        
        <table class="confirmation-table">
            <thead>
                <tr>
                    <th>Game Title</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                foreach ($cartItems as $item): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['title']) ?></td>
                    <td>$<?= number_format($item['price'], 2) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>$<?= number_format($subtotal, 2) ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="3"><strong>Total</strong></td>
                    <td><strong>$<?= number_format($total, 2) ?></strong></td>
                </tr>
            </tbody>
        </table>
        
        <div class="button-group">
            <a href="payment.php" class="payment-button">
                <i class="fas fa-credit-card"></i> Complete Payment
            </a>
            <a href="products.php" class="back-button">
                <i class="fas fa-shopping-bag"></i> Continue Shopping
            </a>
        </div>
        
        <p class="thank-you-message">
            Thank you for shopping with us! We've sent a confirmation email with your order details.
        </p>
    </div>
</body>
</html>