<?php
    session_start();
    // Check if the User_ID session variable is not set or empty
    if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
        // Redirect to index.php
        header("Location: ../index.php");
        exit(); // Ensure script execution stops after redirection
    }

    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308); // database connection

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
    <title>Dashboard</title>
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
            <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else: ?>
            <img src="default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php echo  $firstName . "<br/>" .  $role; ?></span></strong>
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item active"> <a href="./admin_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./admin_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./admin_transactions.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transactions</a> </li>
            <li class="nav-item"> <a href="./admin_staff.php" class="nav-link link-body-emphasis"><i class='bx bxs-user'></i>Manage Staff</a> </li>
            <li class="nav-item"> <a href="./admin_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./admin_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <li class="nav-item"> <a href="./admin_generate_report.php" class="nav-link link-body-emphasis"><i class='bx bxs-cloud'></i>Generate Report</a> </li>
            <hr>
            <li class="nav-item"> <a href="./admin_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
    </div>
    <div class="board1 container-fluid"><!--board container--> 
        <div class="header">
            <div class="text">
                <div class="title">
                    <h2>Dashboard</h2>
                    <p>Welcome! <strong>Administrator</strong></p>
                </div> 
            </div> 
            <div class="datetime">
                    <p id = "currentTime" style="font-size:1rem;font-weight: 700; margin:0%;"></p>
                    <p id = "currentDate" style="font-size: 10pt;margin:0%;"></p>
                </div>
        </div>
        <div class="content">
        <div class="overview">
                <h3>Overview</h3>
                <div class="ovw-con">
                    <div class="totalbooks">
                        <?php
                            $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308 ); // database connection

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
            <div class="duebooks">
                <h3 class="mb-3">Due Today</h3>
                <div class="duebooks-con" style="overflow-y:auto;">
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
                        echo '<ol class="list-group list-group-numbered" style="height:100%; width:100%">';
                        while ($row = mysqli_fetch_assoc($totalVisits_run)) {
                            echo '<li class="list-group-item d-flex justify-content-between align-items-start">';
                            echo '<div class="ms-2 me-auto">';
                            echo '  <div class="fw"><strong>' . $row['Book_Title'] . '</strong></div>
                                            ' . $row['First_Name'] . ' ' . $row['Last_Name'] . '
                                        </div>';
                            echo '<div class="view-btn">
                                        <input type="hidden" class="borrowerId" value="' . $row['Borrower_ID'] . '">
                                        <a href="#" onclick="sendEmail(' . $row['Borrower_ID'] . ')">
                                        <i class="bx bxs-bell bellIcon"></i>
                                    </a>
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
                            $visitorName = $row['First_Name'] . " " . $row['Last_Name'];
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
            <div class="request-books">
                <h3>Pending Request</h3>
                <div class="request-books-con" style="overflow-y: scroll;">
                    <?php
                        // Database connection
                        $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
    
                        // Check connection
                        if (!$conn) {
                            die("Connection failed: " . mysqli_connect_error());
                        }
                        // Query to fetch data from tbl_requestbooks where status is 'Pending'
                        $requestBooksQuery = "SELECT * FROM tbl_requestbooks WHERE tb_status = 'Pending'";
                        $requestBooksResult = mysqli_query($conn, $requestBooksQuery);

                        if (!$requestBooksResult) {
                            echo "Error fetching request books: " . mysqli_error($conn);
                        } else {
                            // Check if there are any rows returned
                            if (mysqli_num_rows($requestBooksResult) > 0) {
                                // Fetch and display each row
                                echo '<ol class="list-group list-group-numbered" style="height:100%; width:100%">';
                                 
                                while ($row = mysqli_fetch_assoc($requestBooksResult)) {
                                    echo '<li class="list-group-item d-flex justify-content-between align-items-start">';
                                    echo '<div class="ms-2 me-auto">';
                                    echo '  <div class="fw"><strong>' . $row['Book_Title'] . '</strong></div>
                                                    ' . $row['Authors_Name'] . ' <br/>
                                                    ' . $row['Quantity'] .'
                                                </div>'; 
                                    echo '</li>';
                                }
                                echo '</ol>';
                            } else {
                                echo "<p>No New Books</p>";
                            }
                        } 
                        // Close connection
                        mysqli_close($conn);
                    ?> 
                </div>
            </div>
 
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  
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