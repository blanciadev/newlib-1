
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
    <title>VillaReadHub - Fines</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="./admin.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png ">
</head>
<body>
<div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" ><!--sidenav container-->
        <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
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
           // Fetch the First_Name from $userData
    $firstName = $userData['First_Name'];
    $role = $userData['tb_role'];

            }
            ?>
            <?php if (!empty($userData['image_data'])): ?>
                <!-- Assuming the image_data is in JPEG format, change the MIME type if needed -->
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else: ?>
                <!-- Change the path to your actual default image -->
                <img src="default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php echo  $firstName . "<br/>" .  $role; ?></span></strong>
    </div>
    <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item active"> <a href="./admin_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./admin_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./admin_transactions.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transactions</a> </li>
            <li class="nav-item"> <a href="./admin_staff.php" class="nav-link link-body-emphasis"><i class='bx bxs-user'></i>Manage Staff</a> </li>
            <li class="nav-item"> <a href="./admin_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./admin_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <li class="nav-item"> <a href="./admin_generate_report.php" class="nav-link link-body-emphasis"><i class='bx bxs-cloud'></i>Generate Report</a> </li>
            <hr>
            <li class="nav-item"> <a href="./admin_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
        
        
    </div>
  
</div>
<div class=" board container"><!--board container-->
    <div class="header1">
        <div class="text">
            <div class="title">
                <h2>Fines</h2>
            </div>
        </div>
    </div>

    
        
    <div class="books container">
    <!-- Sorting dropdown -->
    <div class="mb-3">
    <label for="sortSelect" class="form-label">Sort By:</label>
    <select class="form-select" id="sortSelect" onchange="sortTable()">
        <option value="4">Date Borrowed Latest to Oldest</option>
        <option value="8">Date Borrowed Oldest to Latest</option>
    </select>
</div>


    <table class="table table-striped table-m" id="borrowerTable">
        <!-- Table header -->
        <thead class="bg-light sticky-top">
            <tr>
                <th scope="col">Borrower ID</th>
                <th scope="col">Accession Code</th>
                <th scope="col">Book Title</th>
                <th scope="col">Quantity</th>
                <th scope="col">Date Borrowed</th>
                <th scope="col">Due Date</th>
                <th scope="col">Fine Amount</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <!-- Table body -->
        <tbody>
            <?php
            
            // Database connection
            $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
        
            // Number of records per page
            $recordsPerPage = 12;
        
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
        
            // Fetch log records from the database with pagination
            $query = "SELECT DISTINCT
                        bd.BorrowDetails_ID, 
                        b.User_ID, 
                        b.Accession_Code, 
                        bk.Book_Title, 
                        bd.Quantity, 
                        b.Date_Borrowed, 
                        b.Due_Date, 
                        br.Borrower_ID, 
                        bd.tb_status, 
                        tbl_fines.Amount
                    FROM
                        tbl_borrowdetails AS bd
                    INNER JOIN
                        tbl_borrow AS b ON bd.Borrower_ID = b.Borrower_ID
                    INNER JOIN
                        tbl_books AS bk ON b.Accession_Code = bk.Accession_Code
                    INNER JOIN
                        tbl_borrower AS br ON bd.Borrower_ID = br.Borrower_ID
                    INNER JOIN
                        tbl_fines ON bd.BorrowDetails_ID = tbl_fines.Borrower_ID
                    LIMIT $offset, $recordsPerPage";
        
            $result = mysqli_query($conn, $query);
        
        
            // Loop through each row in the result set
            while ($row = mysqli_fetch_assoc($result)) {
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

    <!-- Pagination -->
    <ul class="pagination">
        <?php
        // Display pagination links
        for ($i = 1; $i <= $totalPages; $i++) {
            echo '<li class="page-item';
            if ($i == $currentPage) echo ' active';
            echo '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
        }
        ?>
    </ul>
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
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <h4>LOST Fines Summary:</h4>
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

            <h4>DAMAGE Fines Summary:</h4>
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

   
        <div class="col-md-6">
            <h4>GOOD CONDITION Fines Summary:</h4>
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
        </div>
        
            <h4>PARTIALLY DAMAGE Fines Summary:</h4>
            <?php
            // Query for PARTIALLY DAMAGE fines
            $partiallyDamageQuery = "SELECT 
                                COUNT(*) AS record_count,
                                SUM(Amount) AS total_amount
                            FROM 
                                tbl_fines
                            WHERE 
                                Reason = 'PARTIALLY DAMAGE';";
            $partiallyDamageResult = mysqli_query($conn, $partiallyDamageQuery);
            displayFinesSummary($partiallyDamageResult);
            ?>
        </div>
    </div>
</div>

<?php
// Function to display fines summary
function displayFinesSummary($result)
{
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $recordCount = $row['record_count'];
        $totalAmount = $row['total_amount'];
        echo "<p>Record Count: $recordCount</p>";
        echo "<p>Total Amount: $totalAmount</p>";
    } else {
        echo "<p>No fines found</p>";
    }
}
?>


        

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