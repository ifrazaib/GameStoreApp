<?php
session_start();

// Make sure the user is logged in and has a user_id
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

    $gameId = $_POST['game_id'];
    $title = $_POST['title'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    // Check if already in cart for this user
    $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE game_id = :game_id AND user_id = :user_id");
    $stmt->execute([':game_id' => $gameId, ':user_id' => $userId]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Update quantity
        $newQty = $existing['quantity'] + $quantity;
        $stmt = $pdo->prepare("UPDATE cart_items SET quantity = :quantity WHERE game_id = :game_id AND user_id = :user_id");
        $stmt->execute([':quantity' => $newQty, ':game_id' => $gameId, ':user_id' => $userId]);
    } else {
        // Insert new item
        $stmt = $pdo->prepare("INSERT INTO cart_items (game_id, title, price, quantity, user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$gameId, $title, $price, $quantity, $userId]);
    }

    // Update game stock
    $stmt = $pdo->prepare("UPDATE games SET quantity = quantity - :q WHERE id = :id AND quantity >= :q");
    $stmt->execute([':q' => $quantity, ':id' => $gameId]);

    header("Location: cart.php?highlight=$gameId");
    exit();

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
