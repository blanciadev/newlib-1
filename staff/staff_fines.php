
<?php

session_start();
// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fines</title>
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
          <!-- Display user image -->
          <?php
            $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307);
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
            <strong><span><?php $fname = $userData["First_Name"]; $lname = $userData["Last_Name"]; $userName = $fname." ". $lname;  echo $userName . "<br/>" . $_SESSION["role"]; ?></span></strong>
        </div> 
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./staff_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./staff_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./staff_transaction_dash.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transaction</a> </li>
           <li class="nav-item"> <a href="./staff_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item active"> <a href="./staff_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <hr>
            <li class="nav-item"> <a href="./staff_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
    </div>
    <div class=" board container"><!--board container-->
    <div class="header1">
        <div class="text">
            <div class="title">
                <h2>Fines</h2>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="overview1">
            <h3>Overview</h3>
            <div class="ovw-con">
                <div class="overview-item"> 
                    <div class="col"><br>
                        <h4>Lost Books:</h4>
                        <?php
                            // Query for LOST fines
                            $lostQuery = "SELECT 
                                                COUNT(*) AS record_count,
                                                SUM(Amount) AS total_amount
                                            FROM 
                                                tbl_fines
                                            WHERE 
                                                Reason = 'LOST';";
                            $lostResult = mysqli_query($conn, $lostQuery);
                            displayFinesSummary($lostResult);
                        ?>
                    </div>
                    <div class="col"><br>
                        <h4>Damaged Books:</h4>
                        <?php
                            // Query for DAMAGE fines
                            $damageQuery = "SELECT 
                                                COUNT(*) AS record_count,
                                                SUM(Amount) AS total_amount
                                            FROM 
                                                tbl_fines
                                            WHERE 
                                                Reason = 'DAMAGE';";
                            $damageResult = mysqli_query($conn, $damageQuery);
                            displayFinesSummary($damageResult);
                        ?>
                    </div>
                    <div class="col"><br>
                        <h4>Late Return:</h4>
                        <?php
                            // Query for GOOD CONDITION fines
                            $goodConditionQuery = "SELECT 
                                                COUNT(*) AS record_count,
                                                SUM(Amount) AS total_amount
                                            FROM 
                                                tbl_fines
                                            WHERE 
                                                Reason = 'GOOD CONDITION';";
                            $goodConditionResult = mysqli_query($conn, $goodConditionQuery);
                            displayFinesSummary($goodConditionResult);
                        ?>
                        <?php
                            // Function to display fines summary
                            function displayFinesSummary($result)
                            {
                                if ($result && mysqli_num_rows($result) > 0) {
                                    $row = mysqli_fetch_assoc($result);
                                    $recordCount = $row['record_count'];
                                    $totalAmount = $row['total_amount'];
                                    // echo "<p>Record Count: $recordCount</p>";
                                    echo "<p>Total Amount: $totalAmount</p>";
                                } else {
                                    echo "<p>Record Count: 0</p>";
                                    echo "<p>Total Amount: 0</p>";
                                }
                            }
                        ?>
                    </div> 
                </div>
            </div>
        </div>

        <div class="fines">
            <h3>History</h3>
            <div class="fines-con">
                <table class="table table-striped table-m" id="borrowerTable"> 
                    <thead class="bg-light sticky-top">
                        <tr>
                            <th scope="col">Borrower</th>
                            <th scope="col">Accession Code</th>
                            <th scope="col">Book Title</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Date Borrowed</th>
                            <th scope="col">Due Date</th>
                            <th scope="col">Fine Amount</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            // Database connection
                            $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307);
                            if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                            } 
                            // Number of records per page
                            $recordsPerPage = 10;  
                            // Calculate the total number of records
                            $totalRecordsQuery = "SELECT COUNT(*) AS total FROM tbl_borrowdetails";
                            $totalRecordsResult = mysqli_query($conn, $totalRecordsQuery);
                            $totalRecords = mysqli_fetch_assoc($totalRecordsResult)['total']; 
                            
                            // Calculate total number of pages
                            $totalPages = ceil($totalRecords / $recordsPerPage);
                        
                            // Determine current page number
                            $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
                
                            // Calculate the offset for the SQL query
                            $offset = ($currentPage - 1) * $recordsPerPage;
                    
                            // Fetch log records from the database with pagination and sorted by Date_Borrowed
                            $query = "SELECT DISTINCT
                            b.User_ID, 
                            b.Accession_Code, 
                            bk.Book_Title, 
                            b.Date_Borrowed, 
                            b.Due_Date, 
                            tbl_fines.Amount, 
                            b.Borrower_ID, 
                            tbl_borrowdetails.tb_status, 
                            tbl_borrowdetails.Quantity
                            FROM
                            tbl_borrow AS b
                            INNER JOIN
                            tbl_books AS bk
                            ON 
                                b.Accession_Code = bk.Accession_Code
                            INNER JOIN
                            tbl_borrower AS br
                            INNER JOIN
                            tbl_fines
                            ON 
                                b.Borrow_ID = tbl_fines.Borrower_ID
                            INNER JOIN
                            tbl_borrowdetails
                            WHERE
                            tbl_borrowdetails.tb_status = 'Returned'
                            ORDER BY
                            b.Date_Borrowed DESC
                            LIMIT
                            $offset, $recordsPerPage;
                            ";
                            
                            $result = mysqli_query($conn, $query);
                            // Initialize variables to store oldest and earliest dates
                            $oldestDate = PHP_INT_MAX;
                            $earliestDate = PHP_INT_MIN;
    
                            // Loop through each row in the result set
                            while ($row = mysqli_fetch_assoc($result)) {
                                $dateBorrowed = strtotime($row['Date_Borrowed']);
                                // Check if the current date is older than the oldestDate
                                if ($dateBorrowed < $oldestDate) {
                                    $oldestDate = $dateBorrowed;
                                }
                                
                                // Check if the current date is earlier than the earliestDate
                                if ($dateBorrowed > $earliestDate) {
                                    $earliestDate = $dateBorrowed;
                                } 
                                
                                echo '<tr>';
                                echo '<td>' . $row['Borrower_ID'] . '</td>'; 
                                echo '<td>' . $row['Accession_Code'] . '</td>'; 
                                echo '<td>' . $row['Book_Title'] . '</td>'; 
                                echo '<td>' . $row['Quantity'] . '</td>'; 
                                echo '<td>' . $row['Date_Borrowed'] . '</td>'; 
                                echo '<td>' . $row['Due_Date'] . '</td>'; 
                                echo '<td>' . $row['Amount'] . '</td>'; 
                                echo '<td>' . $row['tb_status'] . '</td>'; 
                                echo '</tr>';
                            }
                        ?>
                    </tbody>
                </table> 
            </div>
            <!-- Pagination -->
            <!-- <ul class="pagination">
                <?php
                // Display pagination links
                    //for ($i = 1; $i <= $totalPages; $i++) {
                        //echo '<li class="page-item';
                        //if ($i == $currentPage) echo ' active';
                           // echo '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                   // }
                ?>
            </ul> -->
        </div> 
    </div>
