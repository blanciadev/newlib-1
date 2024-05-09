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
    <title>VillaReadHub - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap"
        rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="./admin.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png ">
</head>

<body>
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary"><!--sidenav container-->
        <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <h2>Villa<span>Read</span>Hub</h2>
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon" />
        </a><!--header container-->

        <div class="user-header  d-flex flex-row flex-wrap align-content-center justify-content-evenly">
            <!--user container-->
            <?php
            $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307);
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
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image"
                    width="50" height="50" class="rounded-circle me-2">
            <?php else: ?>
                <!-- Change the path to your actual default image -->
                <img src="default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php echo $firstName . "<br/>" . $role; ?></span></strong>
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item "> <a href="./admin_dashboard.php" class="nav-link link-body-emphasis "> <i
                        class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./admin_books.php" class="nav-link link-body-emphasis"><i
                        class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item active"> <a href="./admin_transactions.php" class="nav-link link-body-emphasis"><i
                        class='bx bxs-customize'></i>Transactions</a> </li>
            <li class="nav-item"> <a href="./admin_staff.php" class="nav-link link-body-emphasis"><i
                        class='bx bxs-user'></i>Manage Staff</a> </li>
            <li class="nav-item"> <a href="./admin_log.php" class="nav-link link-body-emphasis"><i
                        class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./admin_fines.php" class="nav-link link-body-emphasis"><i
                        class='bx bxs-wallet'></i>Fines</a> </li>
            <li class="nav-item"> <a href="./admin_generate_report.php" class="nav-link link-body-emphasis"><i
                        class='bx bxs-cloud'></i>Generate Report</a> </li>
            <hr>
            <li class="nav-item"> <a href="./admin_settings.php" class="nav-link link-body-emphasis"><i
                        class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i
                        class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>


    </div>
    <div class="board container"><!--board container-->

        <form id="bookForm" method="POST">
            <!-- Fix Button -->
            <button type="submit" class="btn btn-primary" id="borrow" name="action" value="borrow">Book Borrow</button>
            <button type="submit" class="btn btn-primary" id="return" name="action" value="return">Book Return</button>
        </form>




        <div class="content">
            <div class="overview">
                <h3>Pending</h3>
                <div class="ovw-con">
                    <?php
                    // CHANGE THE PORT IF NEEDED
                    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307); // database connection
                    
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
            </div>
            <div class="overview-item">
                <h3>Top Borrower and Most Borrowed Book</h3>
                <?php
                // Database connection
                $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307);

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
LIMIT 1";
                $topBorrowerResult = mysqli_query($conn, $topBorrowerQuery);

                // Display the top borrower and most borrowed book
                if ($topBorrowerResult && mysqli_num_rows($topBorrowerResult) > 0) {
                    $topBorrowerData = mysqli_fetch_assoc($topBorrowerResult);
                    $topBorrowerID = $topBorrowerData['Borrower_ID'];
                    $borrowCount = $topBorrowerData['borrow_count'];
                    $name = $topBorrowerData['First_Name'];
                    $lname = $topBorrowerData['Last_Name'];

                    // Query to get the most borrowed book details
                    $mostBorrowedBookQuery = "SELECT
        tbl_borrowdetails.Accession_Code,
        COUNT(*) AS borrow_count,
        tbl_books.Book_Title
    FROM
        tbl_borrowdetails
    INNER JOIN
        tbl_books
    ON 
        tbl_borrowdetails.Accession_Code = tbl_books.Accession_Code
    WHERE
        tbl_borrowdetails.Borrower_ID = $topBorrowerID
    GROUP BY
        tbl_borrowdetails.Accession_Code
    ORDER BY
        borrow_count DESC
    LIMIT 1";
                    $mostBorrowedBookResult = mysqli_query($conn, $mostBorrowedBookQuery);

                    if ($mostBorrowedBookResult && mysqli_num_rows($mostBorrowedBookResult) > 0) {
                        $mostBorrowedBookData = mysqli_fetch_assoc($mostBorrowedBookResult);
                        $accessionCode = $mostBorrowedBookData['Accession_Code'];
                        $bookTitle = $mostBorrowedBookData['Book_Title'];

                        echo "<br><h4>Top Borrower:</h4>";
                        echo "<p>Borrower ID: $topBorrowerID</p>";
                        echo "<p>Complete Name: $name, $lname</p>";
                        echo "<p>Borrow Count: $borrowCount</p>";

                        echo "<h4>Most Borrowed Book:</h4>";
                        echo "<p>Accession Code: $accessionCode</p>";
                        echo "<p>Book Title: $bookTitle</p>";
                    } else {
                        echo "<p>No most borrowed book found for the top borrower</p>";
                    }
                } else {
                    echo "<p>No top borrower found</p>";
                }
                ?>
            </div>

        </div>

        <div class="board container"><!--board container-->
            <div class="content">
                <div class="overview">
                    <h3>Returned</h3>
                    <div class="ovw-con">
                        <?php
                        // CHANGE THE PORT IF NEEDED
                        $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307); // database connection
                        
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

                            echo "<br><h4>Total Returned Books:</h4>";
                            echo "<p>Total Quantity: $returnedQuantity (Records: $returnedCount)</p>";
                        } else {
                            echo "<p>No returned books found</p>";
                        }


                        ?>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"> </script>
    <script>
        document.getElementById('bookForm').addEventListener('submit', function (event) {
            event.preventDefault();
            const action = event.submitter.value;
            if (action === 'borrow') {
                window.location.href = 'admin_book_borrow_dash.php';
            } else if (action === 'return') {
                window.location.href = 'admin_return_dash.php';
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"> </script>
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


        let navItems = document.querySelectorAll(".nav-item"); //adding .active class to navitems 
        navItems.forEach(item => {
            item.addEventListener('click', () => {
                document.querySelector('.active')?.classList.remove('active');
                item.classList.add('active');


            })

        })
    </script>
</body>

</html>