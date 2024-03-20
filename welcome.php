<?php
include "authentication.php"; // Include authentication script 
include "db_conn.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect the user to the login page if not logged in
    header("Location: login.php");
    exit(); // Terminate script execution
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Update the active status in the database to "active" for the logged-in user
$update_active_query = "UPDATE user SET Active='Active' WHERE user_id='$user_id'";
mysqli_query($conn, $update_active_query);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Include custom stylesheet -->
    <link href="Stylesheet.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="text-container">
            <h2>Welcome</h2>
            <!-- Form for logout -->
            <form action="logout.php" method="post"> 
                <button type="submit" class="btn btn-primary">Logout</button>
            </form>
        </div>
    </div>
</body>
</html>
