<?php
$pdo = new PDO("mysql:host=localhost;dbname=game_store", "root", "");

$gameId = $_POST['game_id'];
$action = $_POST['action'];

$stmt = $pdo->prepare("SELECT quantity FROM cart_items WHERE game_id = ?");
$stmt->execute([$gameId]);
$item = $stmt->fetch();

if ($item) {
    $qty = $item['quantity'];

    if ($action == 'increase') {
        $pdo->prepare("UPDATE cart_items SET quantity = quantity + 1 WHERE game_id = ?")->execute([$gameId]);
    } elseif ($action == 'decrease' && $qty > 1) {
        $pdo->prepare("UPDATE cart_items SET quantity = quantity - 1 WHERE game_id = ?")->execute([$gameId]);
    } elseif ($action == 'remove') {
        $pdo->prepare("DELETE FROM cart_items WHERE game_id = ?")->execute([$gameId]);
    }
}

header("Location: cart.php");
?>
