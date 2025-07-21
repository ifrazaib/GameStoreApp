<?php
// Database connection
$host = 'localhost';
$dbname = 'game_store';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$gameId = $_GET['id'] ?? null;

if (!$gameId) {
    header("Location: products.php");
    exit();
}

// Get game details
$stmt = $pdo->prepare("SELECT * FROM games WHERE id = ?");
$stmt->execute([$gameId]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
    header("Location: products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($game['title']) ?> - Game Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/product_details.css">
</head>
<body>
    <!-- Hamburger Menu -->
    <div class="menu-container">
        <div class="hamburger-menu" onclick="toggleMenu()">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </div>
        <div class="menu-content" id="menuContent">
            <a href="home.php">Home</a>
            <a href="products.php">Products</a>
            <a href="http://localhost/GAME_STORE/HTML/logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="product-details-container">
        <h1 class="product-title"><?= htmlspecialchars($game['title']) ?></h1>
        
        <div class="product-meta">
            <span class="product-price">$<?= number_format($game['price'], 2) ?></span>
            <span class="product-category"><?= htmlspecialchars($game['category']) ?></span>
            <?php
            $stockClass = 'in-stock';
            $stockText = 'In Stock: ' . $game['quantity'];
            if ($game['quantity'] == 0) {
                $stockClass = 'out-of-stock';
                $stockText = 'Out of Stock';
            } elseif ($game['quantity'] < 5) {
                $stockClass = 'low-stock';
                $stockText = 'Low Stock: ' . $game['quantity'] . ' left';
            }
            ?>
            <span class="product-stock <?= $stockClass ?>"><?= $stockText ?></span>
        </div>
        
        <div class="product-description">
            <p><?= nl2br(htmlspecialchars($game['description'])) ?></p>
        </div>
        
        <div class="product-actions">
        <form action="add_to_cart.php" method="post" class="cart-form">
    <input type="hidden" name="game_id" value="<?= $game['id'] ?>">
    <input type="hidden" name="title" value="<?= htmlspecialchars($game['title']) ?>">
    <input type="hidden" name="price" value="<?= $game['price'] ?>">
    <input type="hidden" name="quantity" value="1"> <!-- default 1 -->
    <button type="submit" class="add-to-cart" <?= $game['quantity'] == 0 ? 'disabled' : '' ?>>
        <?= $game['quantity'] == 0 ? 'Out of Stock' : 'Add to Cart' ?>
    </button>
</form>

            <a href="products.php?category=<?= urlencode($game['category']) ?>" class="back-link">Back to Games</a>
        </div>
    </div>

    <script src="../JS/product_details.js"></script>
</body>
</html>