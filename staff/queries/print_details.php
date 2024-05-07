<?php
session_start();


// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

$borrowerID = $_SESSION['bID'];


$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);

// Check if the email already exists in the database
$checkEmailQuery = "SELECT * FROM tbl_borrower WHERE Borrower_ID = '$borrowerID'";
$result = mysqli_query($conn, $checkEmailQuery);
if (mysqli_num_rows($result) > 0) {
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
                    <?php
    // Check if the session variable 'bID' is set
    if(isset($_SESSION['bID'])) {
        $borrowerID = $_SESSION['bID'];

        // Establish a connection to the database
        $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307);

        // Check if the connection was successful
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Prepare and execute the SQL query to retrieve borrower information
        $checkBorrowerQuery = "SELECT * FROM tbl_borrower WHERE Borrower_ID = '$borrowerID'";
        $result = mysqli_query($conn, $checkBorrowerQuery);

        // Check if there are any rows returned
        if (mysqli_num_rows($result) > 0) {
            // Fetch the data from the result set
            $row = mysqli_fetch_assoc($result);

            // Assign retrieved data to variables
            $first_name = $row['First_Name'];
            $middle_name = $row['Middle_Name'];
            $last_name = $row['Last_Name'];
            $contact_number = $row['Contact_Number'];
            $email = $row['Email'];
            $affiliation = $row['affiliation'];

            // Retrieve the image data from the database
            $imageData = $row['image_file'];

            // Output the user information and display the image
            echo '<div class="row mt-3">
                    <div class="col-md-12"> 
                        <img src="data:image/jpeg;base64,'.base64_encode($imageData).'" alt="User Image"> <br>
                        <p class="card-text"><strong>First Name:</strong> ' . $first_name . '</p>
                        <p class="card-text"><strong>Middle Name:</strong> ' . $middle_name . '</p>
                        <p class="card-text"><strong>Last Name:</strong> ' . $last_name . '</p>
                        <p class="card-text"><strong>Contact Number:</strong> ' . $contact_number . '</p>
                        <p class="card-text"><strong>Email:</strong> ' . $email . '</p>
                        <p class="card-text"><strong>Affiliation:</strong> ' . $affiliation . '</p>
                    </div>
                </div>';
        } else {
            echo "No records found for the provided borrower ID.";
        }
        
    // Unset or dispose of the session variable 'bID'
    unset($_SESSION['bID']);

        // Close the database connection
        mysqli_close($conn);
    } else {
        echo "Session variable 'bID' is not set.";
    }
?>


                </div>
                
    <div class="container-fluid">
    <!-- Bootstrap print button -->
    <button id="printButton" class="print-button btn btn-primary" onclick="printPage()">Print</button>

    <!-- Go to Staff Log button -->
    <a href="../staff_log.php" id="goToStaffLogButton" class="go-to-staff-log-button btn btn-primary">Go to Staff Log</a>
    </div>
    <script>
    // Function to hide the button before printing starts
    window.onbeforeprint = function() {
        document.getElementById('goToStaffLogButton').style.display = 'none';
    };

    // Function to show the button after printing ends
    window.onafterprint = function() {
        document.getElementById('goToStaffLogButton').style.display = 'block';
    };

    function printPage() {
        window.print();
        // Hide the print button after clicking
        document.getElementById('printButton').style.display = 'none';
    }
</script>
            </div>
            
        </div>
      
    </div>

  
</body>

</html>