<?php

session_start();


$borrowDetailsId = $_GET['borrowId'];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307); 
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

            
        // Check if Borrower_ID is set in session
        if (isset($_SESSION['borrowerId'])) {
            $borrowerId = $_SESSION['borrowerId'];
            // Use the Borrower_ID as needed
        } else {
            // Handle the case where Borrower_ID is not set in session
        }

        // Check if BorrowDetails_ID is set in the URL
        if (isset($_GET['borrowId'])) {
            $borrowDetailsId = $_GET['borrowId'];
            // Use the BorrowDetails_ID as needed
        } else {
            // Handle the case where BorrowDetails_ID is not set in the URL
        }


        

    // Check if the 'borrowId' is set in the POST data
    // if (isset($_POST['borrowId'])) {
    //     $borrowId = $_POST['borrowId'];
        
    //     // Update the status in the database
    //     $updateSql = "UPDATE tbl_borrowdetails SET tb_status = 'Returned' WHERE BorrowDetails_ID = '$borrowId'";
    //     if ($conn->query($updateSql) === TRUE) {
    //         echo "<script>alert('Status updated successfully');</script>";
    //     } else {
    //         echo "<script>alert('Error updating status: " . $conn->error . "');</script>";
    //     }
    // }

    // Close connection
    $conn->close();
}


$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307); 
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT 
tbl_borrow.Borrow_ID,
tbl_borrow.Borrower_ID,
tbl_borrow.Accession_Code,
tbl_books.Book_Title,
tbl_borrowdetails.Quantity,
tbl_borrow.Date_Borrowed,
tbl_borrow.Due_Date
FROM 
tbl_borrowdetails
INNER JOIN 
tbl_borrow ON tbl_borrowdetails.Borrower_ID = tbl_borrow.Borrow_ID
INNER JOIN 
tbl_books ON tbl_borrow.Accession_Code = tbl_books.Accession_Code
WHERE 
tbl_borrow.Borrow_ID = '$borrowDetailsId' AND
tbl_borrowdetails.BorrowDetails_ID = '$borrowDetailsId'";

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VillaReadHub - Borrow</title>
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
        <div class="user-header mt-4 d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
            <img src="https://github.com/mdo.png" alt="" width="50" height="50" class="rounded-circle me-2">
            <strong><span><?php echo $_SESSION["staff_name"] ."<br/>"; echo $_SESSION["role"]; ?></span> </strong> 
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./staff_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./staff_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item active"> <a href="./staff_borrow.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Borrow</a> </li>
            <li class="nav-item"> <a href="./staff_return.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Return</a> </li>
            <li class="nav-item"> <a href="./staff_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./staff_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <hr>
            <li class="nav-item"> <a href="./staff_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
    </div>

    <form method="POST" action="staff_borrow.php">
   



    <?php
    // Database connection and SQL query
   

    $result = $conn->query($sql);
                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>".$row["BorrowDetails_ID"]."</td>";
                    echo "<td>".$row["Borrower_ID"]."</td>";
                    echo "<td>".$row["Accession_Code"]."</td>";
                    echo "<td>".$row["Book_Title"]."</td>";
                    echo "<td>".$row["Quantity"]."</td>";
                    echo "<td>".$row["Date_Borrowed"]."</td>";
                    echo "<td>".$row["Due_Date"]."</td>";
                    echo "<td>".$row["tb_status"]."</td>";
                                    
                   echo "<td>";
                    echo "<form class='update-form' method='GET' action='staff_borrow_details.php'>"; 
                    echo "<input type='hidden' name='borrowId' id='borrowId' value='".$row["BorrowDetails_ID"]."'>";
                    // Conditionally render the button based on the status
                    echo "<input type='hidden' name='borrowerId' value='".$row["Borrower_ID"]."'>";
                    if ($row["tb_status"] === 'Borrowed') {
                        echo "<button type='button' class='btn btn-primary btn-sm update-btn' onclick='redirectToBorrowDetails(" . $row["BorrowDetails_ID"] . ")'>Returned</button>";
                    } else {
                        echo "<button type='button' class='btn btn-secondary btn-sm' disabled>Returned</button>";
                    }
                    echo "<div class='update-message'></div>";
                    echo "</form>";
                    echo "</td>";

                                    
                    echo "</tr>";
                }
                
                    // Close connection
                    $conn->close();
                ?>




    <tbody>
    <div id="statusMessage"></div>
    <button type="button" class="btn btn-primary" id="book_borrow">Book Borrow</button>


           
    </tbody>
</table>

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
