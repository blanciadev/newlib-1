<?php

require "../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

// Get the Borrower_ID from the AJAX request
if (isset($_POST['borrower_id'])) {
    $borrowerId = $_POST['borrower_id'];

    // Database connection
    $conn_display_all = mysqli_connect("localhost", "root", "12345678", "db_library_2", 3306);
    if ($conn_display_all->connect_error) {
        die("Connection failed: " . $conn_display_all->connect_error);
    }

    // SQL query to select specific record from tbl_borrower based on Borrower_ID
    $sql_display_all = "SELECT First_Name, Middle_Name, Last_Name, Contact_Number, Email, affiliation, image_file 
                        FROM tbl_borrower 
                        WHERE Borrower_ID = '$borrowerId'";
    $result_display_all = $conn_display_all->query($sql_display_all);

    // Check if the query was successful
    if ($result_display_all) {
        // Fetch the data as an associative array
        $borrowerData = $result_display_all->fetch_assoc();

        // Check if any data was found
        if ($borrowerData) {
            // Create a new PHPMailer instance
            $mail = new PHPMailer(true);

            // Try sending the email
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
                $mail->addAddress($borrowerData['Email']); // Add recipient email
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = 'Borrower Details';
                $mail->Body = '<h1>Borrower Details</h1>' .
                    '<p>Name: ' . $borrowerData['First_Name'] . ' ' . $borrowerData['Middle_Name'] . ' ' . $borrowerData['Last_Name'] . '</p>' .
                    '<p>Contact Number: ' . $borrowerData['Contact_Number'] . '</p>' .
                    '<p>Email: ' . $borrowerData['Email'] . '</p>' .
                    '<p>Affiliation: ' . $borrowerData['affiliation'] . '</p>';

                // Attach image as an attachment
                $mail->addStringAttachment($borrowerData['image_file'], 'borrower_image.png', 'base64', 'image/png');

                // Send email
                if ($mail->send()) {
                    echo json_encode(array('success' => 'Email sent successfully'));
                    // Log success
                    echo "<script>console.log('Email sent successfully');</script>";
                } else {
                    echo json_encode(array('error' => 'Error sending email'));
                    // Log error
                    echo "<script>console.error('Error sending email: " . $mail->ErrorInfo . "');</script>";
                }
            } catch (Exception $e) {
                echo json_encode(array('error' => "Error sending email: {$mail->ErrorInfo}"));
                // Log exception
                echo "<script>console.error('Error sending email: {$mail->ErrorInfo}');</script>";
            }
        } else {
            echo json_encode(array('error' => 'No data found for the specified Borrower_ID'));
            // Log error
            echo "<script>console.error('No data found for the specified Borrower_ID');</script>";
        }
    } else {
        echo json_encode(array('error' => 'Error executing the query: ' . $conn_display_all->error));
        // Log error
        echo "<script>console.error('Error executing the query: " . $conn_display_all->error . "');</script>";
    }

    // Close the database connection
    $conn_display_all->close();
}
