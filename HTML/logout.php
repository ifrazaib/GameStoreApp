<?php
// MUST be first line with no whitespace before
session_start();

// Clear all session data
$_SESSION = [];

// Destroy session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Redirect to home page
header("Location: http://localhost/GAME_STORE/HTML/login.php");
exit;
?>