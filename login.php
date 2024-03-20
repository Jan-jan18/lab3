<?php
session_start();
include "db_conn.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
    $_SESSION['message'] = "Logged out successfully";
    $_SESSION['alert_type'] = "success";
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    // Generate random verification code
    $verification_code = mt_rand(100000, 999999);

    $sql = "SELECT * FROM user WHERE email=? AND password=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['fname'] = $row['First_name'];
        $_SESSION['lname'] = $row['Lastname'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['verification_code'] = $verification_code; // Store verification code in session

        $_SESSION['alert_type'] = "success";

        // Send email with verification code
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'uchihareikata@gmail.com';
        $mail->Password = 'qyki jszw moov wvhz';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->setFrom('uchihareikata@gmail.com', 'Russells Website');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = "Verification Code";
        $mail->Body = "Your verification code is: $verification_code";
        $mail->send();

        header("Location: verify.php");
        exit();
    } else {
        $_SESSION['message'] = "Incorrect email or password";
        $_SESSION['alert_type'] = "error";
        // Redirect back to login page with error message
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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
    <div class="login-container">
        <h2>Log In</h2>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message-container <?php echo $_SESSION['alert_type']; ?>">
                <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']); 
                ?> 
            </div>
            <?php unset($_SESSION['alert_type']); ?>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="email" name="email" class="form-control" placeholder="Email" required><br>
            <input type="password" name="password" class="form-control" placeholder="Password" required><br>
            <button type="submit" name="login" class="btn btn-primary">Log In</button>
        </form>
        <div class="btn-container">
            <p>Don't have an account? <a href="index.php" class="btn btn-primary">Sign Up</a></p>
        </div>
    </div>
</div>

</body>
</html>
