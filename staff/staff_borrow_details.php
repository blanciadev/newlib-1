<?php
session_start();
// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

$borrowerId = $_GET['borrower_id'];


// Check if the Borrower ID is provided in the URL
if (isset($borrowerId)) {
    echo $borrowerId;
    // Database connection
    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308); 
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Retrieve Borrower ID from the URL
   
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
    // Handle the case where Borrower ID is not provided in the URL
    // echo "Error: Borrower_ID is not provided in the URL";
    // You can also redirect the user to the previous page here
    echo $_GET['borrower_id'];
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
            <img src="../images/lib-icon.png" style="width: 16%;" alt="lib-icon"/>
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
                }
            ?>
            <?php if (!empty($userData['image_data'])): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else: ?>
                <img src="../images/default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php echo $userData['First_Name'] . "<br/>" . $_SESSION["role"]; ?></span></strong> 
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./staff_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./staff_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./staff_transaction_dash.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transaction</a> </li>
             <li class="nav-item"> <a href="./staff_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./staff_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <hr>
            <li class="nav-item"> <a href="./staff_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="" data-bs-toggle="modal" data-bs-target="#logOut"  class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
    </div>

        <form method="POST" action="staff_book_borrow_process.php?borrowerId=<?php echo $borrowerId; ?>"> 
            <?php
                
                        if ($result->num_rows > 0) {
                            
                        echo "<p><strong>Borrow ID:</strong> " . $borrowerId . "</p>";
                        echo "<p><strong>Visitor Id:</strong> " . $row["Borrower_ID"] . "</p>";
                        echo "<p><strong>Accession Code:</strong> " . $row["Accession_Code"] . "</p>";
                        echo "<p><strong>Book Title:</strong> " . $row["Book_Title"] . "</p>";
                        echo "<p><strong>Quantity:</strong>10000</p>";
                        echo "<p><strong>Date Borrowed:</strong> " . $row["Date_Borrowed"] . "</p>";
                        echo "<p><strong>Due Date:</strong> " . $row["Due_Date"] . "</p>";
                        echo "<p><strong>Title:</strong> " . $row["tb_status"] . "</p>";
                        $accession_code = $row["Accession_Code"];
                        $_SESSION['Accession_Code'] = $accession_code;
                        echo "SQL Query: " . $sql;
                        }
            ?>
        </form>
        <div>
            <div id="statusMessage"></div> 
            <button type="button" class="btn btn-primary" id="book_borrow">Get Book</button>
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
        // Get the button element
        var button = document.getElementById("book_borrow");

        // Add click event listener to the button
        button.addEventListener("click", function() {
            // Retrieve the accession code from the session
            var accession_code = "<?php echo $_SESSION['Accession_Code']; ?>";
        
            // Construct the URL with the accession code as a query parameter
            var url = "staff_book_borrow_process.php?Code=" + accession_code + "&borrower_id=<?php echo $borrowerId; ?>";
            
            // Redirect to the constructed URL
            window.location.href = url;
        });

    </script>
</body>
</html>
