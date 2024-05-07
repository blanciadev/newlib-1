<?php

require "../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

echo "<script>console.log('Send Email Function');</script>";

// Check if the User_ID session variable is not set or empty
// if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
//     // Redirect to index.php
//     header("Location: ../index.php");
//     exit(); // Ensure script execution stops after redirection
// }

// Get the Borrower_ID from the URL parameter
if (isset($_GET['borrower_id'])) {
    $borrowerId = $_GET['borrower_id'];

    // Database connection
    $conn_display_all = mysqli_connect("localhost", "root", "root", "db_library_2", 3307);
    if ($conn_display_all->connect_error) {
        die("Connection failed: " . $conn_display_all->connect_error);
    }

    // SQL query to select specific record from tbl_borrower based on Borrower_ID
    $sql_display_all = "SELECT
                            b.User_ID, 
                            b.Accession_Code, 
                            bk.Book_Title, 
                            bd.Quantity, 
                            b.Date_Borrowed, 
                            b.Due_Date, 
                            br.Borrower_ID, 
                            bd.tb_status, 
                            br.First_Name, 
                            br.Last_Name, 
                            b.Borrow_ID, 
                            br.Email
                        FROM
                            tbl_borrowdetails AS bd
                            INNER JOIN
                            tbl_borrow AS b ON bd.Borrower_ID = b.Borrower_ID AND bd.BorrowDetails_ID = b.Borrow_ID
                            INNER JOIN
                            tbl_books AS bk ON b.Accession_Code = bk.Accession_Code
                            INNER JOIN
                            tbl_borrower AS br ON bd.Borrower_ID = br.Borrower_ID
                        WHERE
                            b.Due_Date = CURDATE() AND
                            bd.tb_status = 'Pending' AND
                            br.Borrower_ID = $borrowerId";

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
                $mail->setFrom('villareadhub@gmail.com', 'Administrator'); // Set sender email and name
                $mail->addAddress($borrowerData['Email']); // Add recipient email
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = 'Notification Of Book Due Date';
                $mail->Body = '<h3>Hi! Its time to return the book!</h3><hr>' .
                    '<p>Dear Borrower,</p>'.
                    '<p>We hope this message finds you well. 
                        We wanted to remind you that today is the due date for the book you borrowed from our library. 
                        We greatly appreciate your patronage and trust that the book has been a valuable resource for you.</p>'.
                    '<p><strong>Book: </strong>' . $borrowerData['Book_Title'] . ' - ' . $borrowerData['Quantity'] . ' </p>' . 
                    "<p>To avoid any late fees or penalties, please return the book to the library by the end of the day. 
                    If you need to renew the book or require any assistance, please don't hesitate to reach out to us. <br>

                    Thank you for your cooperation, and we look forward to continuing to serve you in the future.</p>";

                // Send email
                if ($mail->send()) {
                    echo json_encode(array('success' => 'Email sent successfully'));
                    $message = "Email sent successfully";
                    // Log success
                    echo "<script>  
                            console.log(".$message."); 
                          </script>";
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
} else {
    echo "<script>console.error('NO BORROWER ID FOUND');</script>";
}
?> 