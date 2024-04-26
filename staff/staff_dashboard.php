<?php

use PhpParser\Node\Stmt\Echo_;

include "../auth.php";
// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}


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


// Fetch data from tbl_log
$sql = "SELECT DATE_FORMAT(Date_Time, '%Y-%m') AS Month, COUNT(*) AS Visits
        FROM tbl_log
        GROUP BY DATE_FORMAT(Date_Time, '%Y-%m')
        ORDER BY DATE_FORMAT(Date_Time, '%Y-%m') ASC";
$result = mysqli_query($conn, $sql);

// Initialize arrays to hold the labels and data for the chart
$labels = [];
$data = [];


// Process the fetched data
while ($row = mysqli_fetch_assoc($result)) {
    // Add the month to labels array
    $labels[] = $row['Month'];

    // Add the number of visits to data array
    $data[] = $row['Visits'];
}

// Convert labels and data arrays to JSON format
$labelsJSON = json_encode($labels);
$dataJSON = json_encode($data);


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
    <link href="./staff.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png ">
</head>

<body>
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary"><!--sidenav container-->
        <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <h2>Villa<span>Read</span>Hub</h2>
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon" />
        </a><!--header container-->

        <div class="user-header d-flex flex-row flex-wrap align-content-center justify-content-evenly">
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
    <!--default image -->
    <img src="../images/default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
<?php endif; ?>
<strong><span><?php $fname = $userData["First_Name"]; $lname = $userData["Last_Name"]; $userName = $fname." ". $lname;  echo $userName . "<br/>" . $_SESSION["role"]; ?></span></strong>

        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item active"> <a href="./staff_dashboard.php" class="nav-link link-body-emphasis "> <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./staff_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./staff_transaction_dash.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transaction</a> </li>
            <li class="nav-item"> <a href="./staff_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./staff_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <hr>
            <li class="nav-item"> <a href="./staff_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="" data-bs-toggle="modal" data-bs-target="#logOut" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
    </div>
    <div class="board1 container"><!--board container-->
        <div class="header">
            <div class="text">
                <div class="title">
                    <h2>Dashboard</h2>
                </div>
                <div class="datetime">
                    <p id="currentDate"></p>
                    <p id="currentTime"></p>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="overview">
                <h3>Overview</h3>
                <div class="ovw-con">
                    <div class="totalbooks">
                        <?php
                        $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308); // database connection

                        // Query to get the total quantity of all books
                        $totalQuantityQuery = "SELECT SUM(Quantity) AS total_quantity FROM tbl_books";
                        $totalQuantityResult = mysqli_query($conn, $totalQuantityQuery);

                        // Check if the query was successful and fetch the total quantity
                        if ($totalQuantityResult && mysqli_num_rows($totalQuantityResult) > 0) {
                            $totalQuantityData = mysqli_fetch_assoc($totalQuantityResult);
                            $totalQuantity = $totalQuantityData['total_quantity'];

                            // Display the total quantity of all books
                            echo "<h3>" . $totalQuantity . "</h3>";
                        } else {
                            echo "No books found";
                        }

                        ?>
                    <div class="d-flex w-100 flex-wrap justify-content-around ">
                        <i class='bx bxs-book' style="font-size: 30pt;"></i>
                        <p>Total Books</p>
                    </div>   
                    </div>

                    <div class="line"></div>
                    
                    <div class="totalvisits">
                        <?php
                        $currentDate = date("Y-m-d");
                        // Query to count visits for the current date using the Date_Time column
                        $totalVisitsQuery = "SELECT COUNT(*) AS total_visits FROM tbl_log WHERE DATE(`Date_Time`) = '$currentDate'";
                        $totalVisitsResult = mysqli_query($conn, $totalVisitsQuery);

                        // Check if the query was successful and fetch the total visits
                        if ($totalVisitsResult && mysqli_num_rows($totalVisitsResult) > 0) {
                            $totalVisitsData = mysqli_fetch_assoc($totalVisitsResult);
                            $totalVisitsCount = $totalVisitsData['total_visits'];

                            // Display the total visits for the current date
                            echo "<h3>" . $totalVisitsCount . "</h3>";
                        } else {
                            echo "<h4>0</h4>"; // No visits found for the current date
                        }
                        ?>
                        <div class="d-flex w-100 flex-wrap justify-content-around ">
                            <i class='bx bxs-book-reader' style="font-size: 30pt;"></i>
                            <p>Total Visits</p>
                        </div>

                    </div>
                </div>
            </div>

            <div class="due-books">
    <h3 class="mb-3">Due Today</h3>
    <div class="due-books-container" style="max-height: 200px; overflow-y: auto;">
        <?php
        $totalVisits = "SELECT
        b.User_ID, 
        b.Accession_Code, 
        bk.Book_Title, 
        bd.Quantity, 
        b.Date_Borrowed, 
        b.Due_Date, 
        br.Borrower_ID, 
        bd.tb_status, 
        br.First_Name, 
        br.Last_Name, 
        b.Borrow_ID
    FROM
        tbl_borrowdetails AS bd
        INNER JOIN
        tbl_borrow AS b
        ON 
            bd.Borrower_ID = b.Borrower_ID AND
            bd.BorrowDetails_ID = b.Borrow_ID
        INNER JOIN
        tbl_books AS bk
        ON 
            b.Accession_Code = bk.Accession_Code
        INNER JOIN
        tbl_borrower AS br
        ON 
            bd.Borrower_ID = br.Borrower_ID
    WHERE
        b.Due_Date = CURDATE() AND
        bd.tb_status = 'Pending'";

        $totalVisits_run = mysqli_query($conn, $totalVisits);

        if ($totalVisits_run && mysqli_num_rows($totalVisits_run) > 0) {

            echo '<ol class="list-group list-group-numbered">';
            while ($row = mysqli_fetch_assoc($totalVisits_run)) {
            echo '<li class="list-group-item d-flex justify-content-between align-items-start">';
            echo '<div class="ms-2 me-auto">';
            echo '  <div class="fw-bold"><strong>' . $row['Book_Title'] . '</strong></div>
                        ' . $row['First_Name'] . ' ' . $row['Last_Name'] . '
                    </div>';
            echo '  <div class="view-btn">
                        <a href="#"><i class="bx bxs-bell"></i></a> 
                    </div>';
            echo '</li>';
            
            }
            echo '</ol>';
        } else {
            echo "<p>No Books Due Today.</p>";
        }
        ?>
    </div>
