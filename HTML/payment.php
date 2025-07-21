<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php'; // Assuming this contains your PDO connection

$user_id = $_SESSION['user_id'];
$pdo = new PDO("mysql:host=localhost;dbname=game_store", "root", "");

// Get cart total
$stmt = $pdo->prepare("SELECT SUM(price * quantity) as total FROM orders WHERE user_id = ?");
$stmt->execute([$user_id]);
$total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'];
    $status = 'pending'; // Default status
    $error = "";

    // For card payments, validate and process
    if ($payment_method === 'card') {
        $card_number = str_replace(' ', '', $_POST['card_number']);
        $card_name = $_POST['card_name'];
        $expiry = $_POST['expiry'];
        $cvv = $_POST['cvv'];

        // Simple validation
        if (strlen($card_number) !== 16 || !is_numeric($card_number)) {
            $error = "Invalid card number";
        } elseif (strlen($cvv) !== 3 || !is_numeric($cvv)) {
            $error = "Invalid CVV";
        } else {
            $status = 'completed';
            $save_card = isset($_POST['save_card']) ? 1 : 0;

            // Save payment entry
            $stmt = $pdo->prepare("INSERT INTO payment (user_id, amount, method, status, created_at) 
                                   VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$user_id, $total, 'card', $status]);

        }
    } elseif ($payment_method === 'bank') {
        $stmt = $pdo->prepare("INSERT INTO payment (user_id, amount, method, status, created_at) 
                               VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$user_id, $total, 'bank transfer', $status]);
    } elseif ($payment_method === 'cod') {
        $stmt = $pdo->prepare("INSERT INTO payment (user_id, amount, method, status, created_at) 
                               VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$user_id, $total, 'COD', $status]);
    }
    $cartItems = $pdo->prepare("SELECT * FROM cart_items WHERE user_id = ?");
$cartItems->execute([$user_id]);
$items = $cartItems->fetchAll(PDO::FETCH_ASSOC);

