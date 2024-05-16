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
    <title>Transactions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="./admin.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png ">
</head>

<body>
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary"><!--sidenav container-->
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <h2>Villa<span>Read</span>Hub</h2>
            <img src="../images/lib-icon.png" style="width: 16%;" alt="lib-icon" />
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
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else: ?>
                <img src="../images/default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php echo $userData['First_Name'] . "<br/>" . $_SESSION["role"]; ?></span></strong>
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./admin_dashboard.php" class="nav-link link-body-emphasis "> <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item "> <a href="./admin_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item active"> <a href="./admin_transaction_dash.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transaction</a> </li>
            <li class="nav-item"> <a href="./admin_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./admin_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <hr>
            <li class="nav-item"> <a href="./admin_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a  href="" data-bs-toggle="modal" data-bs-target="#logOut"  class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>

    </div>
    <div class="board1 container-fluid"><!--board container-->
    <div class="header">
            <div class="text">
                <div class="title">
                    <h2>Transactions</h2>
                </div>
            </div>
            <div class="datetime">
                    <p id="currentTime" style="font-size:1rem;font-weight: 700; margin:0%;"></p>
                    <p id="currentDate" style="font-size: 10pt;margin:0%;"></p>
                </div>
        </div>
        <div class="content">
            <div class="overview">
                <h3>Overview</h3>
                <div class="ovw-con">
                    <div class="totalbooks">
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

                                echo "<h3>$borrowedQuantity</h3>";
                            } else{
                                echo "<p>No borrowed books found</p>";
                            }
                        ?>
                        <div class="d-flex w-100 flex-wrap justify-content-start gap-2">
                            <i class='bx bxs-book' style="font-size: 30pt;"></i>
                            <p>Borrowed</p>
                        </div>
                    </div>

                    <div class="line"></div>
                    
                    <div class="totalvisits">
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

                            // Display the total quantity of returned books
                            if ($returnedResult && mysqli_num_rows($returnedResult) > 0) {
                                $returnedData = mysqli_fetch_assoc($returnedResult);
                                $returnedCount = $returnedData['returned_count'];
                                $returnedQuantity = $returnedData['returned_quantity'];

                                echo "<h3>$returnedQuantity</h3>";
                            } else {
                                echo "<h3>0</h3>";
                            }
                        ?>
                        <div class="d-flex w-100 flex-wrap justify-content-start gap-2">
                            <i class='bx bxs-book' style="font-size: 30pt;"></i>
                            <p>Returned</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="topBorrowers">
                <h3>Top Borrowers</h3>
                <div class="topBorrowers-con">  
                    <?php
                        // Database connection
                        $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);

                        // Query to get the top borrower based on Borrower_ID
                        $topBorrowerQuery = "SELECT
                            tbl_borrowdetails.Borrower_ID, 
                            COUNT(*) AS borrow_count, 
                            tbl_borrower.First_Name, 
                            tbl_borrower.Middle_Name, 
                            tbl_borrower.Last_Name
                        FROM
                            tbl_borrowdetails
                        INNER JOIN
                            tbl_borrower
                        ON 
                            tbl_borrowdetails.Borrower_ID = tbl_borrower.Borrower_ID
                        GROUP BY
                            tbl_borrowdetails.Borrower_ID
                        ORDER BY
                            borrow_count DESC
                        LIMIT 3";
                        $topBorrowerResult = mysqli_query($conn, $topBorrowerQuery);

                        // Display the top three borrowers
                        if ($topBorrowerResult && mysqli_num_rows($topBorrowerResult) > 0) {
                            echo "<ul class='list-group' style='width:98%'>";
                            while ($topBorrowerData = mysqli_fetch_assoc($topBorrowerResult)) {
                                $topBorrowerID = $topBorrowerData['Borrower_ID'];
                                $borrowCount = $topBorrowerData['borrow_count'];
                                $name = $topBorrowerData['First_Name'];
                                $lname = $topBorrowerData['Last_Name'];

                                echo "<li class='list-group-item d-flex flex-column justify-content-between align-items-start' style='height:40%; width:100%'>";
                                echo "<div class='w-100 d-flex flex-row justify-content-between' style='height: 20px;'>";
                                echo "<p style='font-size:12pt' class='fw-bold'>$topBorrowerID</p>";
                                echo "<span class='badge text-bg-primary rounded-pill'>$borrowCount</span>";
                                echo "</div>";
                                echo "<small style='font-size:12px'>$name $lname</small>";
                                echo "</li>";
                            }
                            echo "</ul>";
                        } else {
                            echo "<p>No Borrower Found</p>";
                        }
                    ?>
                </div>
            </div>

            <div class="stats">
                <h3>Borrow / Return</h3>
                <div class="stats-con">
                    <div class="buttons">
                        <a href="admin_book_borrow_dash.php" class="btn btn-lg btn-primary">Borrow Book</a>
                        <a href="admin_return_dash.php" class="btn btn-lg btn-primary">Return Book</a>
                    </div>
                </div>
            </div>

            <div class="mostbooks">
                <h3>Most Borrowed Books</h3>
                <div class="mostbooks-con">
                    <?php
                        // Check if $topBorrowerID is set and not empty
                        if(isset($topBorrowerID) && !empty($topBorrowerID)) {
                            // Execute the SQL query to retrieve the most borrowed books details
                            $mostBorrowedBooksQuery = "SELECT
                                    tbl_borrowdetails.Accession_Code,
                                    COUNT(*) AS borrow_count,
                                    tbl_books.Book_Title
                                FROM
                                    tbl_borrowdetails
                                INNER JOIN
                                    tbl_books
                                ON 
                                    tbl_borrowdetails.Accession_Code = tbl_books.Accession_Code
                                GROUP BY
                                    tbl_borrowdetails.Accession_Code
                                ORDER BY
                                    borrow_count DESC
                                LIMIT 4";

                            $mostBorrowedBooksResult = mysqli_query($conn, $mostBorrowedBooksQuery);

                                if ($mostBorrowedBooksResult && mysqli_num_rows($mostBorrowedBooksResult) > 0) {
                                    echo "<ul class='list-group' style='width:98%'>";
                                    while ($row = mysqli_fetch_assoc($mostBorrowedBooksResult)) {
                                        $accessionCode = $row['Accession_Code'];
                                        $bookTitle = $row['Book_Title'];
                                        $borrowCount = $row['borrow_count'];

                                        echo "<li class='list-group-item d-flex flex-column justify-content-between align-items-start' style='height:50%; width:100%'>";
                                        echo "<div class='w-100 d-flex flex-row justify-content-between' style='height: 20px;'>";
                                        echo "<p style='font-size:12pt' class='fw-bold'>$accessionCode</p>";
                                        echo "<span class='badge text-bg-primary rounded-pill'>$borrowCount</span>";
                                        echo "</div>";
                                        echo "<small style='font-size:12px'>$bookTitle</small>";
                                        echo "</li>";
                                    }
                                    echo "</ul>";
                                } else {
                                    echo "<p>No Book Found</p>";
                                }
                        } else {
                            echo "<p>No Book Found</p>";
                        }
                    ?>
                </div>
            </div>
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
        let date = new Date().toLocaleDateString('en-US', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            weekday: 'long',
        });
        document.getElementById("currentDate").innerText = date;

        setInterval(() => {
            let time = new Date().toLocaleTimeString('en-US', {
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
