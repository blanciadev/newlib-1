<?php
session_start();
require "../vendor/autoload.php";


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

// Retrieve session data
$first_name = $_SESSION['first_name'];
$middle_name = $_SESSION['middle_name'];
$last_name = $_SESSION['last_name'];
$contact_number = $_SESSION['contact_number'];
$email = $_SESSION['email'];
$affiliation = $_SESSION['affiliation'];


    // CHANGE THE PORT IF NEEDED
    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);

    // Check if the email already exists in the database
$checkEmailQuery = "SELECT * FROM tbl_borrower WHERE Email = '$email'";
$result = mysqli_query($conn, $checkEmailQuery);
if (mysqli_num_rows($result) > 0) {
    // Email already exists, handle accordingly (e.g., show error message or update existing record)
    echo "Email address already exists in the database.";

      // Retrieve the QR code data from wherever it's stored
 //     $qrCodeData = ''; // Update this line with the actual retrieval of QR code data

   // sendQRCodeEmail($qrCodeData);
} else {

    // Email does not exist, proceed with insertion
    $insertQuery = "INSERT INTO tbl_borrower (First_Name, Middle_Name, Last_Name, Contact_Number, Email, Affiliation) 
                    VALUES ('$first_name', '$middle_name', '$last_name', '$contact_number', '$email', '$affiliation')";

    // Execute the insertion query
    $insertResult = mysqli_query($conn, $insertQuery);

    // Check if insertion was successful
    if ($insertResult) {
        echo "Data inserted successfully.";
        
        // Get the dataURL parameter
        $dataURL = $_POST['dataURL'];
    
        // Extract base64 encoded data
        $base64Data = substr($dataURL, strpos($dataURL, ",") + 1);
    
        // Convert base64 data to binary data
        $binaryData = base64_decode($base64Data);
        
        // Insert binary image data into database
        $query = "UPDATE tbl_borrower SET image_file = ? WHERE Borrower_ID = ?";
        $statement = $mysqli->prepare($query);
    
        // Assuming you have already inserted the row and retrieved its ID
        $borrowerID = mysqli_insert_id($mysqli);
    
        $statement->bind_param("bi", $binaryData, $borrowerID);
        $statement->execute();
        
        // Check if update was successful
        if ($statement->affected_rows > 0) {
            echo "QR Code inserted into database successfully.";
        } else {
            echo "Failed to insert QR Code into database.";
        }
    } else {
        echo "Failed to insert data into database.";
    }
    
} 


function sendQRCodeEmail($qrCodeData) {
    echo "<script>console.log('sendQRCodeEmail is called');</script>";

    // Extract QR code data
    $qrCodeData = $qrCodeData['qrCodeData'];

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    // Try sending the email
    try {
        // SMTP configuration for Gmail
        $email = $_SESSION['email'];

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
        $mail->Body = "Your QR CODE: $qrCodeData";


        //   // Regenerate QR code server-side using PHP QR Code library
        //   $qrCode = new Endroid\QrCode\QrCode($qrCodeData);
        //   $qrCodeImage = $qrCode-getDataUri();
  
          // Attach QR code image
        //   $mail->addStringAttachment($qrCodeImage, 'qrcode.png', 'base64', 'image/png');
  

          
        // Send email
        $mail->send();
     
        echo '<script>alert("Record Updated successfully.");</script>';
    
        exit(); // Exit after redirection

    } catch (Exception $e) {
        echo "Error sending email: {$mail->ErrorInfo}";
    }
    exit();
} 




?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VillaReadHub - Register User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="./staff.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png ">
</head>

