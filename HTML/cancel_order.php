<?php
session_start();
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

    // Check if already in cart
    $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE game_id = :game_id");
    $stmt->execute([':game_id' => $gameId]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Update quantity if exists
        $newQty = $existing['quantity'] + $quantity;
        $update = $pdo->prepare("UPDATE cart_items SET quantity = :quantity WHERE game_id = :game_id");
        $update->execute([':quantity' => $newQty, ':game_id' => $gameId]);
    } else {
        // Insert new item
        $insert = $pdo->prepare("INSERT INTO cart_items (game_id, title, price, quantity) VALUES (:game_id, :title, :price, :quantity)");
        $insert->execute([
            ':game_id' => $gameId,
            ':title' => $title,
            ':price' => $price,
            ':quantity' => $quantity
        ]);
    }

    header("Location: products.php");
    exit();

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
