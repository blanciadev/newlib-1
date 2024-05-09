<?php

session_start();

// Initialize a flag to track the validity of the Borrower ID
$isBorrowerIdValid = false;
$errorMessage = "";

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
                 window.location.href = "staff_log.php";
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



// Check if the form is submitted and Borrower ID is provided
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['borrower_id'])) {
    // Database connection
    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308); 
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    // Retrieve Borrower_ID from the form
    $borrower_id = $_POST['borrower_id'];
    if (substr($borrower_id, 0, 1) === '0') {
        // Borrower_ID starts with '0', flag as error
          echo '<script>
    // Call showToast with "error" message type after encountering an error
    showToast("error", "Error Entry.");
</script>';

    } else {
        // Check if a log entry already exists for the specified Borrower ID and the current date
        $currentDate = date("Y-m-d");
        $sql_check_log = "SELECT * FROM tbl_log WHERE Borrower_ID = '$borrower_id' AND DATE(`Date_Time`) = '$currentDate'";
        $result_check_log = $conn->query($sql_check_log);

        if ($result_check_log->num_rows > 0) {
            // Log entry already exists for the current date, display error message
            // $errorMessage = "A log entry for this borrower already exists for today.";
           echo '<script>
            // Call showToast with "error" message type after encountering an error
            showToast("error", "A log entry for this borrower already exists for today.");
        </script>';


        } else {
            // Validate Borrower_ID against tbl_borrower table
            $sql_validate_borrower = "SELECT * FROM tbl_borrower WHERE Borrower_ID = '$borrower_id'";
            $result_validate_borrower = $conn->query($sql_validate_borrower);

            if ($result_validate_borrower->num_rows > 0) {
                // Borrower_ID is valid
                $isBorrowerIdValid = true;
                $_SESSION['borrower_id'] = $borrower_id;

                // SQL query to insert data with auto-increment Log_ID and current timestamp
                $sql = "INSERT INTO tbl_log (Borrower_ID, `Date_Time`) 
                VALUES ($borrower_id, NOW())";

                if ($conn->query($sql) === TRUE) {
                    // echo '<script>alert("Record inserted successfully."); window.location.href = "staff_log.php";</script>';
                 echo '<script>
        // Call showToast with "success" message type after successful insertion
        showToast("success", "Record inserted successfully.");
    </script>';

                 
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                // Borrower_ID is invalid
                  echo '<script>
                    // Call showToast with "error" message type after encountering an error
                    showToast("error", "Invalid ID.");
                     </script>';

            }
        }
    }


    // Close connection
    $conn->close();
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Visitor Card</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="./staff.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png ">
</head>
<body>

    <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" ><!--sidenav container-->
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <h2>Villa<span>Read</span>Hub</h2> 
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon"/>
        </a><!--header container-->
        <div class="user-header  d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
            <?php
                $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
                $userID = $_SESSION["User_ID"];
                $sql = "SELECT User_ID, First_Name, Middle_Name, Last_Name, tb_role, Contact_Number, E_mail, tb_address, image_data 
                        FROM tbl_employee 
                        WHERE User_ID = $userID";
                $result = mysqli_query($conn, $sql);
                if (!$result) {
                    echo "Error: " . mysqli_error($conn);
                } else {
                    $userData = mysqli_fetch_assoc($result);
                }
            ?>
            <?php if (!empty($userData['image_data'])): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else: ?>
                <img src="../images/default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php echo $userData['First_Name'] . "<br/>" . $_SESSION["role"]; ?></span></strong>
        </div> 
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./staff_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./staff_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./staff_transaction_dash.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transaction</a> </li>
           <li class="nav-item active"> <a href="./staff_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./staff_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <hr>
            <li class="nav-item"> <a href="./staff_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="" data-bs-toggle="modal" data-bs-target="#logOut" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
    </div>
    <style>
        main {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #reader {
            width: 600px;
        }
        #result {
            text-align: center;
            font-size: 1.5rem;
        }
    </style>
    <div class="board1 container"><!--board container-->
        <div class="header1">
            <div class="text">
                <div class="back-btn">
                    <a href="./staff_log.php"><i class='bx bx-arrow-back'></i></a>
                </div>
                <div class="title">
                    <h2>Scan Visitor Card</h2>
                </div>
            </div>
        </div>
        <div class="books container">
            <main>
                <div id="reader"></div>
                <div id="result"></div>
            </main>

            <div id="statusMessage"></div>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <label for="borrower_id">Or Enter Borrower ID:</label>
                  <!-- Bootstrap input field -->
                <input type="text" class="form-control" id="borrower_id" name="borrower_id" placeholder="Enter Borrower ID">
                <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
            </form>

            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $errorMessage; ?>
                </div>
            <?php endif; ?>
        </div>
        <button class="btn btn-success showToast">Show Toast</button>
    </div>

    <!--Logout Modal -->
    <div class="modal fade" id="logOut" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Logging Out...</h1>
                </div>
                <div class="modal-body">
                    Do you want to log out?
                </div>
                <div class="modal-footer d-flex flex-row justify-content-center">
                    <a href="javascript:history.go(0)"><button type="button" class="btn" data-bs-dismiss="modal">Cancel</button></a>
                    <a href="../logout.php"><button type="button" class="btn">Log Out</button></a>
                </div>
            </div>
        </div>
    </div>
        
    <div class="toastNotif" class="hide">
        <div class="toast-content">
            <i class='bx bx-check check'></i>

            <div class="message">
                <span class="text text-1">Success</span><!-- this message can be changed to "Success" and "Error"-->
                <span class="text text-2"></span> <!-- specify based on the if-else statements -->
            </div>
        </div>
        <i class='bx bx-x close'></i>
        <div class="progress"></div>
    </div>
    
    <script src="../node_modules/html5-qrcode/html5-qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js" integrity="sha512-k/KAe4Yff9EUdYI5/IAHlwUswqeipP+Cp5qnrsUjTPCgl51La2/JhyyjNciztD7mWNKLSXci48m7cctATKfLlQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>
   
   
    <script>
        //Toast Notification 
        const btn = document.querySelector(".showToast"),
            toast = document.querySelector(".toastNotif"),
            close = document.querySelector(".close"),
            progress = document.querySelector(".progress");

        btn.addEventListener("click", () => { // showing toast
            console.log("showing toast")
            toast.classList.add("showing");
            progress.classList.add("showing");
            setTimeout(() => {
                toast.classList.remove("showing");
                progress.classList.remove("showing");
                console.log("hide toast after 5s")
            }, 5000);
        });

        close.addEventListener("click", () => { // closing toast
            toast.classList.remove("showing");
            progress.classList.remove("showing");
        });

        const scanner = new Html5QrcodeScanner('reader', { 
            // Scanner will be initialized in DOM inside element with id of 'reader'
            qrbox: {
                width: 250,
                height: 250,
            },  // Sets dimensions of scanning box (set relative to reader element width)
            fps: 20, // Frames per second to attempt a scan
        });


        scanner.render(success, error);
        // Starts scanner

        function success(result) {
            // Set the scanned result as the value of the input field
            document.getElementById('borrower_id').value = result;

            // Clear the scanning instance
            scanner.clear();

            // Remove the reader element from the DOM since it's no longer needed
            document.getElementById('reader').remove();
        }


        function error(err) {
            // console.error(err);
            // Prints any errors to the console
        }

        function redirectToBorrowDetails(borrowId) {
            window.location.href = "staff_borrow_find.php?borrowId=" + borrowId;
        }

        document.addEventListener("DOMContentLoaded", function() {
            console.log("DOMContentLoaded event fired.");

            var borrowerIdInput = document.getElementById("borrower_id");
            var borrowForm = document.querySelector('form'); // Get the form element

            // Function to submit the form if the borrower ID input field has a value
            function checkAndSubmitForm() {
                // Check if the input field has a value
                if (borrowerIdInput.value.trim() !== "") {
                    console.log("Input field has a value. Submitting form.");
                    borrowForm.submit(); // Submit the form
                } else {
                    console.log("Input field is empty.");
                }
            }

            // Check and submit the form every 2 seconds
            setInterval(checkAndSubmitForm, 5000);
        });
    </script>
</body>
</html>
