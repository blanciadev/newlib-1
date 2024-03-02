<?php
session_start();

// Check if the form is submitted and Borrower ID is provided
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['borrowId'])) {
    // Database connection
    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307); 
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Retrieve Borrower ID from the form
    $borrowerId = $_GET['borrowId'];

    // Your SQL query using $borrowerId goes here
    $sql = "SELECT 
        tbl_borrowdetails.BorrowDetails_ID,
        tbl_borrow.Borrow_ID,
        tbl_borrow.Borrower_ID,
        tbl_borrow.Accession_Code,
        tbl_books.Book_Title,
        tbl_borrowdetails.Quantity,
        tbl_borrow.Date_Borrowed,
        tbl_borrow.Due_Date,
        tbl_borrowdetails.tb_status
    FROM 
        tbl_borrow
    JOIN
        tbl_borrowdetails ON tbl_borrow.Borrow_ID = tbl_borrowdetails.Borrower_ID
    JOIN
        tbl_books ON tbl_borrow.Accession_Code = tbl_books.Accession_Code
    WHERE 
        tbl_borrowdetails.BorrowDetails_ID = '$borrowerId'";

    // Execute the SQL query
    $result = $conn->query($sql);

    // Close connection
    $conn->close();
} else {
    // Handle the case where Borrower ID is not provided
    echo "Error: Borrower_ID is not provided";
    // You can also redirect the user to the previous page here
}
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
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['borrower_id'])) {
            while ($row = $result->fetch_assoc()) {
                echo "<p><strong>Borrow ID:</strong> " . $row["BorrowDetails_ID"] . "</p>";
                echo "<p><strong>Borrow ID:</strong> " . $borrowerId . "</p>";
                echo "<p><strong>Visitor Id:</strong> " . $row["Borrower_ID"] . "</p>";
                echo "<p><strong>Accession Code:</strong> " . $row["Accession_Code"] . "</p>";
                echo "<p><strong>Book Title:</strong> " . $row["Book_Title"] . "</p>";
                echo "<p><strong>Quantity:</strong> " . $row["Quantity"] . "</p>";
                echo "<p><strong>Date Borrowed:</strong> " . $row["Date_Borrowed"] . "</p>";
                echo "<p><strong>Due Date:</strong> " . $row["Due_Date"] . "</p>";
                echo "<p><strong>Title:</strong> " . $row["tb_status"] . "</p>";
            }
        } else {
            echo "<p>Error: Borrower ID not provided.</p>";
        }
        ?>




    <tbody>
    <div id="statusMessage"></div>
    <button type="button" class="btn btn-primary" id="book_borrow">Calculate</button>


           
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
