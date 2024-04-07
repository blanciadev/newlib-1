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

   
} else {

    // Email does not exist, proceed with insertion
    $insertQuery = "INSERT INTO tbl_borrower (First_Name, Middle_Name, Last_Name, Contact_Number, Email, Affiliation) 
                    VALUES ('$first_name', '$middle_name', '$last_name', '$contact_number', '$email', '$affiliation')";

    // Execute the insertion query
    $insertResult = mysqli_query($conn, $insertQuery);

    if ($insertResult) {
        // Get the last inserted ID
        $lastInsertedID = mysqli_insert_id($conn);
        $_SESSION['lastInsertedID'] = $lastInsertedID;
      
        echo json_encode(['lastInsertedID' => $lastInsertedID]);
        
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    
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

   
    <div class="container d-flex justify-content-center"><!-- board container -->
    <div class="back-btn">
                        <a href="./staff_log.php"><i class='bx bx-arrow-back'></i></a>
                    </div>
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
                        <p class="card-text"><strong>School/Affiliation:</strong> <?php echo $affiliation; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
            } else {
                console.log("No ID available for QR Code"); // Log that no ID is available for QR code generation
                qrCodeContainer.innerHTML = "QR Code not available";
            }
        });
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