<?php
session_start(); // Start the session to use session variables
include "db_conn.php"; // Include the database connection script

// Include PHPMailer library
use PHPMailer\PHPMailer\PHPMailer; // Import PHPMailer class
use PHPMailer\PHPMailer\Exception; // Import Exception class

require 'phpmailer/src/Exception.php'; // Include Exception class file
require 'phpmailer/src/PHPMailer.php'; // Include PHPMailer class file
require 'phpmailer/src/SMTP.php'; // Include SMTP class file

if (isset($_POST['register'])) { // Check if the registration form is submitted
    // Validate and sanitize user input
    function validate($data) {
        $data = trim($data); // Remove whitespace from the beginning and end of a string
        $data = stripslashes($data); // Remove backslashes (\) from a string
        $data = htmlspecialchars($data); // Convert special characters to HTML entities
        return $data; // Return the sanitized data
    }

    // Assign input values to variables after validation
    $fname = validate($_POST['fname']); // Get and validate first name
    $mname = validate($_POST['mname']); // Get and validate middle name
    $lname = validate($_POST['lname']); // Get and validate last name
    $email = validate($_POST['email']); // Get and validate email
    $pass = validate($_POST['password']); // Get and validate password
    $username = validate($_POST['username']); // Get and validate username

    // Default status and active values
    $status = 'Not Verified';
    $active = 'Not Active';

    if (empty($fname) || empty($lname) || empty($email) || empty($pass) || empty($username)) { // Check if any required field is empty
        $_SESSION['message'] = "All fields are required"; // Set error message
        $_SESSION['alert_type'] = "error"; // Set error alert type
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // Check if email is valid
        $_SESSION['message'] = "Invalid email format"; // Set error message
        $_SESSION['alert_type'] = "error"; // Set error alert type
    } else {
        // Check if email is already registered
        $email_check_stmt = $conn->prepare("SELECT * FROM user WHERE Email=?");
        $email_check_stmt->bind_param("s", $email);
        $email_check_stmt->execute();
        $email_check_result = $email_check_stmt->get_result();
        
        if ($email_check_result->num_rows > 0) { // If email is already registered
            $_SESSION['message'] = "Email already registered"; // Set error message
            $_SESSION['alert_type'] = "error"; // Set error alert type
        } else {
            // Insert user data into the database
            $stmt = $conn->prepare("INSERT INTO user (username, password, First_name, Middle_name, Lastname, Email, Status, Active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $username, $pass, $fname, $mname, $lname, $email, $status, $active);
            $stmt->execute(); // Execute the prepared statement
            $_SESSION['message'] = "Registration successful"; // Set success message
            $_SESSION['alert_type'] = "success"; // Set success alert type

            // Send email to the user
            $mail = new PHPMailer(true); // Create a new instance of PHPMailer class for sending emails
            $mail->isSMTP(); // Set the mailer to use SMTP
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server hostname
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail->Username = 'reikatauchiha@gmail.com';
            $mail->Password = 'rhlt zyks rwyc mzpf';
            $mail->SMTPSecure = 'ssl'; // Enable SSL encryption for SMTP secure connection
            $mail->Port = 465; // Set the SMTP port for Gmail
            $mail->setFrom('reikatauchiha@gmail.com', 'Email Verification'); // Set the sender's email address and name
            $mail->addAddress($email); // Add recipient email address
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = "Email Verification"; // Set the subject of the email
            // Set the body of the email, including user's information
            $mail->Body = "Dear ".$fname." ".$mname." ".$lname.",<br><br>Thank you for providing your information. Your email (".$email.") has been used for email verification for localhost/phpmyadmin.<br><br>Sincerely,<br>Russell Osias";
            $mail->send(); // Send the email
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="Stylesheet.css" rel="stylesheet">
    <style>
        .btn-container {
            margin-top: 20px; /* Adjust margin */
            display: flex;
            justify-content: space-between; /* Align buttons horizontally */
            align-items: center; /* Align buttons vertically */
        }
        .message-container {
            text-align: center;
            color: #fff;
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
        <div class="register-container">
            <h2>Sign Up</h2>
            <?php if (isset($_SESSION['message'])): ?> <!-- Check if session message is set -->
                <div class="message-container <?php echo ($_SESSION['alert_type'] == 'error') ? 'error' : 'success'; ?>"> <!-- Display alert based on alert type -->
                    <?php
                    echo $_SESSION['message']; // Display the session message
                    unset($_SESSION['message']); // Clear the session message after displaying
                    unset($_SESSION['alert_type']); // Clear the alert type after displaying
                    ?>
                </div>
            <?php endif; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> <!-- Form for registration -->
                <input type="text" name="fname" class="form-control" placeholder="First Name" required><br>
                <input type="text" name="mname" class="form-control" placeholder="Middle Name"><br>
                <input type="text" name="lname" class="form-control" placeholder="Last Name" required><br>
                <input type="email" name="email" class="form-control" placeholder="Email" required><br>
                <input type="text" name="username" class="form-control" placeholder="Username" required><br>
                <input type="password" name="password" class="form-control" placeholder="Password" required><br>
                <button type="submit" name="register" class="btn btn-primary">Sign Up</button> <!-- Submit button for registration -->
            </form>
            <div class="btn-container"> <!-- Link to login page -->
                <p>Already have an account? <a href="login.php" class="btn btn-primary">Log In</a></p>
            </div>
           
        </div>
        <?php include('footer.php')?>
    </div>

</body>
</html>
