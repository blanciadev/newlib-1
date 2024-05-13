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



 // Define the HTML code for the toast element
 echo '<div class="toastNotif hide">
 <div class="toast-content">
     <i class="bx bx-check check"></i>
     <div class="message">
         <span class="text text-1"></span>
         <!-- this message can be changed to "Success" and "Error"-->
         <span class="text text-2"></span>
         <!-- specify based on the if-else statements -->
     </div>
 </div>
 <i class="bx bx-x close"></i>
 <div class="progress"></div>
</div>';

// Define JavaScript functions to handle the toast
echo '<script>
 function showToast(type, message) {
     var toast = document.querySelector(".toastNotif");
     var progress = document.querySelector(".progress");
     var text1 = toast.querySelector(".text-1");
     var text2 = toast.querySelector(".text-2");
     
     if (toast && progress && text1 && text2) {
         // Update the toast content based on the message type
         if (type === "success") {
             text1.textContent = "Success";
             toast.classList.remove("error");
         } else if (type === "error") {
             text1.textContent = "Error";
             toast.classList.add("error");
         } else {
             console.error("Invalid message type");
             return;
         }
         
         // Set the message content
         text2.textContent = message;
         
         // Show the toast and progress
         toast.classList.add("showing");
         progress.classList.add("showing");
         
         // Hide the toast and progress after 5 seconds
         setTimeout(() => {
             toast.classList.remove("showing");
             progress.classList.remove("showing");
             //  window.location.href = "admin_staff.php";
         }, 5000);
     } else {
         console.error("Toast elements not found");
     }
 }

 function closeToast() {
     var toast = document.querySelector(".toastNotif");
     var progress = document.querySelector(".progress");
     toast.classList.remove("showing");
     progress.classList.remove("showing");
 }
</script>';


// Retrieve session data
$first_name = $_SESSION['first_name'];
$middle_name = $_SESSION['middle_name'];
$last_name = $_SESSION['last_name'];
$contact_number = $_SESSION['contact_number'];
$email = $_SESSION['email'];
$affiliation = $_SESSION['affiliation']; 
$age = $_SESSION['age'];
$gender = $_SESSION['Gender'];

$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);

// Check if the email already exists in the database
$checkEmailQuery = "SELECT * FROM tbl_borrower WHERE Email = '$email'";
$result = mysqli_query($conn, $checkEmailQuery);
if (mysqli_num_rows($result) > 0) {
    // Email already exists, handle accordingly (e.g., show error message or update existing record)
    // echo "Email address already exists in the database.";
    echo '<script>
    // Call showToast with "success" message type after successful insertion
    showToast("error", "Email address already exists in the database.");
</script>';
   
} else {

    // Email does not exist, proceed with insertion
    $insertQuery = "INSERT INTO tbl_borrower (First_Name, Middle_Name, Last_Name, Age, Gender, Contact_Number, Email, Affiliation) 
                    VALUES ('$first_name', '$middle_name', '$last_name', '$age', '$gender', '$contact_number', '$email', '$affiliation')";

    // Execute the insertion query
    $insertResult = mysqli_query($conn, $insertQuery);

    if ($insertResult) {
        // Get the last inserted ID
        $lastInsertedID = mysqli_insert_id($conn);
        $_SESSION['lastInsertedID'] = $lastInsertedID;
      
        // echo json_encode(['lastInsertedID' => $lastInsertedID]);
        
        echo '<script>
        // Call showToast with "success" message type after successful insertion
        showToast("success", "Data Inserted Succesfully");
    </script>';
   
    
    } else {
        echo '<script>
    // Call showToast with "success" message type after successful insertion
    showToast("error", "Error");
</script>';
    }

    
}




?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Card</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="./staff.css" rel="stylesheet">
    <link href="toast.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png ">
</head>

<body>
    <div class="container d-flex flex-wrap align-content-center justify-content-around align-items-center"><!-- board container -->
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
                            <p class="card-text"><strong>Age: </strong> <?php echo $age; ?></p>
                            <p class="card-text"><strong>Gender: </strong> <?php echo $gender; ?></p>
                            <p class="card-text"><strong>Contact Number:</strong> <?php echo $contact_number; ?></p>
                            <p class="card-text"><strong>Email:</strong> <?php echo $email; ?></p>
                            <p class="card-text"><strong>Affiliation:</strong> <?php echo $affiliation; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-con">
            <form action="" method="POST">
                <div class="btn-container row">
                    <button class="button" name="sendCode" type="submit">Send QR Code</button> <!-- Button to send QR code -->
                    <a href="admin_dashboard.php">Cancel</a>
                </div>
            </form> 
            <a href="staff_log.php" class="btn btn-primary">Go to Log Record</a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" integrity="sha512-CNgIRecGo7nphbeZ04Sc13ka07paqdeTu0WR1IM4kNcpmBAUSHSQX0FslNhTDadL4O5SAGapGt4FodqL8My0mA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let qrCodeContainer = document.getElementById("qrcode-container");
            let lastInsertedID = "<?php echo isset($_SESSION['lastInsertedID']) ? $_SESSION['lastInsertedID'] : ''; ?>";
            let email = "<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>";
          
            if (lastInsertedID !== '') {
                console.log("Generating QR Code for ID:", lastInsertedID); // Log that QR code generation is starting
                // Set options for QRCode.js
                let qrOptions = {
                text: lastInsertedID,
                width: 200, // Custom width in pixels
                height: 200, // Custom height in pixels
                colorDark: "#000000", // QR code color
                colorLight: "#ffffff" // Background color
                };

                // Generate QR Code using QRCode.js with custom options
                new QRCode(qrCodeContainer, qrOptions);
 
                // Wait for the QR code image to be generated
                setTimeout(function() {
                    // Convert QR code to data URL
                    let dataURL = qrCodeContainer.getElementsByTagName('img')[0].src;

                   // Send the data URL and lastInsertedID to a PHP script using AJAX
                    let xhr = new XMLHttpRequest();
                    xhr.open("POST", "save_qr_code.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            console.log(this.responseText); // Log the server response to the console
                        }
                    };
                    xhr.send("dataURL=" + encodeURIComponent(dataURL) + "&lastInsertedID=" + encodeURIComponent(lastInsertedID)  + "&email=" + encodeURIComponent(email));

                }, 500); // Adjust the timeout if needed

            } else {
                console.log("No ID available for QR Code"); // Log that no ID is available for QR code generation
                qrCodeContainer.innerHTML = "QR Code not available";
            }
        });

    </script>
    
</body>

</html>