<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$pdo = new PDO("mysql:host=localhost;dbname=game_store", "root", "");
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT id, game_id, title, price, quantity, total, user_id,status
                       FROM orders 
                       WHERE user_id = ? 
                       ORDER BY id DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group orders by order ID (assuming multiple items can be in one order)
$grouped_orders = [];
foreach ($orders as $order) {
    if (!isset($grouped_orders[$order['id']])) {
        $grouped_orders[$order['id']] = [
            'id' => $order['id'],
            'items' => [],
            'status' => $order['status'],
            'order_date' => $order['status'],
            'total' => 0
        ];
    }
    $grouped_orders[$order['id']]['items'][] = $order;
    $grouped_orders[$order['id']]['total'] += $order['price'] * $order['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Tracking | GameStore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #6c5ce7;
            --secondary: #a29bfe;
            --dark: #2d3436;
            --light: #f5f6fa;
            --success: #00b894;
            --processing: #0984e3;
            --cancelled: #d63031;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 40px 20px;
            color: var(--dark);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        header {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        h1 {
            font-size: 28px;
            font-weight: 600;
        }
        
        .order-count {
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
        }
        
        .content {
            padding: 30px;
        }
        
        .order-card {
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            margin-bottom: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .order-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .order-header {
            background: var(--light);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }
        
        .order-header h3 {
            font-size: 18px;
            color: var(--primary);
        }
        
        .order-meta {
            display: flex;
            gap: 20px;
        }
        
        .order-meta div {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
        }
        
        .order-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-processing {
            background: rgba(9, 132, 227, 0.1);
            color: var(--processing);
        }
        
        .status-completed {
            background: rgba(0, 184, 148, 0.1);
            color: var(--success);
        }
        
        .status-cancelled {
            background: rgba(214, 48, 49, 0.1);
            color: var(--cancelled);
        }
        
        .order-details {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .order-details.show {
            padding: 20px;
            max-height: 500px;
        }
        
        .game-item {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
            align-items: center;
        }
        
        .game-item:last-child {
            border-bottom: none;
        }
        
        .game-img {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            background: #eee;
            margin-right: 20px;
            background-size: cover;
            background-position: center;
        }
        
        .game-info {
            flex: 1;
        }
        
        .game-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .game-price {
            color: #666;
            font-size: 14px;
        }
        
        .game-qty {
            color: #666;
            font-size: 14px;
        }
        
        .order-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: var(--light);
            border-radius: 0 0 12px 12px;
            margin-top: 15px;
        }
        
        .order-total {
            font-weight: 600;
            font-size: 18px;
        }
        
        .order-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a4bd1;
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid #ddd;
        }
        
        .btn-outline:hover {
            background: #f5f5f5;
        }
        
        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }
        
        .empty-state i {
            font-size: 60px;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            font-size: 22px;
            margin-bottom: 10px;
            color: #555;
        }
        
        .empty-state p {
            color: #888;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .order-meta {
                flex-wrap: wrap;
                gap: 10px;
            }
            
            .game-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .game-img {
                margin-bottom: 10px;
            }
            
            .order-summary {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .order-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-gamepad"></i> Your Orders</h1>
            <div class="order-count"><?= count($grouped_orders) ?> order(s)</div>
        </header>
        
        <div class="content">
            <?php if (count($grouped_orders) > 0): ?>
                <?php foreach ($grouped_orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header" onclick="toggleOrderDetails(this)">
                            <h3>Order #<?= $order['id'] ?></h3>
                            <div class="order-meta">
                                <div><i class="far fa-calendar-alt"></i> <?= date('M d, Y', strtotime($order['order_date'])) ?></div>
                                <div><i class="fas fa-receipt"></i> $<?= number_format($order['total'], 2) ?></div>
                                <div class="order-status status-<?= strtolower($order['status']) ?>">
                                    <i class="fas fa-circle"></i> <?= $order['status'] ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="order-details">
                            <?php foreach ($order['items'] as $item): ?>
                                <div class="game-item">
                                    <div class="game-img" style="background-image: url('game_images/<?= $item['game_id'] ?>.jpg')"></div>
                                    <div class="game-info">
                                        <div class="game-title"><?= htmlspecialchars($item['title']) ?></div>
                                        <div class="game-price">$<?= number_format($item['price'], 2) ?></div>
                                    </div>
                                    <div class="game-qty">Qty: <?= $item['quantity'] ?></div>
                                </div>
                            <?php endforeach; ?>
                            
                            <div class="order-summary">
                                <div class="order-total">Total: $<?= number_format($order['total'], 2) ?></div>
                                <div class="order-actions">
                                    <button class="btn btn-outline"><i class="fas fa-print"></i> Invoice</button>
                                    <?php if ($order['status'] == 'Processing'): ?>
                                        <button class="btn btn-outline"><i class="fas fa-times"></i> Cancel</button>
                                    <?php endif; ?>
                                    <button class="btn btn-primary"><i class="fas fa-shopping-cart"></i> Buy Again</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h3>No Orders Yet</h3>
                    <p>You haven't placed any orders with us yet.</p>
                    <a href="store.php" class="btn btn-primary"><i class="fas fa-shopping-cart"></i> Start Shopping</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleOrderDetails(header) {
            const card = header.parentElement;
            const details = card.querySelector('.order-details');
            details.classList.toggle('show');
            
            // Rotate icon if present
            const icon = header.querySelector('.fa-chevron-down');
            if (icon) {
                icon.classList.toggle('rotate-180');
            }
        }
        
        // Add chevron icon to headers
        document.querySelectorAll('.order-header').forEach(header => {
            const icon = document.createElement('i');
            icon.className = 'fas fa-chevron-down';
            icon.style.transition = 'transform 0.3s';
            icon.style.marginLeft = '10px';
            header.appendChild(icon);
        });
    </script>
</body>
</html>