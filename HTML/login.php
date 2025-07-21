<?php
session_start();

// Database config
$host = "localhost";
$db_user = "root";
$pass = "";
$db = "game_store";

$conn = new mysqli($host, $db_user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $input_password = $_POST['password'];

    // 1. First check if it's an admin
    $admin_query = "SELECT * FROM admins_data WHERE email='$email' AND password='$input_password'";
    $admin_result = $conn->query($admin_query);

    if ($admin_result->num_rows == 1) {
        $admin = $admin_result->fetch_assoc();
        $_SESSION['user_id'] = $admin['id']; // Assuming 'id' column exists
        $_SESSION['email'] = $email;
        $_SESSION['user_type'] = 'admin';
        header("Location: admin_dashboard.php");
        exit();
    }

    // 2. If not admin, check regular users
    $user_query = "SELECT * FROM users_data WHERE email='$email' AND password='$input_password'";
    $user_result = $conn->query($user_query);

    if ($user_result->num_rows == 1) {
        $user = $user_result->fetch_assoc();
        $_SESSION['user_id'] = $user['id']; // This line was the original problem
        $_SESSION['email'] = $email;
        $_SESSION['user_type'] = 'user';
        header("Location: home.php");
        exit();
    }

    // If login fails
    echo "<script>
        if(confirm('Account not found. Would you like to register now?')) {
            window.location.href = 'register.php';
        } else {
            window.history.back();
        }
    </script>";
    exit();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - Treasure Hunt</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="../CSS/login.css" />
</head>
<body>
  <!-- Animated leaves -->
  <div class="leaves" id="leaves"></div>
  
  <!-- Game logo -->
  <div class="game-logo">GAME STORE</div>
  
  <div class="center-page">
    <div class="login-container">
      <form method="POST" action="login.php"> <!-- Fixed form action -->
        <!-- Treasure decorations -->
        <div class="treasure-decoration treasure-top-left"></div>
        <div class="treasure-decoration treasure-top-right"></div>
        <div class="treasure-decoration treasure-bottom-left"></div>
        <div class="treasure-decoration treasure-bottom-right"></div>
        
        <h2>Login</h2>
        
        <div class="input-group">
          <label for="email">Email</label>
          <i class="fas fa-envelope icon"></i>
          <input type="email" id="email" name="email" placeholder="Enter your email" required> <!-- Added name attribute -->
        </div>
        
        <div class="input-group">
          <label for="password">Password</label>
          <i class="fas fa-lock icon"></i>
          <input type="password" id="password" name="password" placeholder="Enter your password" required> <!-- Added name attribute -->
        </div>
        
        <button type="submit" class="login-btn">
          Login
        </button>
        
        <p class="register-link">Don't have an account? <a href="register.php">Register</a></p>
      </form>
    </div>
  </div>
  
  <script src="../JS/login.js"></script>
</body>
</html>
