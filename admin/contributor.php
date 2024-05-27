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
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary"><!--sidenav container-->
        <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <h2>Villa<span>Read</span>Hub</h2>
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon" />
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
            <?php if (!empty($userData['image_data'])) : ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else : ?>
                <img src="default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php echo  $firstName . "<br/>" .  $role; ?></span></strong>
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./admin_dashboard.php" class="nav-link link-body-emphasis "> <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./admin_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./admin_transactions.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transactions</a> </li>
            <li class="nav-item"> <a href="./admin_staff.php" class="nav-link link-body-emphasis"><i class='bx bxs-user'></i>Manage Staff</a> </li>
            <li class="nav-item"> <a href="./admin_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item active"> <a href="./contributor.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Contributors</a> </li>
            <li class="nav-item"> <a href="./admin_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <li class="nav-item"> <a href="./admin_generate_report.php" class="nav-link link-body-emphasis"><i class='bx bxs-report'></i>Generate Report</a> </li>
            <hr>
            <li class="nav-item"> <a href="./admin_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="" data-bs-toggle="modal" data-bs-target="#logOut" class="nav-link link-body-emphasis"><i class='bx bx-log-out'></i>Log Out</a> </li>
        </ul>
    </div>
    <div class="board1 container-fluid"><!--board container-->
        <div class="header">
            <div class="text">
                <div class="title">
                    <h2>Contributors</h2>
                    <p>Welcome! <strong>Administrator</strong></p>
                </div>
            </div>
            <div class="datetime">
                <p id="currentTime" style="font-size:1rem;font-weight: 700; margin:0%;"></p>
                <p id="currentDate" style="font-size: 10pt;margin:0%;"></p>
            </div>
        </div>
        <div class="content">
    <?php
    // Query to fetch data from tbl_contributor and tbl_books
    $query = "SELECT * FROM tbl_contributor INNER JOIN tbl_books ON tbl_contributor.Accession_Code = tbl_books.Accession_Code";
    $result = mysqli_query($conn, $query);

    // Check if there are rows in the result
    if (mysqli_num_rows($result) > 0) {
        // Loop through each row of the result
        while ($row = mysqli_fetch_assoc($result)) {
            // Store the 'Name' as the key in the groupedData array
            $name = $row['Name'];
            // Check if the 'Name' already exists in the groupedData array
            if (!isset($groupedData[$name])) {
                // If not, create an array for the 'Name'
                $groupedData[$name] = array();
            }
            // Add the current row to the array for the 'Name'
            $groupedData[$name][] = $row;
        }

        // Output each grouped 'Name' as an accordion item
        foreach ($groupedData as $name => $rows) {
    ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading<?php echo $rows[0]['Contributor_ID']; ?>">
                <button class="accordion-button collapsed bg-light text-dark rounded-3 border border-secondary py-2 px-3 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $rows[0]['Contributor_ID']; ?>" aria-expanded="false" aria-controls="collapse<?php echo $rows[0]['Contributor_ID']; ?>">
    <span class="me-2"><?php echo $name; ?></span>
    <i class="bi bi-arrow-down-circle-fill"></i>
</button>

                </h2>
                <div id="collapse<?php echo $rows[0]['Contributor_ID']; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $rows[0]['Contributor_ID']; ?>" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <div class="row">
                            <?php foreach ($rows as $row) { ?>
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo $row['Book_Title']; ?></h5>
                                            <p class="card-text">Publisher Name: <?php echo $row['Publisher_Name']; ?></p>
                                            <!-- Add more details as needed -->
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
    <?php
        }
    } else {
        // If no data found
        echo "No data found.";
    }
    ?>

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
    </script>
</body>

</html>