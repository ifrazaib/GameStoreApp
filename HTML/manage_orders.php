<?php
session_start();

$pdo = new PDO("mysql:host=localhost;dbname=game_store", "root", "");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$new_status, $order_id]);
}

$orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management | GameStore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4a6bff;
            --primary-light: rgba(74, 107, 255, 0.1);
            --processing: #fdcb6e;
            --shipped: #0984e3;
            --delivered: #00b894;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --light: #f8f9fa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fb;
            color: var(--dark);
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        h2 {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 28px;
            border-bottom: 2px solid var(--light-gray);
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--light-gray);
        }

        th {
            background-color: var(--primary);
            color: white;
            font-weight: 500;
        }

        tr:nth-child(even) {
            background-color: var(--light);
        }

        tr:hover {
            background-color: var(--primary-light);
        }

        select {
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid var(--light-gray);
            font-size: 14px;
            margin-right: 8px;
        }

        button {
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            background-color: var(--primary);
            color: white;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
        }

        button:hover {
            background-color: #3a56cc;
            transform: translateY(-1px);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            display: inline-block;
        }

        .status-processing {
            background-color: rgba(253, 203, 110, 0.2);
            color: #a37b0e;
        }

        .status-shipped {
            background-color: rgba(9, 132, 227, 0.2);
            color: #095ea3;
        }

        .status-delivered {
            background-color: rgba(0, 184, 148, 0.2);
            color: #007a5e;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
                overflow-x: auto;
            }
            
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            
            th, td {
                padding: 10px 8px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-shopping-cart"></i> Order Management</h2>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>User ID</th>
                    <th>Qty</th>
                    <th>Update Status</th>
                    <th>Current Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td><?= htmlspecialchars($order['title']) ?></td>
                        <td><?= $order['user_id'] ?></td>
                        <td><?= $order['quantity'] ?></td>
                        <td>
                            <form method="post" style="display: flex; align-items: center;">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <select name="status">
                                    <option value="Processing" <?= $order['status'] === 'Processing' ? 'selected' : '' ?>>Processing</option>
                                    <option value="Shipped" <?= $order['status'] === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                    <option value="Delivered" <?= $order['status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                </select>
                                <button type="submit"><i class="fas fa-sync-alt"></i> Update</button>
                            </form>
                        </td>
                        <td>
                            <span class="status-badge status-<?= strtolower($order['status']) ?>">
                                <?= $order['status'] ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>