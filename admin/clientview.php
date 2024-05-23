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
    <link rel="icon" href="../images/lib-icon.png">
</head>
<body>
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary"><!--sidenav container-->
        <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <h2>Villa<span>Read</span>Hub</h2>
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon"/>
        </a><!--header container-->
        <div class="user-header d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
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
                    $firstName = $userData['First_Name'];
                    $role = $userData['tb_role'];
                }
            ?>
            <?php if (!empty($userData['image_data'])): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else: ?>
                <img src="default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php echo htmlspecialchars($firstName) . "<br/>" . htmlspecialchars($role); ?></span></strong>
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./admin_dashboard.php" class="nav-link link-body-emphasis"><i class='bx bxs-home'></i>Dashboard</a></li>
            <li class="nav-item"> <a href="./admin_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a></li>
            <li class="nav-item"> <a href="./admin_transactions.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transactions</a></li>
            <li class="nav-item"> <a href="./admin_staff.php" class="nav-link link-body-emphasis"><i class='bx bxs-user'></i>Manage Staff</a></li>
            <li class="nav-item"> <a href="./admin_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a></li>
            <li class="nav-item active"> <a href="./clientview.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Client Books</a></li>
            <li class="nav-item"> <a href="./admin_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a></li>
            <li class="nav-item"> <a href="./admin_generate_report.php" class="nav-link link-body-emphasis"><i class='bx bxs-report'></i>Generate Report</a></li>
            <hr>
            <li class="nav-item"> <a href="./admin_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a></li>
            <li class="nav-item"> <a href="" data-bs-toggle="modal" data-bs-target="#logOut" class="nav-link link-body-emphasis"><i class='bx bx-log-out'></i>Log Out</a></li>
        </ul>
    </div>
     <!-- CONTENT OF THE QUERY -->
     <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Borrower ID</th>
                    <th>Borrower Name</th>
                    <th>Book Title</th>
                    <th>Quantity</th>
                    <th>Date Borrowed</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['borrower_id'])) {
                    $borrower_id = intval($_GET['borrower_id']);

                    $conn = new mysqli("localhost", "root", "root", "db_library_2", 3308);
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $sql = "SELECT DISTINCT
                                b.User_ID, 
                                b.Accession_Code, 
                                bk.Book_Title, 
                                bd.Quantity, 
                                b.Date_Borrowed, 
                                b.Due_Date, 
                                bd.tb_status, 
                                br.Borrower_ID, 
                                b.Borrow_ID, 
                                br.First_Name, 
                                br.Middle_Name, 
                                br.Last_Name, 
                                bd.BorrowDetails_ID
                            FROM
                                tbl_borrowdetails AS bd
                            INNER JOIN
                                tbl_borrow AS b
                                ON bd.Borrower_ID = b.Borrower_ID AND bd.BorrowDetails_ID = b.Borrow_ID
                            INNER JOIN
                                tbl_books AS bk
                                ON b.Accession_Code = bk.Accession_Code
                            INNER JOIN
                                tbl_borrower AS br
                                ON b.Borrower_ID = br.Borrower_ID AND bd.Borrower_ID = br.Borrower_ID
                            WHERE
                                bd.Borrower_ID = ?";

                    if ($stmt = $conn->prepare($sql)) {
                        $stmt->bind_param("i", $borrower_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                // Output data of each row
                                $dateBorrowed = new DateTime($row["Date_Borrowed"]);
                                $currentDate = new DateTime();
                                $interval = $currentDate->diff($dateBorrowed);
                                $monthsDifference = $interval->m + ($interval->y * 12);
                                $rowClass = ($monthsDifference > 1 && $row["tb_status"] !== 'Returned') ? 'table-danger' : '';

                                echo '<tr class="' . htmlspecialchars($rowClass) . '">';
                                echo '<td>' . htmlspecialchars($row["Borrower_ID"]) . '</td>';
                                echo '<td>' . htmlspecialchars($row["First_Name"] . " " . $row["Middle_Name"] . " " . $row["Last_Name"]) . '</td>';
                                echo '<td>' . htmlspecialchars($row["Book_Title"]) . '</td>';
                                echo '<td>' . htmlspecialchars($row["Quantity"]) . '</td>';
                                echo '<td>' . htmlspecialchars($row["Date_Borrowed"]) . '</td>';
                                echo '<td>' . htmlspecialchars($row["Due_Date"]) . '</td>';
                                echo '<td>' . htmlspecialchars($row["tb_status"]) . '</td>';
                                echo '<td>';
                                echo '<div class="update-message"></div>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="8">No records found.</td></tr>';
                        }

                        $stmt->close();
                    } else {
                        echo '<tr><td colspan="8">Error: ' . htmlspecialchars($conn->error) . '</td></tr>';
                    }

                    $conn->close();
                }
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
          div class="modal-footer d-flex flex-row justify-content-center">
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
  document.addEventListener("DOMContentLoaded", function() {
    console.log("DOMContentLoaded event fired.");

    var borrowerIdInput = document.getElementById("borrower_id");
    var borrowForm = document.querySelector('form'); // Get the form element

    // Function to submit the form if the borrower ID input field has a value
    function checkAndSubmitForm() {
        // Check if the input field has a value
        if (borrowerIdInput.value.trim() !== "") {
            console.log("Input field has a value. Submitting form.");
            borrowForm.submit(); // Submit the form
        } else {
            console.log("Input field is empty.");
        }
    }

    // Chart.js integration
    const labels = <?php echo $labelsJSON; ?>;
    const data = <?php echo $dataJSON; ?>;

    const ctx = document.getElementById('myChart').getContext('2d');
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

    // Update date and time
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
            hour12: true,
        });  
        document.getElementById("currentTime").innerText = time; 
    }, 1000);
});


    </script>
</body>
</html>