</div>

    <script>
    // Function to sort the table
    function sortTable() {
        var selectBox = document.getElementById("sortSelect");
        var selectedValue = selectBox.options[selectBox.selectedIndex].value;
        var table, rows, switching, i, shouldSwitch;

        table = document.getElementById("borrowerTable");
        switching = true;
        while (switching) {
            switching = false;
            rows = table.rows;
            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;
                var x = rows[i].getElementsByTagName("td")[selectedValue];
                var y = rows[i + 1].getElementsByTagName("td")[selectedValue];
                if (x && y) {
                    if (selectedValue == 4) {
                        // Sort from latest to oldest
                        if (new Date(x.innerHTML) < new Date(y.innerHTML)) {
                            shouldSwitch = true;
                            break;
                        }
                    } else if (selectedValue == 8) {
                        // Sort from oldest to latest
                        if (new Date(x.innerHTML) > new Date(y.innerHTML)) {
                            shouldSwitch = true;
                            break;
                        }
                    }
                } else {
                    // Handle case where selected table cell is undefined
                    console.error("Error: One or more table cells are undefined.");
                    return;
                }
            }
            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
            }
        }
    }

    // Add event listener to the dropdown
    document.getElementById("sortSelect").addEventListener("change", sortTable);

    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>
</body>
</html>