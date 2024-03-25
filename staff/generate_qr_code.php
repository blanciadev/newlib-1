<?php
session_start();
require "../vendor/autoload.php";

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Retrieve session data
$first_name = $_SESSION['first_name'];
$middle_name = $_SESSION['middle_name'];
$last_name = $_SESSION['last_name'];
$contact_number = $_SESSION['contact_number'];
$email = $_SESSION['email'];
$affiliation = $_SESSION['affiliation'];

// CHANGE THE PORT IF NEEDED
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308); // Change the database credentials as needed

// Check if the connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Your insert query
$insertQuery = "INSERT INTO tbl_borrower (First_Name, Middle_Name, Last_Name, Contact_Number, Email, Affiliation) 
                VALUES ('$first_name', '$middle_name', '$last_name', '$contact_number', '$email', '$affiliation')";

// Perform the insert operation
if ($conn->query($insertQuery)) {
    // Get the last inserted ID
    $lastInsertedID = mysqli_insert_id($conn);

    // Store the last inserted ID in session
    $_SESSION['lastInsertedID'] = $lastInsertedID;

   
    
} else {
    // Handle the case where the insert query fails
    echo "Error: " . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VillaReadHub - Fines</title>
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

    <!-- <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" ><!--sidenav container-->
        <!-- <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none"> -->
            <h2>Villa<span>Read</span>Hub</h2> 
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon"/>
        </a><!--header container-->
        <div class="user-header mt-4 d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
            <img src="https://github.com/mdo.png" alt="" width="50" height="50" class="rounded-circle me-2">
            <!-- <strong><span><?php echo $_SESSION["staff_name"] ."<br/>"; echo $_SESSION["role"]; ?></span> </strong>  -->
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./staff_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./staff_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
                      <li class="nav-item"> <a href="./staff_borrow.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transaction</a> </li>Borrow</a> </li>
            <li class="nav-item"> <a href="./staff_return.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Return</a> </li>
            <li class="nav-item"> <a href="./staff_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item active"> <a href="./staff_registerUser.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <hr>
            <li class="nav-item"> <a href="./staff_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
    </div> 

    <div class="board container"><!--board container-->
    <div id="qrcode-container"></div>
   
    </div>
    <div class="board container"><!--board container-->
    <?php


// Display the session data using echo
echo "First Name: " . $first_name . "<br>";
echo "Middle Name: " . $middle_name . "<br>";
echo "Last Name: " . $last_name . "<br>";
echo "Contact Number: " . $contact_number . "<br>";
echo "Email: " . $email . "<br>";
echo "Affiliation: " . $affiliation . "<br>";
?>
</div>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        console.log("Document loaded"); // Log that the document has loaded
        let qrCodeContainer = document.getElementById("qrcode-container");
        console.log("QR Code Container:", qrCodeContainer); // Log the QR code container element
        let lastInsertedID = "<?php echo isset($_SESSION['lastInsertedID']) ? $_SESSION['lastInsertedID'] : ''; ?>";
        console.log("Last Inserted ID:", lastInsertedID); // Log the last inserted ID from PHP session

        if (lastInsertedID !== '') {
            console.log("Generating QR Code for ID:", lastInsertedID); // Log that QR code generation is starting
            // Generate QR Code using QRCode.js
            new QRCode(qrCodeContainer, lastInsertedID);
        } else {
            console.log("No ID available for QR Code"); // Log that no ID is available for QR code generation
            qrCodeContainer.innerHTML = "QR Code not available";
        }
    });
</script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>
    <script> 
        let date = new Date().toLocaleDateString('en-US', {  
            day:   'numeric',
            month: 'long',
            year:  'numeric' ,  
            weekday: 'long', 
        });   
        document.getElementById("currentDate").innerText = date; 

        setInterval( () => {
            let time = new Date().toLocaleTimeString('en-US',{ 
            hour: 'numeric',
            minute: 'numeric', 
            second: 'numeric',
            hour12: 'true',
        })  
        document.getElementById("currentTime").innerText = time; 

        }, 1000)
        

        let navItems = document.querySelectorAll(".nav-item");  //adding .active class to navitems 
        navItems.forEach(item => {
            item.addEventListener('click', ()=> { 
                document.querySelector('.active')?.classList.remove('active');
                item.classList.add('active');
                
                
            })
            
        })
     


    </script>
</body>
</html>