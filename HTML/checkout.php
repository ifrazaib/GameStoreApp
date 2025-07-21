<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login if user is not authenticated
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$pdo = new PDO("mysql:host=localhost;dbname=game_store", "root", "");
$cart = $pdo->query("SELECT * FROM cart_items WHERE user_id = $user_id")->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary | GameStore</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #6c5ce7;
            --secondary: #a29bfe;
            --success: #00b894;
            --danger: #d63031;
            --light: #f8f9fa;
            --dark: #2d3436;
            --white: #ffffff;
            --shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 40px 20px;
            color: var(--dark);
        }
        
        .summary-container {
            max-width: 700px;
            margin: 0 auto;
            background: var(--white);
            border-radius: 16px;
            box-shadow: var(--shadow);
            overflow: hidden;
            animation: fadeIn 0.6s ease-out;
            position: relative;
        }
        
        .summary-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: var(--white);
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .summary-header h2 {
            font-size: 28px;
            font-weight: 600;
            position: relative;
            z-index: 2;
        }
        
        .summary-header::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        
        .summary-header::after {
            content: "";
            position: absolute;
            bottom: -80px;
            left: -30px;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        
        .summary-content {
            padding: 30px;
        }
        
        .cart-items {
            margin-bottom: 30px;
        }
        
        .cart-item {
            display: flex;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        
        .cart-item:hover {
            transform: translateX(5px);
        }
        
        .item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            background: #f1f1f1;
            margin-right: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 24px;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-title {
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .item-price {
            color: var(--primary);
            font-weight: 600;
        }
        
        .item-quantity {
            background: var(--light);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 14px;
        }
        
        .total-section {
            background: rgba(108, 92, 231, 0.05);
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
            text-align: right;
        }
        
        .total-label {
            font-size: 16px;
            color: var(--dark);
        }
        
        .total-amount {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
            margin-top: 5px;
        }
        
        .actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .btn-confirm {
            background: var(--success);
            color: var(--white);
        }
        
        .btn-confirm:hover {
            background: #00997a;
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(0, 184, 148, 0.3);
        }
        
        .btn-cancel {
            background: var(--danger);
            color: var(--white);
        }
        
        .btn-cancel:hover {
            background: #c02a2a;
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(214, 48, 49, 0.3);
        }
        
        .empty-cart {
            text-align: center;
            padding: 50px 0;
        }
        
        .empty-cart i {
            font-size: 60px;
            color: var(--secondary);
            margin-bottom: 20px;
        }
        
        .empty-cart p {
            font-size: 18px;
            margin-bottom: 30px;
        }
        
        .btn-continue {
            background: var(--primary);
            color: var(--white);
            text-decoration: none;
        }
        
        .btn-continue:hover {
            background: #5a4bc2;
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(108, 92, 231, 0.3);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @media (max-width: 600px) {
            .summary-header {
                padding: 20px;
            }
            
            .summary-content {
                padding: 20px;
            }
            
            .cart-item {
                flex-direction: column;
                text-align: center;
            }
            
            .item-image {
                margin-right: 0;
                margin-bottom: 15px;
            }
            
            .actions {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="summary-container">
        <div class="summary-header">
            <h2>Your Order Summary</h2>
        </div>
        
        <div class="summary-content">
            <?php if (empty($cart)): ?>
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Your cart is empty</h3>
                    <p>Looks like you haven't added any games to your cart yet.</p>
                    <a href="store.php" class="btn btn-continue">
                        <i class="fas fa-gamepad"></i> Continue Shopping
                    </a>
                </div>
            <?php else: ?>
                <div class="cart-items">
                    <?php foreach ($cart as $item): ?>
                        <div class="cart-item">
                            <div class="item-image">
                                <i class="fas fa-gamepad"></i>
                            </div>
                            <div class="item-details">
                                <h3 class="item-title"><?= htmlspecialchars($item['title']) ?></h3>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span class="item-price">$<?= number_format($item['price'], 2) ?></span>
                                    <span class="item-quantity">Qty: <?= $item['quantity'] ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="total-section">
                    <p class="total-label">Total Amount</p>
                    <p class="total-amount">$<?= number_format($total, 2) ?></p>
                </div>
                
                <div class="actions">
                    <form action="confirm_order.php" method="post">
                        <button type="submit" class="btn btn-confirm">
                            <i class="fas fa-check-circle"></i> Confirm Order
                        </button>
                    </form>
                    <form action="cancel_order.php" method="post">
                        <button type="submit" class="btn btn-cancel">
                            <i class="fas fa-times-circle"></i> Cancel Order
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Add some interactive animations
        document.addEventListener('DOMContentLoaded', function() {
            const items = document.querySelectorAll('.cart-item');
            items.forEach((item, index) => {
                item.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>
</body>
</html>