foreach ($items as $item) {
    $insertOrder = $pdo->prepare("INSERT INTO orders (game_id, title, price, quantity,total, user_id, status)
                                  VALUES (?, ?, ?, ?, ?, 'Processing')");
    $insertOrder->execute([
        $item['game_id'],
        $item['title'],
        $item['price'],
        $item['quantity'],
        $item['total'],
        $user_id
    ]);
}

    // Clear cart and redirect if no error
    if (empty($error)) {
        $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?")->execute([$user_id]);
        header("Location: order_tracking.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment | GameStore</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #6c5ce7;
            --secondary: #a29bfe;
            --success: #00b894;
            --danger: #d63031;
            --warning: #fdcb6e;
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
        
        .payment-container {
            max-width: 800px;
            margin: 0 auto;
            background: var(--white);
            border-radius: 16px;
            box-shadow: var(--shadow);
            overflow: hidden;
            animation: fadeIn 0.6s ease-out;
        }
        
        .payment-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: var(--white);
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .payment-header h2 {
            font-size: 28px;
            font-weight: 600;
            position: relative;
            z-index: 2;
        }
        
        .payment-content {
            display: flex;
            flex-wrap: wrap;
        }
        
        .order-summary {
            flex: 1;
            min-width: 300px;
            padding: 30px;
            background: rgba(108, 92, 231, 0.05);
        }
        
        .payment-options {
            flex: 2;
            min-width: 400px;
            padding: 30px;
        }
        
        .payment-method {
            margin-bottom: 30px;
        }
        
        .method-tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .tab.active {
            border-bottom: 3px solid var(--primary);
            color: var(--primary);
            font-weight: 500;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        input, select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
        }
        
        input:focus, select:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.2);
        }
        
        .card-element {
            background: var(--white);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        
        .card-icons {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .card-icon {
            width: 40px;
            height: 25px;
            background: #eee;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #555;
        }
        
        .row {
            display: flex;
            gap: 15px;
        }
        
        .row .form-group {
            flex: 1;
        }
        
        .save-card {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
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
            width: 100%;
        }
        
        .btn-pay {
            background: var(--success);
            color: var(--white);
        }
        
        .btn-pay:hover {
            background: #00997a;
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(0, 184, 148, 0.3);
        }
        
        .btn-cod {
            background: var(--warning);
            color: var(--dark);
        }
        
        .btn-cod:hover {
            background: #f5b933;
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(253, 203, 110, 0.3);
        }
        
        .btn-bank {
            background: var(--primary);
            color: var(--white);
        }
        
        .btn-bank:hover {
            background: #5a4bc2;
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(108, 92, 231, 0.3);
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .order-total {
            font-size: 20px;
            font-weight: 700;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid var(--primary);
            display: flex;
            justify-content: space-between;
        }
        
        .error {
            color: var(--danger);
            background: rgba(214, 48, 49, 0.1);
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }
        
        .error.show {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @media (max-width: 768px) {
            .payment-content {
                flex-direction: column;
            }
            
            .order-summary, .payment-options {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-header">
            <h2>Complete Your Purchase</h2>
        </div>
        
        <div class="payment-content">
            <div class="order-summary">
                <h3>Order Summary</h3>
                
                <?php 
                $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($cart_items as $item): ?>
                    <div class="order-item">
                        <span><?= htmlspecialchars($item['title']) ?> × <?= $item['quantity'] ?></span>
                        <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                    </div>
                <?php endforeach; ?>
                
                <div class="order-total">
                    <span>Total</span>
                    <span>$<?= number_format($total, 2) ?></span>
                </div>
            </div>
            
            <div class="payment-options">
                <?php if (isset($error)): ?>
                    <div class="error show"><?= $error ?></div>
                <?php endif; ?>
                
                <div class="payment-method">
                    <div class="method-tabs">
                        <div class="tab active" data-tab="card">Credit Card</div>
                        <div class="tab" data-tab="bank">Bank Transfer</div>
                        <div class="tab" data-tab="cod">Cash on Delivery</div>
                    </div>
                    
                    <form id="payment-form" method="POST">
                        <input type="hidden" name="payment_method" id="payment_method" value="card">
                        
                        <!-- Credit Card Tab -->
                        <div class="tab-content active" id="card-tab">
                            <div class="card-element">
                                <div class="card-icons">
                                    <div class="card-icon"><i class="fab fa-cc-visa"></i></div>
                                    <div class="card-icon"><i class="fab fa-cc-mastercard"></i></div>
                                    <div class="card-icon"><i class="fab fa-cc-amex"></i></div>
                                    <div class="card-icon"><i class="fab fa-cc-discover"></i></div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="card_number">Card Number</label>
                                    <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                                </div>
                                
                                <div class="form-group">
                                    <label for="card_name">Name on Card</label>
                                    <input type="text" id="card_name" name="card_name" placeholder="John Doe">
                                </div>
                                
                                <div class="row">
                                    <div class="form-group">
                                        <label for="expiry">Expiry Date</label>
                                        <input type="text" id="expiry" name="expiry" placeholder="MM/YY" maxlength="5">
                                    </div>
                                    <div class="form-group">
                                        <label for="cvv">CVV</label>
                                        <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3">
                                    </div>
                                </div>
                                
                                <div class="save-card">
                                    <input type="checkbox" id="save_card" name="save_card" checked>
                                    <label for="save_card">Save this card for future payments</label>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-pay">
                                <i class="fas fa-lock"></i> Pay $<?= number_format($total, 2) ?>
                            </button>
                        </div>
                    </form>
                    
                    <!-- Bank Transfer Tab Content -->
                    <div class="tab-content" id="bank-tab">
                        <div class="form-group">
                            <p>Please transfer the amount of <strong>$<?= number_format($total, 2) ?></strong> to our bank account:</p>
                            <p><strong>Bank Name:</strong> GameStore Bank</p>
                            <p><strong>Account Number:</strong> 1234567890</p>
                            <p><strong>Routing Number:</strong> 987654321</p>
                            <p>Use your order ID as the payment reference.</p>
                        </div>
                        <button type="button" class="btn btn-bank" id="confirm-bank">
                            <i class="fas fa-university"></i> Confirm Bank Transfer
                        </button>
                    </div>
                    
                    <!-- COD Tab Content -->
                    <div class="tab-content" id="cod-tab">
                        <div class="form-group">
                            <p>Pay with cash when your order is delivered.</p>
                            <p>An additional $2.00 service charge will be applied for COD orders.</p>
                        </div>
                        <button type="button" class="btn btn-cod" id="confirm-cod">
                            <i class="fas fa-money-bill-wave"></i> Confirm Cash on Delivery
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Tab switching
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs and contents
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked tab
                tab.classList.add('active');
                const tabId = tab.getAttribute('data-tab');
                document.getElementById(tabId + '-tab').classList.add('active');
                document.getElementById('payment_method').value = tabId;
            });
        });
        
        // Format card number
        document.getElementById('card_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '');
            if (value.length > 0) {
                value = value.match(new RegExp('.{1,4}', 'g')).join(' ');
            }
            e.target.value = value;
        });
        
        // Format expiry date
        document.getElementById('expiry').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
        
        // Bank transfer confirmation
        document.getElementById('confirm-bank').addEventListener('click', function() {
            document.getElementById('payment_method').value = 'bank';
            document.getElementById('payment-form').submit();
        });
        
        // COD confirmation
        document.getElementById('confirm-cod').addEventListener('click', function() {
            document.getElementById('payment_method').value = 'cod';
            document.getElementById('payment-form').submit();
        });
    </script>
</body>
</html>