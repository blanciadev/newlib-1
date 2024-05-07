<?php
require "../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['dataURL'])) {
    $dataURL = $_POST['dataURL'];
    $lastInsertedID = isset($_POST['lastInsertedID']) ? $_POST['lastInsertedID'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
   
    $parts = explode(',', $dataURL);
    $data = base64_decode($parts[1]);

    $dirPath = 'assets';
    if (!file_exists($dirPath)) {
        mkdir($dirPath, 0777, true);
    }

    $filePath = $dirPath . '/' . $lastInsertedID . '.png';
    if (!file_put_contents($filePath, $data)) {
        echo json_encode(["status" => "error", "message" => "Failed to write to file."]);
    } else {
        echo json_encode(["status" => "success", "message" => "QR code saved to " . $filePath]);
            
        // CHANGE THE PORT IF NEEDED
        $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307);


       // Read the image file into a PHP variable
$imageData = mysqli_real_escape_string($conn, file_get_contents($filePath));

// Prepare the SQL query
$insertQuery = "UPDATE tbl_borrower SET image_file = '{$imageData}' WHERE Email = '{$email}'";

// Execute the statement
$insertResult = mysqli_query($conn, $insertQuery);

if ($insertResult) {
    echo json_encode(["status" => "success", "message" => "Image data inserted successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to insert image data: " . mysqli_error($conn)]);
}



        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);

        // Try sending the email
        try {
            // SMTP configuration for Gmail
           // $email = $_SESSION['email'];

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
            $mail->Subject = 'QR CODE VILLA READ HUB';
            $mail->Body = "Your QR CODE: $lastInsertedID";

            // Attach QR code image
            $mail->addAttachment($filePath, $lastInsertedID . '.png');

            // Send email
            $mail->send();

            // Delete the temporary QR code file
            unlink($filePath);
          //  echo '<script>alert("Record Updated successfully.");</script>';

            exit(); // Exit after redirection

        } catch (Exception $e) {
            echo "Error sending email: {$mail->ErrorInfo}";
        }
        exit();
    }
}
?>