<body>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" integrity="sha512-CNgIRecGo7nphbeZ04Sc13ka07paqdeTu0WR1IM4kNcpmBAUSHSQX0FslNhTDadL4O5SAGapGt4FodqL8My0mA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Include QRCode.js library -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@latest"></script>


    <div class="board container"><!-- board container -->
        <div class="col-md-6 mt-4">
            <div class="card">
                <div class="card-header">
                    Library Card
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-center" id="qrcode-container">
                            <!-- QR Code Container -->
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">

                            <p class="card-text"><strong>First Name:</strong> <?php echo $first_name; ?></p>
                            <p class="card-text"><strong>Middle Name:</strong> <?php echo $middle_name; ?></p>
                            <p class="card-text"><strong>Last Name:</strong> <?php echo $last_name; ?></p>
                            <p class="card-text"><strong>Contact Number:</strong> <?php echo $contact_number; ?></p>
                            <p class="card-text"><strong>Email:</strong> <?php echo $email; ?></p>
                            <p class="card-text"><strong>Affiliation:</strong> <?php echo $affiliation; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    </div>

    <div class="form-con">
        <form action="" method="POST">
            <div class="btn-container row">
                <button class="button" name="sendCode" type="submit">Send QR Code</button> <!-- Button to send QR code -->
                <a href="../index.php">Cancel</a>
            </div>
        </form>
    </div>

    <a href="staff_log.php" class="btn btn-primary">Go to Staff Log</a>


    <script>

        document.addEventListener("DOMContentLoaded", function() {
            console.log("Document loaded"); // Log that the document has loaded
            let qrCodeContainer = document.getElementById("qrcode-container");
            console.log("QR Code Container:", qrCodeContainer); // Log the QR code container element
            let lastInsertedID = "<?php echo isset($_SESSION['lastInsertedID']) ? $_SESSION['lastInsertedID'] : ''; ?>";
            console.log("Last Inserted ID:", lastInsertedID); // Log the last inserted ID from PHP session

            if (lastInsertedID !== '') {
                console.log("Generating QR Code for ID:", lastInsertedID); // Log that QR code generation is starting
                // Set options for QRCode.js
                let qrOptions = {
                    text: lastInsertedID,
                    width: 200, // Custom width in pixels
                    height: 200, // Custom height in pixels
                };
                // Generate QR Code using QRCode.js with custom options
                new QRCode(qrCodeContainer, qrOptions);

                // Wait for the QR code image to be generated
                setTimeout(function() {
                    // Convert QR code to data URL
                    let dataURL = qrCodeContainer.getElementsByTagName('img')[0].src;

                    // Create a download link for the QR code image
                    let downloadLink = document.createElement('a');
                    downloadLink.href = dataURL;
                    downloadLink.download = 'qr_code.png'; // Set the download file name
                    downloadLink.textContent = 'Download QR Code';

                    // Append the download link to the document body
                    document.body.appendChild(downloadLink);
                }, 500); // Adjust the timeout if needed
            } else {
                console.log("No ID available for QR Code"); // Log that no ID is available for QR code generation
                qrCodeContainer.innerHTML = "QR Code not available";
            }
        });



    // Function to send email with QR code attachment
    // function sendQRCodeEmail(qrOptions, lastInsertedID) {
    //     let xhr = new XMLHttpRequest();
    //     xhr.open('POST', ''); // Call the PHP file containing the sendQRCodeEmail function
    //     xhr.setRequestHeader('Content-Type', 'application/json');
    //     xhr.onload = function() {
    //         if (xhr.status === 200) {
    //             console.log(xhr.responseText); // Log the response
    //         } else {
    //             console.error('Failed to send email');
    //         }
    //     };
    //     xhr.send(JSON.stringify({ qrOptions, lastInsertedID })); // Send necessary data
    // }

        
    </script>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>
    <script>
        let date = new Date().toLocaleDateString('en-US', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            weekday: 'long',
        });
        document.getElementById("currentDate").innerText = date;

        setInterval(() => {
            let time = new Date().toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: 'numeric',
                second: 'numeric',
                hour12: 'true',
            })
            document.getElementById("currentTime").innerText = time;

        }, 1000)


        let navItems = document.querySelectorAll(".nav-item"); //adding .active class to navitems 
        navItems.forEach(item => {
            item.addEventListener('click', () => {
                document.querySelector('.active')?.classList.remove('active');
                item.classList.add('active');


            })

        })
    </script>
</body>

</html>