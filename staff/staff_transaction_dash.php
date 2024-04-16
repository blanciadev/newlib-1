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
    <title>VillaReadHub - Transactions</title>
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
                <!-- Assuming the image_data is in JPEG format, change the MIME type if needed -->
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else: ?>
                <!-- Change the path to your actual default image -->
                <img src="default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
       <strong><span><?php echo $userData['First_Name'] . "<br/>" . $_SESSION["role"]; ?></span></strong></div> 
    
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./staff_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item "> <a href="./staff_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item active"> <a href="./staff_transaction_dash.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transaction</a> </li>
            <li class="nav-item"> <a href="./staff_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./staff_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>    
            <hr>
            <li class="nav-item"> <a href="./staff_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
         
    </div>
    <div class="board1 container"><!--board container-->
        <div class="header1">
            <div class="text">
                <div class="title">
                    <h2>Transactions</h2>
                </div>
            </div>
        </div>
    <div class="content">
        <div class="overview">
            <h3>Overview</h3>
            <div class="ovw-con">
                <div class="overview-item">
                    <h3>Pending</h3>
                    <?php
                        $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308); // database connection

                        // Query to get the total quantity of borrowed books
                        $borrowedQuery = "SELECT 
                            COUNT(*) AS borrowed_count,
                            SUM(Quantity) AS borrowed_quantity
                        FROM 
                            tbl_borrowdetails
                        WHERE 
                            tb_status = 'Pending'"; // Assuming 'Pending' status indicates borrowed books
                        $borrowedResult = mysqli_query($conn, $borrowedQuery);

                        // Query to get the total quantity of returned books
                        $returnedQuery = "SELECT 
                            COUNT(*) AS returned_count,
                            SUM(Quantity) AS returned_quantity
                        FROM 
                            tbl_borrowdetails
                        WHERE 
                            tb_status = 'Returned'";
                        $returnedResult = mysqli_query($conn, $returnedQuery);

                        // Display the total quantity of borrowed books
                        if ($borrowedResult && mysqli_num_rows($borrowedResult) > 0) {
                            $borrowedData = mysqli_fetch_assoc($borrowedResult);
                            $borrowedCount = $borrowedData['borrowed_count'];
                            $borrowedQuantity = $borrowedData['borrowed_quantity'];

                            echo "<h4>Total Borrowed Books: $borrowedQuantity</h4>";
                            echo "<p>Records: $borrowedCount</p>";
                        } else {
                            echo "<p>No borrowed books found</p>";
                        }
                    ?>
                </div>
                <div class="overview-item">
                    <h3>Returned</h3>
                    <?php
                        // CHANGE THE PORT IF NEEDED
                        $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308); // database connection

                        // Query to get the total quantity of borrowed books
                        $borrowedQuery = "SELECT 
                            COUNT(*) AS borrowed_count,
                            SUM(Quantity) AS borrowed_quantity
                        FROM 
                            tbl_borrowdetails
                        WHERE 
                            tb_status = 'Pending'"; // Assuming 'Pending' status indicates borrowed books
                        $borrowedResult = mysqli_query($conn, $borrowedQuery);

                        // Query to get the total quantity of returned books
                        $returnedQuery = "SELECT 
                            COUNT(*) AS returned_count,
                            SUM(Quantity) AS returned_quantity
                        FROM 
                            tbl_borrowdetails
                        WHERE 
                            tb_status = 'Returned'";
                        $returnedResult = mysqli_query($conn, $returnedQuery);

                        // Display the total quantity of returned books
                        if ($returnedResult && mysqli_num_rows($returnedResult) > 0) {
                            $returnedData = mysqli_fetch_assoc($returnedResult);
                            $returnedCount = $returnedData['returned_count'];
                            $returnedQuantity = $returnedData['returned_quantity'];

                            echo "<br><h4>Total Returned Books:$returnedQuantity</h4>";
                            echo "<p>Records: $returnedCount</p>";
                        } else {
                            echo "<p>No returned books found</p>";
                        }


                    ?>
                </div>
            </div>
        </div>
        <div class="duebooks"> <!--Borrowers dont change class name-->
        <h3>Top Borrowers</h3>
            <div class="duebooks-con" style="max-height: 400px; overflow-y: auto; padding-top: 20px;">
                <!--ADD CODE HERE... top 3 borrowers + borrowed book count-->
            </div>
        </div>
        <div class="stats">
            <h3>Borrow / Return</h3>
            <div class="stats-con">
                <div class="buttons">
                        <a href="staff_borrow_dash.php" class="btn btn-lg btn-primary">Borrow Book</a>
                        <a href="staff_return_dash.php" class="btn btn-lg btn-primary">Return Book</a>
                </div>
            </div>
        </div>
        <div class="newbooks"><!--books dont change class name-->
            <h3>Most Borrowed Book</h3>
            <div class="newbooks-con">
                <!--ADD CODE HERE... top 3 most borrowed books + borrower count-->
            </div>
        </div>
        
    </div>
    
</div>


<!-- <style>
     .overview {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
}

.ovw-con {
    display: flex;
}

.overview-item {
    flex: 1;
    border: 1px solid #ccc;
    padding: 20px;
    border-radius: 5px;
    margin-right: 10px;
}

.overview-item h3 {
    margin-top: 0;
}

.buttons {
    text-align: center;
    margin-top: 20px;
}

</style> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>
</body>
</html>