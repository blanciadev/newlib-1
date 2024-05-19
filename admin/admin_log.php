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

$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the current date in the format 'YYYY-MM-DD'
$currentDate = date('Y-m-d');

// Pagination variables for today's log
$results_per_page_today = 5;
$current_page_today = isset($_GET['page_today']) ? $_GET['page_today'] : 1;
$start_from_today = ($current_page_today - 1) * $results_per_page_today;

// SQL query to count records for the current date from tbl_log
$sql_count_today = "SELECT COUNT(*) AS total FROM tbl_log WHERE DATE(Date_Time) = '$currentDate'";
$result_count_today = $conn->query($sql_count_today);
$row_count_today = $result_count_today->fetch_assoc();
$total_pages_today = ceil($row_count_today["total"] / $results_per_page_today);

// SQL query to select records for the current date from tbl_borrower and tbl_log with pagination
$sql_display_today = "SELECT tbl_borrower.*, tbl_log.* FROM tbl_borrower INNER JOIN tbl_log ON tbl_borrower.Borrower_ID = tbl_log.Borrower_ID WHERE DATE(tbl_log.Date_Time) = '$currentDate' ORDER BY tbl_log.Date_Time DESC  ";
$result_display_today = $conn->query($sql_display_today);

// Pagination variables for all logs
$results_per_page = 5;
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$start_from = ($current_page - 1) * $results_per_page;

// SQL query to select all records with pagination
$sql_display_all = "SELECT tbl_borrower.*, tbl_log.* FROM tbl_borrower INNER JOIN tbl_log ON tbl_borrower.Borrower_ID = tbl_log.Borrower_ID ORDER BY tbl_log.Date_Time DESC ";
$result_display_all = $conn->query($sql_display_all);

// Total number of records for pagination
$sql_count_all = "SELECT COUNT(*) AS total FROM tbl_log";
$result_count_all = $conn->query($sql_count_all);
$row_count_all = $result_count_all->fetch_assoc();
$total_pages = ceil($row_count_all["total"] / $results_per_page);

// Close connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VillaReadHub - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="./admin.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png ">

    <style>
        .img-responsive {
            max-width: 20%;
            /* This will make sure the image does not exceed the width of its container */
            height: auto;
            /* This will maintain the aspect ratio of the image */
        }
    </style>
</head>

<body>
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary"><!--sidenav container-->
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <h2>Villa<span>Read</span>Hub</h2>
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon" />
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
            <?php if (!empty($userData['image_data'])) : ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else : ?>
                <!--default image -->
                <img src="../images/default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php echo $userData['First_Name'] . "<br/>" . $_SESSION["role"]; ?></span></strong>
        </div>

        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item "> <a href="./admin_dashboard.php" class="nav-link link-body-emphasis "> <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./admin_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./admin_transactions.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transactions</a> </li>
            <li class="nav-item"> <a href="./admin_staff.php" class="nav-link link-body-emphasis"><i class='bx bxs-user'></i>Manage Staff</a> </li>
            <li class="nav-item active"> <a href="./admin_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./admin_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <li class="nav-item"> <a href="./admin_generate_report.php" class="nav-link link-body-emphasis"><i class='bx bxs-report'></i>Generate Report</a> </li>
            <hr>
            <li class="nav-item"> <a href="./admin_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="" data-bs-toggle="modal" data-bs-target="#logOut" class="nav-link link-body-emphasis"><i class='bx bx-log-out'></i>Log Out</a> </li>
        </ul>


    </div>
    <div class="board container-fluid"><!--board container-->
    <div class="header1">
            <div class="text">
                <div class="title">
                    <h2>Log Record</h2>
                </div>
            </div>
            <div class="searchbar">
                <form action="">
                    <i class='bx bx-search' id="search-icon"></i>
                    <input type="search" id="searchInput"  placeholder="Search..." required>
                    
                </form>
            </div>
        </div>
        <div class="books container-fluid">
            <caption><em> Today </em></caption>
            <table class="table table-striped">
                <thead class="bg-light sticky-top">
                    <tr>
                        <th>Borrower ID</th>
                        <th>Borrower Name</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_display_today && $result_display_today->num_rows > 0) {
                        while ($row = $result_display_today->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['Borrower_ID'] . "</td>";
                            echo "<td>" . $row['First_Name'] . " " . $row['Middle_Name'] . " " . $row['Last_Name'] . "</td>";
                            echo "<td>" . $row['Date_Time'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No records found for the current date.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>  
            <caption><em>Recent</em></caption> 
            <table class="table table-striped">
                <thead class="bg-light sticky-top">
                    <tr>
                        <th>Borrower ID</th>
                        <th>Borrower Name</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_display_all && $result_display_all->num_rows > 0) {
                        while ($row = $result_display_all->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['Borrower_ID'] . "</td>";
                            echo "<td>" . $row['First_Name'] . " " . $row['Middle_Name'] . " " . $row['Last_Name'] . "</td>";
                            echo "<td>" . $row['Date_Time'] . "</td>";
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
        <a href="admin_log_qrscan.php" class="btn btn-primary mr-2">Scan</a>
            <a href="admin_registeredList.php" class="btn btn-secondary">Registered List</a>
    </div>
</div>
    <div class="btn-con mt-4">
            <a href="admin_log_qrscan.php" class="btn btn-primary mr-2">Scan</a>
            <a href="admin_registeredList.php" class="btn btn-secondary">Registered List</a>
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