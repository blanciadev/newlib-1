<?php
// Start session to access session variables
session_start();

// Check if the reset_email session variable is set
if (isset($_SESSION['_email'])) {
    // Retrieve the email from the session
    $resetEmail = $_SESSION['_email'];

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['code'])) {
        // Get the entered code from the form
        $enteredCode = $_POST['code'];

        // Database connection and prepare the SQL query
        $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
        $stmt = $conn->prepare("SELECT E_mail, token FROM tbl_employee WHERE E_mail = ?");
        $stmt->bind_param("s", $resetEmail);
        $stmt->execute();
        $stmt->store_result();

        // Bind the result variables
        $stmt->bind_result($dbEmail, $dbToken);

        // Fetch the results
        $stmt->fetch();

        // Check if the entered code matches the token
        if ($stmt->num_rows > 0 && $enteredCode == $dbToken) {
            // Code matches, proceed with password change or further action
            // Redirect to a success page or perform necessary actions
            header("Location: process_pass.php"); // Example: Redirect to success page
            exit(); // Exit after redirection
        } else {
            // Code does not match, redirect to error page or handle accordingly
            header("Location: changepass.php?error=Invalid code");
            exit(); // Exit after redirection
        }
    }
} else {
    // Redirect to an error page or handle the case when the session variable is not set
    header("Location: forgot_password.php");
    exit(); // Exit after redirection
}

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification</title>
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
                    <form action="" method="POST">
                        <label for="code">Enter Code:</label><br>
                        <input type="text" id="code" name="code" required><br><br>
                        <button type="submit" name="submit">Submit</button>
                    </form>
                </div>
                
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>
<script>

</script>
 
</body>
</html> 