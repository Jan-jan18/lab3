<?php
session_start(); //starts the session 
include "db_conn.php"; // includes the database connection 

use PHPMailer\PHPMailer\PHPMailer;// import the php mailer class    
use PHPMailer\PHPMailer\Exception; // Import Exception class

require 'phpmailer/src/Exception.php'; // Include Exception class file
require 'phpmailer/src/PHPMailer.php'; // Include PHPMailer class file
require 'phpmailer/src/SMTP.php'; // Include SMTP class file

if (isset($_GET['logout']) && $_GET['logout'] == 'success') { //for the logout, it checks if the user comes from welcome.php and clicked log out
    $_SESSION['message'] = "Logged out successfully"; // shows the message if the user is logged out
    $_SESSION['alert_type'] = "success"; // success 
}

if (isset($_POST['login'])) {     //check if the login is submitted
    $email = $_POST['email']; // sanitize 
    $pass = $_POST['password']; // 

    // Generate random verification code
    $verification_code = mt_rand(100000, 999999);

    $sql = "SELECT * FROM user WHERE email=? AND password=?"; // query the database for user credentials
    $stmt = $conn->prepare($sql); //prepares the statement
    $stmt->bind_param("ss", $email, $pass); // 
    $stmt->execute(); // execute the prepared statement
    $result = $stmt->get_result(); 

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['user_id'];// stores users id
        $_SESSION['fname'] = $row['First_name'];//stores the first name in session
        $_SESSION['lname'] = $row['Lastname']; //stores the last name in session
        $_SESSION['email'] = $row['email']; //stores the email in session
        $_SESSION['verification_code'] = $verification_code; // Store verification code in session

        $_SESSION['alert_type'] = "success"; //set success alert type

        // Send email with verification code
        $mail = new PHPMailer(true);// create a new instance of phpmailer class for sending mails
        $mail->isSMTP(); //set the mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';// it sets the SMTP server hostname
        $mail->SMTPAuth = true;// Enable SMTP authentication
        $mail->Username = 'reikatauchiha@gmail.com';//servers username/email
        $mail->Password = 'rhlt zyks rwyc mzpf'; // app password from the server
        $mail->SMTPSecure = 'ssl';// enable SSL encryption for smtp secure connection
        $mail->Port = 465;//SMTP port for gmail
        $mail->setFrom('reikatauchiha@gmail.com', 'Russells Website');//Set the senders email and name
        $mail->addAddress($email);//add recipient email address
        $mail->isHTML(true);//set email format to html
        $mail->Subject = "Verification Code";// the subject of the mail
        $mail->Body = "Your verification code is: $verification_code"; //the code from the verification
        $mail->send();//sends the mail

        header("Location: verify.php"); // goes to verify.php
        exit();
    } else {
        $_SESSION['message'] = "Incorrect email or password"; // msg if the email or password is incorrect
        $_SESSION['alert_type'] = "error";// error alert type
        // Redirect back to login page with error message
        header("Location: login.php");// redirects to login.php
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
            <div class="message-container <?php echo $_SESSION['alert_type']; ?>">  <!--Display alert type-->
                <?php 
                echo $_SESSION['message']; //display the session message
                unset($_SESSION['message']); // clear the session message after displaying
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
