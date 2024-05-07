<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Start session to access session variables
session_start();

require '../vendor/autoload.php'; // Include PHPMailer autoload file

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sendCode"])) {
    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307); // Database connection

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


      // Function to generate a 6-digit numeric token and update it in the database
      function generateNumericToken($conn, $email) {
        $minValue = 100000; // Minimum 6-digit number
        $maxValue = 999999; // Maximum 6-digit number
        $token = random_int($minValue, $maxValue);

        $stmt = $conn->prepare("UPDATE tbl_employee SET token = ? WHERE E_mail = ?");
        if ($stmt === false) {
            // Handle the error if the prepare statement fails
            die("Error preparing statement: " . $conn->error);
        }

        // Bind parameters to the prepared statement
        $stmt->bind_param("ss", $token, $email); // Use "ss" for two string parameters

        // Execute the statement
        if ($stmt->execute()) {
            return str_pad($token, 6, '0', STR_PAD_LEFT); // Return the formatted token
        } else {
            // Handle the error if execution fails
            die("Error updating token: " . $stmt->error);
        }
    }

 // Function to send email using PHPMailer
    function sendTokenEmail($email, $token) {
      
        $mail = new PHPMailer(true); // Create a new PHPMailer instance
        try {
            // SMTP configuration for Gmail
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'villareadhub@gmail.com'; // Your Gmail email address
            $mail->Password = 'ulmh emwr tsbw ijao'; // Your Gmail password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Email content
            $mail->setFrom('villareadhub@gmail.com', 'ADMIN'); // Set sender email and name
            $mail->addAddress($email); // Add recipient email
            $mail->isHTML(false); // Set email format to plain text
            $mail->Subject = 'Password Reset Token';
            $mail->Body = "Your password reset token is: $token";

            // Send email
            $mail->send();
            $_SESSION['_email'] = $email; // Save email in session for use in change password page
            header("Location: changepass.php"); // Redirect to the change password page
            exit(); // Exit after redirection

        } catch (Exception $e) {
            echo "Error sending email: {$mail->ErrorInfo}";
        } 
        exit(); 
    }

    $email = $_POST["email"];
// Prepare SQL statement
$stmt = $conn->prepare("SELECT E_mail FROM tbl_employee WHERE E_mail = ?");
$stmt->bind_param("s", $email); // Bind the email parameter
$stmt->execute(); // Execute the query
$stmt->store_result(); // Store the result

// Check if any rows were returned
if ($stmt->num_rows > 0) {
    // Email exists in the database, proceed with token generation and sending email
    $token = generateNumericToken($conn, $email); // Call generateNumericToken with the correct arguments
    if ($token !== false) {
        // Token generation and update successful, proceed with sending the email
        sendTokenEmail($email, $token);
    } else {
        echo "<p>Error generating token or updating database.</p>";
    }
} else {
    // If email does not exist in the database
    header("Location: forgot_password.php?error=Invalid_Email");
    exit();
}

   
   

  
    
    // Close database connection
    $conn->close();
    exit(); // or die(); 
}
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="../styles.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png ">
</head>
<body>
    <div class="main-wrap container-fluid">
        <div class="main-con row ">
            <div class="img-sec col-7">
                <img src="https://villanuevamisor.gov.ph/wp-content/uploads/2022/11/Villanueva-Municipal-Government-Association_LGU_Region-10-1024x692.jpg" alt="Library">
            </div>
            <div class="form-sec col-5">
                <div class="title">
                    <h1><strong>Villa<span>Read</span>Hub</strong></h1>
                <img src="../images/lib-icon.png" alt="lib-icon"/>
                </div>

                <div class="error-con">
                <?php
                    // Check if an error message is passed in the URL
                    if (isset($_GET['error'])) {
                        $error = $_GET['error'];
                        echo "<p class='error-message'>$error</p>";
                    }
                    ?>
               
                </div>
                <div class="form-con">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">  
            <label>Email Address</label><br/>
            <input type="email" name="email" id="Email" required>
            <br/><br/>  
            <div class="btn-container row">
                <button class="button" name="sendCode" type="submit">Send Code</button> 
                <a href="../index.php">Cancel</a>
            </div>
        </form>
    </div>

   

                    
                    </form>
                </div>
                
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>
  
 
</body>
</html> 