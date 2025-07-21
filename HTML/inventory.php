<?php
session_start();

$pdo = new PDO("mysql:host=localhost;dbname=game_store", "root", "");

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM games WHERE id = ?")->execute([$id]);
    header("Location: inventory.php");
    exit();
}

// Handle add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO games (title, description, price, category, quantity) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['title'], $_POST['description'], $_POST['price'], $_POST['category'], $_POST['quantity']]);
}

// Handle edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $stmt = $pdo->prepare("UPDATE games SET title = ?, description = ?, price = ?, category = ?, quantity = ? WHERE id = ?");
    $stmt->execute([$_POST['title'], $_POST['description'], $_POST['price'], $_POST['category'], $_POST['quantity'], $_POST['edit_id']]);
}

$games = $pdo->query("SELECT * FROM games")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management | GameStore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4a6bff;
            --primary-light: rgba(74, 107, 255, 0.1);
            --primary-dark: #3a56cc;
            --danger: #d63031;
            --danger-light: rgba(214, 48, 49, 0.1);
            --success: #00b894;
            --success-light: rgba(0, 184, 148, 0.1);
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
        }

        h3 {
            color: var(--dark);
            margin: 20px 0 15px;
            font-size: 22px;
        }

        .form-container {
            background: var(--light);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        input, button {
            padding: 12px 15px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid var(--light-gray);
            font-size: 16px;
            transition: all 0.3s;
        }

        input {
            width: 100%;
            max-width: 500px;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        button {
            background-color: var(--primary);
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        button:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
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

        .actions {
            display: flex;
            gap: 10px;
        }

        .edit-btn {
            background-color: var(--success);
            padding: 8px 12px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .edit-btn:hover {
            background-color: #00a884;
            transform: translateY(-2px);
        }

        .delete-btn {
            background-color: var(--danger);
            padding: 8px 12px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .delete-btn:hover {
            background-color: #c02a2a;
            transform: translateY(-2px);
        }

        .price {
            font-weight: 600;
            color: var(--success);
        }

        .quantity {
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            th, td {
                padding: 10px 8px;
                font-size: 14px;
            }
            
            .actions {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-gamepad"></i> Inventory Management</h2>

        <div class="form-container">
            <h3><i class="fas fa-plus-circle"></i> Add New Game</h3>
            <form method="post">
                <input type="text" name="title" placeholder="Title" required>
                <input type="text" name="description" placeholder="Description">
                <input type="number" name="price" placeholder="Price" step="0.01" required>
                <input type="text" name="category" placeholder="Category">
                <input type="number" name="quantity" placeholder="Quantity">
                <button type="submit" name="add"><i class="fas fa-save"></i> Add Game</button>
            </form>
        </div>

        <h3><i class="fas fa-list"></i> Current Games</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($games as $game): ?>
                    <tr>
                        <td><?= $game['id'] ?></td>
                        <td><?= htmlspecialchars($game['title']) ?></td>
                        <td class="price">$<?= number_format($game['price'], 2) ?></td>
                        <td class="quantity"><?= $game['quantity'] ?></td>
                        <td class="actions">
                            <a href="inventory.php?edit=<?= $game['id'] ?>" class="edit-btn">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="inventory.php?delete=<?= $game['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this game?')">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (isset($_GET['edit'])):
            $game_id = $_GET['edit'];
            $stmt = $pdo->prepare("SELECT * FROM games WHERE id = ?");
            $stmt->execute([$game_id]);
            $game = $stmt->fetch(PDO::FETCH_ASSOC);
        ?>
        <div class="form-container" style="margin-top: 30px;">
            <h3><i class="fas fa-edit"></i> Edit Game</h3>
            <form method="post">
                <input type="hidden" name="edit_id" value="<?= $game['id'] ?>">
                <input type="text" name="title" value="<?= htmlspecialchars($game['title']) ?>" required>
                <input type="text" name="description" value="<?= htmlspecialchars($game['description']) ?>">
                <input type="number" name="price" value="<?= $game['price'] ?>" step="0.01" required>
                <input type="text" name="category" value="<?= htmlspecialchars($game['category']) ?>">
                <input type="number" name="quantity" value="<?= $game['quantity'] ?>">
                <button type="submit"><i class="fas fa-save"></i> Update Game</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>