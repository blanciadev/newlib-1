
<?php

session_start();


// Database connection
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308); 
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Fetch log records from the database
$query = "SELECT
bd.BorrowDetails_ID, 
b.User_ID, 
b.Accession_Code, 
bk.Book_Title, 
bd.Quantity, 
b.Date_Borrowed, 
b.Due_Date, 
br.Borrower_ID, 
bd.tb_status, 
tbl_fines.Amount, 
br.*
FROM
tbl_borrowdetails AS bd
INNER JOIN
tbl_borrow AS b
ON 
    bd.Borrower_ID = b.Borrower_ID
INNER JOIN
tbl_books AS bk
ON 
    b.Accession_Code = bk.Accession_Code
INNER JOIN
tbl_borrower AS br
ON 
    bd.Borrower_ID = br.Borrower_ID
INNER JOIN
tbl_fines
ON 
    bd.BorrowDetails_ID = tbl_fines.Borrower_ID;
"; 
$result = mysqli_query($conn, $query);

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
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item "> <a href="./admin_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./admin_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./admin_transactions.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transactions</a> </li>
            <li class="nav-item"> <a href="./admin_staff.php" class="nav-link link-body-emphasis"><i class='bx bxs-user'></i>Manage Staff</a> </li>
            <li class="nav-item"> <a href="./admin_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item active"> <a href="./admin_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <li class="nav-item"> <a href="./admin_generate_report.php" class="nav-link link-body-emphasis"><i class='bx bxs-cloud'></i>Generate Report</a> </li>
            <hr>
            <li class="nav-item"> <a href="./admin_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
        
        
    </div>
    </div>

    <div class="container"><!--board container-->
    <div class="scrollable-table" style="max-height: 900px; overflow-y:auto;">
        <?php
        // Your existing PHP code to fetch and display table data
        ?>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Visitors Id</th>
                    <th scope="col">Borrower Id</th>
                    <th scope="col">First Name</th>
                    <th scope="col">Middle Name</th>
                    <th scope="col">Last Name</th>
                    <th scope="col">Contact Number</th>
                    <th scope="col">Fines</th>
                    <th scope="col">Date Borrowed</th>
                    <th scope="col">Due Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Loop through each row in the result set
                while($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                    echo '<td>' . $row['Borrower_ID'] . '</td>'; 
                    echo '<td>' . $row['First_Name'] . '</td>'; 
                    echo '<td>' . $row['Middle_Name'] . '</td>'; 
                    echo '<td>' . $row['Last_Name'] . '</td>'; 
                    echo '<td>' . $row['Contact_Number'] . '</td>'; 
                    echo '<td>' . $row['affiliation'] . '</td>'; 
                    echo '<td>' . $row['Amount'] . '</td>'; 
                    echo '<td>' . $row['Date_Borrowed'] . '</td>'; 
                    echo '<td>' . $row['Due_Date'] . '</td>'; 
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
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