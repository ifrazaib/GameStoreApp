<?php
// Database connection
$host = 'localhost';
$dbname = 'game_store';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get parameters
$category = $_GET['category'] ?? 'all';
$searchTerm = $_GET['search'] ?? '';
$highlightId = $_GET['highlight'] ?? null;

// Build query
$query = "SELECT id, title, price, category, quantity FROM games";
$params = [];

if ($category !== 'all') {
    $query .= " WHERE category = :category";
    $params[':category'] = $category;
}

if (!empty($searchTerm)) {
    $query .= ($category !== 'all' ? " AND" : " WHERE");
    $query .= " (title LIKE :search OR description LIKE :search)";
    $params[':search'] = "%$searchTerm%";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Games Listing</title>
    <link rel="stylesheet" href="../CSS/products.css">
</head>
<body data-highlight-id="<?= htmlspecialchars($highlightId) ?>">
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

    <div class="products-container">
        <h1 class="products-title">
            <?php 
            if (!empty($searchTerm)) {
                echo "Search Results for: " . htmlspecialchars($searchTerm);
            } else {
                echo $category === 'all' ? "All Games" : htmlspecialchars($category) . " Games";
            }
            ?>
        </h1>

        <div class="products-grid">
            <?php if (empty($games)): ?>
                <p class="no-games">No games found matching your criteria.</p>
            <?php else: ?>
                <?php foreach ($games as $game): ?>
                    <div class="product-card <?= $game['id'] == $highlightId ? 'highlighted' : '' ?>" id="game-<?= $game['id'] ?>">
                        <h3><?= htmlspecialchars($game['title']) ?></h3>
                        <p class="product-price">$<?= number_format($game['price'], 2) ?></p>
                        <span class="product-category"><?= htmlspecialchars($game['category']) ?></span>
                        <?php
                            $stockClass = 'in-stock';
                            $stockText = 'In Stock';
                            if ($game['quantity'] == 0) {
                                $stockClass = 'out-of-stock';
                                $stockText = 'Out of Stock';
                            } elseif ($game['quantity'] < 5) {
                                $stockClass = 'low-stock';
                                $stockText = 'Only ' . $game['quantity'] . ' left';
                            }
                        ?>
                        <p class="product-stock <?= $stockClass ?>"><?= $stockText ?></p>
                        <div class="product-buttons">
                            <a href="product_details.php?id=<?= $game['id'] ?>" class="view-btn">View Details</a>
                            <form action="add_to_cart.php" method="post" class="cart-form">
    <input type="hidden" name="game_id" value="<?= $game['id'] ?>">
    <input type="hidden" name="title" value="<?= htmlspecialchars($game['title']) ?>">
    <input type="hidden" name="price" value="<?= $game['price'] ?>">
    <input type="hidden" name="quantity" value="1"> <!-- default 1 -->
    <button type="submit" class="add-to-cart" <?= $game['quantity'] == 0 ? 'disabled' : '' ?>>
        <?= $game['quantity'] == 0 ? 'Out of Stock' : 'Add to Cart' ?>
    </button>
</form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="../JS/products.js"></script>
</body>
</html>
