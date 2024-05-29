<?php
session_start();
include "authentication.php"; // Include authentication script 
include "db_conn.php";

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect the user to the login page
    header("Location: login.php");
    exit(); // Terminate script execution
}

if (isset($_POST['verify'])) {
    $verification_code = $_POST['verification_code'];

    // Perform verification of the verification code here

   
    $expected_verification_code = $_SESSION['verification_code']; // Retrieve the verification code from session
    if ($verification_code == $expected_verification_code) {
        // If verification successful, update status in the database to "verified"
        $update_status_query = "UPDATE user SET Status='Verified' WHERE user_id=?";
        $stmt = $conn->prepare($update_status_query);
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error); //if theres an error
        }
// this process if for making the database change if the user is veried, it will says verified in the database, and not verify if not
        $user_id = $_SESSION['user_id']; //checks whats the users  user_id
        $stmt->bind_param("i", $user_id); // Assuming user_id is an integer
        if (!$stmt->execute()) { // execute
            die("Error executing statement: " . $stmt->error);// for error if theres an error in the statement
        }

        // Set success message
        $_SESSION['message'] = "Verification successful"; //says the message if the verification is a success
        $_SESSION['alert_type'] = "success"; //alert type success
        header("Location: welcome.php"); //goes to welcome.php after verifying successfully
        exit();
    } else {
        // If verification fails, set error message and redirect to verify.php
        $_SESSION['message'] = "Incorrect verification code";// says the message if the verification is not successfull
        $_SESSION['alert_type'] = "error"; //alert type error
        header("Location: verify.php");//reloads to verify.php if the code is wrong 
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verification</title>
    <!-- Linking Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Linking your custom CSS file -->
    <link href="Stylesheet.css" rel="stylesheet">
    <style>
        /* Additional styles here */
        .btn-container {
            margin-top: 20px; /* Adjust margin */
            display: flex;
            justify-content: space-between; /* Align buttons horizontally */
            align-items: center; /* Align buttons vertically */
        }
        .message-container {
            text-align: center;
            color: #fff;
            background-color: #007bff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .error {
            background-color: #dc3545;
        }
        .success {
            background-color: #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="verification-container">
            <h2>Verification</h2>
            <?php if (isset($_SESSION['message'])): ?>
                <div class="message-container <?php echo $_SESSION['alert_type']; ?>">
                    <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']); // Clear the session message after displaying
                    ?> 
                </div>
                <?php unset($_SESSION['alert_type']); // Unsetting session variable after displaying ?>
            <?php endif; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="text" name="verification_code" class="form-control" placeholder="Verification Code" required><br>
                <button type="submit" name="verify" class="btn btn-primary">Verify</button>
            </form>
            <div class="btn-container">
                <p>Back to Login <a href="login.php" class="btn btn-primary">Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>
