<?php
// Start the session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    // Set a message
    $_SESSION['message'] = "Please log in to access this page";
    // Set the alert type
    $_SESSION['alert_type'] = "error";
    // Redirect to the login page
    header("Location: login.php");
    exit(); // Stop further execution
}
?>
