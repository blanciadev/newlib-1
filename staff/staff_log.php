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
        $errorMessage = "Borrower ID cannot start with '0'.";
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
            echo '<script>alert("Record inserted successfully."); window.location.href = "staff_log.php";</script>';
            exit();
        } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        }
     //   header("Location: staff_book_borrow_find.php?borrower_id=$borrower_id");
       // Make sure to exit after redirecting
    } else {
        // Borrower_ID is invalid
        $errorMessage = "Invalid Borrower ID.";
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
    <title>VillaReadHub - LOGS</title>
    <script src="../node_modules/html5-qrcode/html5-qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js" integrity="sha512-k/KAe4Yff9EUdYI5/IAHlwUswqeipP+Cp5qnrsUjTPCgl51La2/JhyyjNciztD7mWNKLSXci48m7cctATKfLlQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="./staff.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png ">

    <style>

    .img-responsive {
        max-width: 20%; /* This will make sure the image does not exceed the width of its container */
        height: auto; /* This will maintain the aspect ratio of the image */
    }

    </style>
</head>
<body>
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" ><!--sidenav container-->
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <h2>Villa<span>Read</span>Hub</h2> 
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon"/>
        </a><!--header container-->
        <div class="user-header  d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
      <!-- Display user image -->
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
                <!--default image -->
                <img src="../images/default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
       <strong><span><?php echo $userData['First_Name'] . "<br/>" . $_SESSION["role"]; ?></span></strong></div> 
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
    <div class="board container"><!--board container-->
    <div class="header1">
            <div class="text">
                <div class="title">
                    <h2>Log Record</h2>
                </div>
            </div>
            <div class="searchbar">
                <form action="">
                    <input type="search" id="searchInput"  placeholder="Search..." required>
                    <i class='bx bx-search' id="search-icon"></i>
                </form>
            </div>
    </div>
    <div class="books container">
        <table class="table table-striped">
            <thead class="bg-light sticky-top">
                <tr>
                    <th>QR Code</th>
                    <th>Borrower Name</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <?php
            // Database connection
            $conn_display_all = mysqli_connect("localhost", "root", "root", "db_library_2", 3308); 
            if ($conn_display_all->connect_error) {
                die("Connection failed: " . $conn_display_all->connect_error);
            }
            
            // SQL query to select all records from tbl_borrower and tbl_log
            $sql_display_all = "SELECT tbl_borrower.*, tbl_log.* FROM tbl_borrower INNER JOIN tbl_log ON tbl_borrower.Borrower_ID = tbl_log.Borrower_ID";
            $result_display_all = $conn_display_all->query($sql_display_all);

            // Close display connection
            $conn_display_all->close();
            ?>
    
            <tbody>
                <?php
                if ($result_display_all && $result_display_all->num_rows > 0) {
                    while ($row = $result_display_all->fetch_assoc()) {
                        echo "<tr>";
                        $imageData = base64_encode($row['image_file']);
                        echo "<td><img class='img-responsive' src='data:image/png;base64," . $imageData . "' /></td>";
                        echo "<td>" . $row['First_Name'] . " " . $row['Middle_Name'] . " " . $row['Last_Name'] . "</td>";
                        echo "<td>" . $row['Date_Time'] . "</td>";
                        // Add more columns as needed
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="btn-con">
        <a href="staff_log_qrscan.php" class="btn">Scan</a>
        <a href="staff_registeredList.php" class="btn">Registered List</a>
    </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>
    <script> 
      // JavaScript code for search functionality
      document.getElementById("searchInput").addEventListener("input", function() {
            let searchValue = this.value.toLowerCase();
            let rows = document.querySelectorAll("tbody tr");
            rows.forEach(row => {
                let cells = row.querySelectorAll("td");
                let found = false;
                cells.forEach(cell => {
                    if (cell.textContent.toLowerCase().includes(searchValue)) {
                        found = true;
                    }
                });
                if (found) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });

    </script> 
</body>
</html>
