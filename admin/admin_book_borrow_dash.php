<?php

session_start();

// Initialize a flag to track the validity of the Borrower ID
$isBorrowerIdValid = false;
$errorMessage = "";

// Check if the form is submitted and Borrower ID is provided
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['borrower_id'])) {
    // Database connection
    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307); 
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve Borrower_ID from the form
    $borrower_id = $_POST['borrower_id'];
    
    // Validate Borrower_ID against tbl_borrower table
    $sql_validate_borrower = "SELECT * FROM tbl_borrower WHERE Borrower_ID = '$borrower_id'";
    $result_validate_borrower = $conn->query($sql_validate_borrower);

    if ($result_validate_borrower->num_rows > 0) {
        // Borrower_ID is valid
        $isBorrowerIdValid = true;
        $_SESSION['borrower_id'] = $borrower_id;
    } else {
        // Borrower_ID is invalid
        $errorMessage = "Invalid Borrower ID.";
    }

    // Close connection
    $conn->close();
}

// If Borrower ID is valid, redirect to the next page
if ($isBorrowerIdValid) {
    $borrower_id = $_POST['borrower_id'];
    header("Location: admin_book_borrow_find.php?borrower_id=$borrower_id");
    exit(); // Make sure to exit after redirecting
}

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
        <div class="user-header mr-3 d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
                <img src="https://github.com/mdo.png" alt="" width="50" height="50" class="rounded-circle me-2">
                <p>(ADMIN)</p>
            </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./admin_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./admin_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item active"> <a href="./admin_transactions.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transactions</a> </li>
            <li class="nav-item"> <a href="./admin_staff.php" class="nav-link link-body-emphasis"><i class='bx bxs-user'></i>Manage Staff</a> </li>
            <li class="nav-item"> <a href="./admin_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./admin_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <li class="nav-item"> <a href="./admin_backup-restore.php" class="nav-link link-body-emphasis"><i class='bx bxs-cloud'></i>Backup & Restore</a> </li>
            <hr>
            <li class="nav-item"> <a href="./admin_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
        
        
    </div>
    <div class="board container"><!--board container--> 
            
            

    <form method="POST" action="">
    <table class="table table-striped">
    <thead>
        <tr>
            <th>Borrow Id</th>
            <th>Visitors Id</th>
            <th>Accession Code</th>
            <th>Book Title</th>
            <th>Quantity</th>
            <th>Date</th>
            <th>Due Date</th>
            <th>Status</th>
            <th></th> 
        </tr>
    </thead>
    <tbody>
    <div id="statusMessage"></div>

    <div class="mb-3">
        <label for="borrowerIdInput" class="form-label">Borrower ID</label>
        <input type="text" class="form-control" id="borrowerIdInput" name="borrower_id" required>
    </div>

    <button type="submit" class="btn btn-primary" id="book_borrow" <?php if (empty($_POST['borrower_id'])) echo "disabled"; ?>>
    Book Borrow
</button>
<?php if (!empty($errorMessage)): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo $errorMessage; ?>
    </div>
<?php endif; ?>





    <?php
    // Database connection and SQL query
    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307); 
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT
	bd.BorrowDetails_ID, 
	b.User_ID, 
	b.Accession_Code, 
	bk.Book_Title, 
	bd.Quantity, 
	b.Date_Borrowed, 
	b.Due_Date, 
	br.Borrower_ID, 
	bd.tb_status, 
	b.Borrower_ID, 
	bd.Borrower_ID
FROM
	tbl_borrowdetails AS bd
	INNER JOIN
	tbl_borrow AS b
	ON 
		bd.Borrower_ID = b.Borrow_ID
	INNER JOIN
	tbl_books AS bk
	ON 
		b.Accession_Code = bk.Accession_Code
	INNER JOIN
	tbl_borrower AS br
	ON 
		b.Borrower_ID = br.Borrower_ID;
            ";


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

                // if ($row["tb_status"] === 'Pending') {
                //     echo "<button type='button' class='btn btn-primary btn-sm update-btn' onclick='redirectToBorrowDetails(" . $row["BorrowDetails_ID"] . ")'>UPDATE</button>";
                // } else {
                //     echo "<button type='button' class='btn btn-secondary btn-sm' disabled>Returned</button>";
                // }
                
                echo "<div class='update-message'></div>";
                echo "</form>";
                echo "</td>";

                                    
                    echo "</tr>";
                }
                
                    // Close connection
                    $conn->close();
                ?>
           

    </tbody>
</table>

</div>

<script>
    function redirectToBorrowDetails(borrowId) {
        window.location.href = "staff_borrow_find.php?borrowId=" + borrowId;
    }
</script>

<script>
    
    document.addEventListener("DOMContentLoaded", function() {
        // Get the input field for the Borrower ID
        var borrowerIdInput = document.getElementById("borrowerIdInput");
        // Get the button
        var bookBorrowButton = document.getElementById("book_borrow");

        // Add an input event listener to the Borrower ID input field
        borrowerIdInput.addEventListener("input", function() {
            // Enable the button if there is input in the Borrower ID field
            if (borrowerIdInput.value.trim() !== "") {
                bookBorrowButton.removeAttribute("disabled");
            } else {
                // Otherwise, disable the button
                bookBorrowButton.setAttribute("disabled", "disabled");
            }
        });
    });
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