</div>

<script>
    // Function to handle the update action and redirect to staff_return_transaction.php
    function updateAndSetSession(borrowId) {
        console.log("Borrow ID:", borrowId); // Debugging console log
        // Redirect to staff_return_transaction.php with the borrowId parameter
        window.location.href = "staff_return_transaction.php?borrowId=" + borrowId;
    }
</script>






            <div class="stats">
                <h3>Statistics</h3>
                <div class="stats-con">
                    <div class="chart" style="width: 70%; height: 98%;">
                        <canvas id="myChart" width="450"></canvas>
                    </div>
                    <?php
                        $currentMonth = date("Y-m");

                        // Query to count visits for the current month and get the top 3 visitors
                        $topVisitorsQuery = "
                        SELECT
                            tbl_log.Borrower_ID, -- Specify the table for Borrower_ID
                            COUNT(*) AS visit_count,
                            tbl_borrower.First_Name,
                            tbl_borrower.Middle_Name,
                            tbl_borrower.Last_Name
                        FROM
                            tbl_log
                        INNER JOIN
                            tbl_borrower ON tbl_log.Borrower_ID = tbl_borrower.Borrower_ID
                        WHERE
                            DATE_FORMAT(tbl_log.Date_Time, '%Y-%m') = '$currentMonth'
                        GROUP BY
                            tbl_log.Borrower_ID, -- Specify the table for Borrower_ID
                            tbl_borrower.First_Name,
                            tbl_borrower.Middle_Name,
                            tbl_borrower.Last_Name
                        ORDER BY
                            visit_count DESC
                        LIMIT 3;
                        ";

                        $topVisitorsResult = mysqli_query($conn, $topVisitorsQuery);

                        // Check if the query was successful and fetch the top visitors
                        if ($topVisitorsResult && mysqli_num_rows($topVisitorsResult) > 0) {
                            echo "<div class='visitorRanking container-sm' style='width: 30%;
                                                                                height: 95%;
                                                                                background-color: palegreen;
                                                                                border-radius: 10px;
                                                                                padding: 5px;
                                                                                display: flex;
                                                                                flex-direction: column;
                                                                                flex-wrap: wrap;
                                                                                justify-content: center;
                                                                                align-content: stretch;'>";
                            echo "<ul class='list-group'>";

                            // Loop through the top visitors
                            while ($row = mysqli_fetch_assoc($topVisitorsResult)) {
                                $visitorName = $row['First_Name'] . " " . $row['Middle_Name'] . " " . $row['Last_Name'];
                                $visitCount = $row['visit_count'];

                                echo "<li class='list-group-item d-flex flex-column justify-content-between align-items-start' style='height:60px'>";
                                echo "<div class='w-100 d-flex flex-row justify-content-between' style='height: 20px;'>";
                                echo "<p style='font-size:12pt' class='fw-bold'>Top Visitor</p>";
                                echo "<span class='badge text-bg-primary rounded-pill'>$visitCount</span>";
                                echo "</div>";
                                echo "<small style='font-size:12px'>$visitorName</small>";
                                echo "</li>";
                            }

                            echo "</ul>";
                            echo "</div>";
                        } else {                            
                            echo "<div class='visitorRanking container-sm' style='width: 30%;
                                                                            height: 95%;
                                                                            background-color: transparent;
                                                                            border-radius: 10px;
                                                                            padding: 5px;
                                                                            display: flex;
                                                                            flex-direction: column;
                                                                            flex-wrap: wrap;
                                                                            justify-content: center;
                                                                            align-content: stretch;'>";
                            echo "<ul class='list-group'>";

                            echo "<li class='list-group-item d-flex flex-column justify-content-between align-items-start' style='height:40px'>";
                            echo "<div class='w-100 d-flex flex-row flex-wrap align-items-center justify-content-center' style='height: 20px; width:100%; text-align:center;'>";
                            echo "<p>No Visits Found</p>";
                            echo "</div>";
                            echo "</li>";

                            echo "</ul>";
                            echo "</div>";
                        }
                    ?>


                </div>
            </div>
            <div class="newbooks">
    <h3>What's New?</h3>
    <div class="newbooks-con">
        <?php

        // Calculate the date 3 days ago from today
        $threeDaysAgo = date('Y-m-d H:i:s', strtotime('-3 days'));

        // SQL query to fetch books inserted in the last 3 days or current along with author's name
        $sql = "SELECT tbl_books.*, tbl_authors.Authors_Name 
                FROM tbl_books 
                INNER JOIN tbl_authors ON tbl_books.Authors_ID = tbl_authors.Authors_ID 
                WHERE tbl_books.date_inserted >= '$threeDaysAgo'";

        $result = mysqli_query($conn, $sql);

        if ($result) {
            $totalBooksCount = mysqli_num_rows($result);

            // Display the books or process further as needed
            echo '<div class="list-group">';
            while ($row = mysqli_fetch_assoc($result)) {
                // Display book details or perform actions
                echo '<a href="#" class="list-group-item list-group-item-action">';
                echo '<div class="d-flex w-100 justify-content-between">';
                echo '<h5 class="mb-1">' . $row['Book_Title'] . '</h5>';
                echo '</div>';
                echo '<p class="mb-1">' . $row['Authors_Name'] . '</p>';
                echo '<small>' . $row['date_inserted'] . '</small>';
                echo '</a>';
            }
            echo '</div>';
        } else {
            echo "<p>No New Books</p>";
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
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

    <script>
        // Access PHP-generated JSON data
        const labels = <?php echo $labelsJSON; ?>;
        const data = <?php echo $dataJSON; ?>;

        const ctx = document.getElementById('myChart');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels, // Use labels from PHP
                datasets: [{
                    label: 'Monthly Visits',
                    data: data, // Use data from PHP
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>

</html>