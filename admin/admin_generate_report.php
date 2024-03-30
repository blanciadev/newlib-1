<?php


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VillaReadHub - Generate Report</title>



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
        <div class="user-header mr-3 d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
                <img src="https://github.com/mdo.png" alt="" width="50" height="50" class="rounded-circle me-2">
                <p>(ADMIN)</p>
            </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./admin_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./admin_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./admin_transactions.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transactions</a> </li>
            <li class="nav-item"> <a href="./admin_staff.php" class="nav-link link-body-emphasis"><i class='bx bxs-user'></i>Manage Staff</a> </li>
            <li class="nav-item"> <a href="./admin_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./admin_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <li class="nav-item active"> <a href="./admin_generate_report.php" class="nav-link link-body-emphasis"><i class='bx bxs-cloud'></i>Generate Report</a> </li>
            <hr>
            <li class="nav-item"> <a href="./admin_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
        
        
    </div>
        
    </div>
    <div class="board container"><!--board container--> 
 
   
    <div class="content">
        <div class="overview">
            <h3>Overview</h3>
            <div class="ovw-con">
            <?php
// Database connection
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query to count books borrowed per month
$countBooksQuery = "SELECT MONTH(Date_Borrowed) AS Month, COUNT(*) AS BooksBorrowed 
                    FROM tbl_borrow 
                    GROUP BY MONTH(Date_Borrowed)";
$countBooksResult = mysqli_query($conn, $countBooksQuery);

// Display the data in a table
echo '<table class="table">';
echo '<tr><th>Books Borrowed</th><th>Month</th><th>Books Borrowed</th><th>Visitors</th></tr>';

if (!$countBooksResult) {
    echo "<tr><td colspan='3'>Error fetching data: " . mysqli_error($conn) . "</td></tr>";
} else {
    // Fetch and display each row as a table row
    while ($row = mysqli_fetch_assoc($countBooksResult)) {
        // Query to count unique Borrower_ID for each month
        $month = $row['Month'];
        $uniqueVisitorsQuery = "SELECT COUNT(DISTINCT Borrower_ID) AS UniqueVisitors 
                                FROM tbl_borrow 
                                WHERE MONTH(Date_Borrowed) = $month";
        $uniqueVisitorsResult = mysqli_query($conn, $uniqueVisitorsQuery);
        $uniqueVisitorsCount = ($uniqueVisitorsResult) ? mysqli_fetch_assoc($uniqueVisitorsResult)['UniqueVisitors'] : 0;

        echo '<tr>';
        echo '<td>';
        echo '<td>' . date("F", mktime(0, 0, 0, $row['Month'], 1)) . '</td>'; // Display month name
        echo '<td>' . $row['BooksBorrowed'] . '</td>';
        echo '<td>' . $uniqueVisitorsCount . '</td>'; // Display unique visitors count for each row
        echo '</td>';
    }
}

// Close connection
mysqli_close($conn);
?>

<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query to count unique Borrower_ID and Date & Time per month from tbl_log
$countLogsQuery = "SELECT MONTH(`Date&Time`) AS Month, 
                        COUNT(DISTINCT Borrower_ID) AS UniqueBorrowers, 
                        COUNT(*) AS TotalLogs 
                    FROM tbl_log 
                    GROUP BY MONTH(`Date&Time`)";

$countLogsResult = mysqli_query($conn, $countLogsQuery);

// Display the data in a table

echo '<tr><th>LOGS</th><th>Month</th><th>Unique Borrowers</th><th>Total Logs</th></tr>';

if (!$countLogsResult) {
    echo "<tr><td colspan='3'>Error fetching data: " . mysqli_error($conn) . "</td></tr>";
} else {
    // Fetch and display each row as a table row
    while ($row = mysqli_fetch_assoc($countLogsResult)) {
        echo '<tr>';
        echo '<td>';
        echo '<td>' . date("F", mktime(0, 0, 0, $row['Month'], 1)) . '</td>'; // Display month name
        echo '<td>' . $row['UniqueBorrowers'] . '</td>';
        echo '<td>' . $row['TotalLogs'] . '</td>';
        echo '</td>';
    }
}


// Close connection
mysqli_close($conn);
?>
<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$totalFinesQuery = "SELECT MONTH(Date_Created) AS Month, 
                        SUM(Amount) AS TotalAmount,
                        COUNT(DISTINCT Borrower_ID) AS UniqueBorrowers 
                    FROM tbl_fines 
                    WHERE Payment_Date IS NOT NULL 
                    GROUP BY MONTH(Date_Created)";

$totalFinesResult = mysqli_query($conn, $totalFinesQuery);

// Display the data in a table

echo '<tr><th>Fines</th><th>Month</th><th>Total Amount of Fines</th><th>Borrower</th></tr>';

if (!$totalFinesResult) {
    echo "<tr><td colspan='2'>Error fetching data: " . mysqli_error($conn) . "</td></tr>";
} else {
    // Fetch and display each row as a table row
    while ($row = mysqli_fetch_assoc($totalFinesResult)) {
        echo '<tr>';
        echo '<td>';
        echo '<td>' . date("F", mktime(0, 0, 0, $row['Month'], 1)) . '</td>'; // Display month name
        echo '<td>' . $row['TotalAmount'] . '</td>';
        echo '<td>' . $row['UniqueBorrowers'] . '</td>';
        echo '</td>';
    }
}

echo '</table>';

// Close connection
mysqli_close($conn);
?>






            </div>
        </div>










  

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