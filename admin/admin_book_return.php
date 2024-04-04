<?php

session_start();

// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

// Database connection
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308); 
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all records
$sql = "SELECT DISTINCT
b.User_ID, 
b.Accession_Code, 
bk.Book_Title, 
bd.Quantity, 
b.Date_Borrowed, 
b.Due_Date, 
bd.tb_status, 
bd.Borrower_ID, 
bd.BorrowDetails_ID
FROM
tbl_borrowdetails AS bd
INNER JOIN
tbl_borrow AS b ON bd.Borrower_ID = b.Borrower_ID
INNER JOIN
tbl_books AS bk ON b.Accession_Code = bk.Accession_Code
INNER JOIN
tbl_borrower AS br ON b.Borrower_ID = br.Borrower_ID AND bd.Borrower_ID = br.Borrower_ID";

// Execute the SQL query
$result = mysqli_query($conn, $sql);
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
            <li class="nav-item"> <a href="./admin_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./admin_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item active"> <a href="./admin_transactions.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transactions</a> </li>
            <li class="nav-item"> <a href="./admin_staff.php" class="nav-link link-body-emphasis"><i class='bx bxs-user'></i>Manage Staff</a> </li>
            <li class="nav-item"> <a href="./admin_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./admin_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <li class="nav-item"> <a href="./admin_generate_report.php" class="nav-link link-body-emphasis"><i class='bx bxs-cloud'></i>Generate Report</a> </li>
            <hr>
            <li class="nav-item"> <a href="./admin_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
        
        
    </div>
    <div class="board container"><!--board container--> 
    <div class="header1">
            <div class="text">
                <div class="title">
                    <h2>Return List</h2>
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
    <table class="table table-hover table-sm">
        <thead>
            <tr>
            <tr>
                    <th>Borrow Id</th>
                    <th>Visitors Id</th>
                    <th>Accession Code</th>
                    <th>Book Title</th>
                    <th>Quantity</th>
                    <th>Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Action</th>
                    <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
                $conn =  mysqli_connect("localhost","root","root","db_library_2", 3308); 
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // SQL query
                $sql = "SELECT DISTINCT
                b.User_ID, 
                b.Accession_Code, 
                bk.Book_Title, 
                bd.Quantity, 
                b.Date_Borrowed, 
                b.Due_Date, 
                bd.tb_status, 
                bd.Borrower_ID, 
                bd.BorrowDetails_ID
            FROM
                tbl_borrowdetails AS bd
            INNER JOIN
                tbl_borrow AS b ON bd.Borrower_ID = b.Borrower_ID
            INNER JOIN
                tbl_books AS bk ON b.Accession_Code = bk.Accession_Code
            INNER JOIN
                tbl_borrower AS br ON b.Borrower_ID = br.Borrower_ID AND bd.Borrower_ID = br.Borrower_ID";
    
                $result = $conn->query($sql);
    

                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["BorrowDetails_ID"] . "</td>";
                    echo "<td>" . $row["Borrower_ID"] . "</td>";
                    echo "<td>" . $row["Accession_Code"] . "</td>";
                    echo "<td>" . $row["Book_Title"] . "</td>";
                    echo "<td>" . $row["Quantity"] . "</td>";
                    echo "<td>" . $row["Date_Borrowed"] . "</td>";
                    echo "<td>" . $row["Due_Date"] . "</td>";
                    echo "<td>" . $row["tb_status"] . "</td>";

                    echo "<td>";
                    echo "<form class='update-form' method='GET' action='staff_borrow_details.php'>";
                    echo "<input type='hidden' name='borrowId' id='borrowId' value='" . $row["BorrowDetails_ID"] . "'>";
                    echo "</form>";
                    // Conditionally render the button based on the status
                    echo "<input type='hidden' name='borrowerId' value='" . $row["Borrower_ID"] . "'>";

                    if ($row["tb_status"] === 'Borrowed') {
                        echo "<button type='button' class='btn btn-primary btn-sm update-btn' onclick='updateAndSetSession(" . $row["BorrowDetails_ID"] . ")'>UPDATE</button>";

                    } else {
                        echo "<button type='button' class='btn btn-secondary btn-sm' disabled>Update</button>";
                    }
                
                    echo "<div class='update-message'></div>";
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table>";

                // Close connection
                $conn->close();
            ?>
        </tbody>
    </table>
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

    

        let navItems = document.querySelectorAll(".nav-item");  //adding .active class to navitems 
        navItems.forEach(item => {
            item.addEventListener('click', ()=> { 
                document.querySelector('.active')?.classList.remove('active');
                item.classList.add('active');
                
                
            })
            
        })
 
</script>


<script>
    // Function to handle the update action and redirect to staff_return_transaction.php
    function updateAndSetSession(borrowId) {
        console.log("Borrow ID:", borrowId); // Debugging console log
        // Redirect to staff_return_transaction.php with the borrowId parameter
        window.location.href = "admin_book_return_transaction.php?borrowIdadmin=" + borrowId;
    }
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
        

    
     


    </script>
</body>
</html>
