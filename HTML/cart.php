<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

$pdo = new PDO("mysql:host=localhost;dbname=game_store", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Modified query to remove the image_url column
$stmt = $pdo->prepare("
    SELECT c.game_id, c.quantity, g.title, g.price
    FROM cart_items c
    JOIN games g ON c.game_id = g.id
    WHERE c.user_id = ?
");
$stmt->execute([$userId]);
$cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total
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
    <title>Your Cart | GameStore</title>
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
            --gray: #dfe6e9;
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
            padding: 20px;
        }
        
        .cart-container {
            max-width: 900px;
            margin: 30px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: fadeIn 0.6s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .cart-header {
            background: linear-gradient(90deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .cart-header h2 {
            font-size: 2rem;
            font-weight: 600;
        }
        
        .cart-body {
            padding: 30px;
        }
        
        .empty-cart {
            text-align: center;
            padding: 50px 0;
        }
        
        .empty-cart i {
            font-size: 60px;
            color: var(--gray);
            margin-bottom: 20px;
        }
        
        .empty-cart p {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 20px;
        }
        
        .empty-cart a {
            display: inline-block;
            padding: 12px 30px;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .empty-cart a:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 92, 231, 0.4);
        }
        
        .cart-item {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #eee;
            transition: all 0.3s;
        }
        
        .cart-item:hover {
            background-color: #f9f9f9;
        }
        
        .game-icon {
            width: 60px;
            height: 60px;
            background: var(--primary);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 1.5rem;
        }
        
        .game-info {
            flex: 1;
        }
        
        .game-title {
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 5px;
            color: var(--dark);
        }
        
        .game-price {
            color: var(--primary-dark);
            font-weight: 500;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }
        
        .quantity-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .quantity-btn:hover {
            background: var(--primary-dark);
            transform: scale(1.1);
        }
        
        .quantity-value {
            margin: 0 15px;
            font-weight: 500;
            min-width: 20px;
            text-align: center;
        }
        
        .remove-btn {
            background: none;
            border: none;
            color: var(--danger);
            cursor: pointer;
            font-size: 1.2rem;
            margin-left: 20px;
            transition: all 0.2s;
        }
        
        .remove-btn:hover {
            transform: scale(1.2);
        }
        
        .cart-summary {
            padding: 20px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .total-label {
            font-size: 1.2rem;
            font-weight: 500;
        }
        
        .total-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-dark);
        }
        
        .checkout-btn {
            display: block;
            width: 100%;
            padding: 15px;
            background: linear-gradient(90deg, var(--success), #00a884);
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .checkout-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 184, 148, 0.4);
        }
        
        .continue-shopping {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .continue-shopping:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .subtotal {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .cart-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .game-icon {
                margin-bottom: 15px;
                margin-right: 0;
            }
            
            .quantity-controls {
                margin-top: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="cart-container">
        <div class="cart-header">
            <h2><i class="fas fa-shopping-cart"></i> Your Shopping Cart</h2>
        </div>
        
        <div class="cart-body">
            <?php if (count($cart) > 0): ?>
                <?php foreach ($cart as $item): ?>
                    <div class="cart-item">
                        <div class="game-icon">
                            <i class="fas fa-gamepad"></i>
                        </div>
                        
                        <div class="game-info">
                            <h3 class="game-title"><?= htmlspecialchars($item['title']) ?></h3>
                            <p class="game-price">$<?= number_format($item['price'], 2) ?></p>
                            
                            <div class="quantity-controls">
                                <form method="post" action="update_cart.php" class="inline-form">
                                    <input type="hidden" name="game_id" value="<?= $item['game_id'] ?>">
                                    <button type="submit" name="action" value="decrease" class="quantity-btn">-</button>
                                </form>
                                
                                <span class="quantity-value"><?= $item['quantity'] ?></span>
                                
                                <form method="post" action="update_cart.php" class="inline-form">
                                    <input type="hidden" name="game_id" value="<?= $item['game_id'] ?>">
                                    <button type="submit" name="action" value="increase" class="quantity-btn">+</button>
                                </form>
                                
                                <form method="post" action="update_cart.php" class="inline-form">
                                    <input type="hidden" name="game_id" value="<?= $item['game_id'] ?>">
                                    <button type="submit" name="action" value="remove" class="remove-btn" title="Remove item">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="item-total">
                            $<?= number_format($item['price'] * $item['quantity'], 2) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Your cart is empty</p>
                    <a href="products.php">Browse Games</a>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if (count($cart) > 0): ?>
        <div class="cart-summary">
            <div class="subtotal">
                <span>Subtotal:</span>
                <span>$<?= number_format($total, 2) ?></span>
            </div>
            <div class="subtotal">
                <span>Shipping:</span>
                <span>FREE</span>
            </div>
            
            <div class="total-row">
                <span class="total-label">Total:</span>
                <span class="total-amount">$<?= number_format($total, 2) ?></span>
            </div>
            
            <a href="checkout.php" class="checkout-btn">
                <i class="fas fa-lock"></i> Proceed to Checkout
            </a>
            
            <a href="products.php" class="continue-shopping">
                <i class="fas fa-arrow-left"></i> Continue Shopping
            </a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>