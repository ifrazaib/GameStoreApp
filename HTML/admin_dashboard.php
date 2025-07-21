<?php
session_start();


// Set default admin name if not set
$admin_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Administrator';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | GameStore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4a6bff;
            --primary-dark: #3a56cc;
            --light: #f8f9fa;
            --dark: #212529;
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .dashboard-container {
            text-align: center;
            max-width: 600px;
            padding: 40px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .welcome-message {
            margin-bottom: 30px;
        }

        .welcome-message h1 {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--primary);
        }

        .welcome-message p {
            color: #666;
            font-size: 1rem;
        }

        .action-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 15px 30px;
            border-radius: 8px;
            border: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 1rem;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .user-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        @media (max-width: 600px) {
            .dashboard-container {
                padding: 30px 20px;
                width: 90%;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="welcome-message">
            <h1>Welcome, <?= htmlspecialchars($admin_name) ?></h1>
            <p>GameStore Administration Panel</p>
        </div>
        
        <div class="action-buttons">
            <a href="inventory.php" class="btn btn-primary">
                <i class="fas fa-gamepad"></i> Manage Inventory
            </a>
            <a href="manage_orders.php" class="btn btn-primary">
                <i class="fas fa-shopping-cart"></i> Order Status
            </a>
        </div>
        
        <div class="user-info">
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($admin_name) ?>&background=4a6bff&color=fff" alt="Admin">
            <a href="logout.php" style="color: var(--primary); text-decoration: none;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</body>
</html>