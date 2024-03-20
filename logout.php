<?php
session_start(); // Start the session
include "db_conn.php"; // Include database connection script

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Get the user ID from the session
    $user_id = $_SESSION['user_id'];
    
    // Update the active status in the database to "not active" for the logged-out user
    $update_active_query = "UPDATE user SET Active='Not Active' WHERE user_id='$user_id'";
    mysqli_query($conn, $update_active_query);
    
    // Unset all session variables
    session_unset();
    // Destroy the session
    session_destroy();
}

// Redirect to the login page with a parameter indicating successful logout
header("Location: login.php?logout=success");
exit(); // Stop further execution
?>
