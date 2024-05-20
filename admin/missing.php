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
<>
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
            <li class="nav-item "> <a href="./admin_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./admin_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./admin_transactions.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transactions</a> </li>
            <li class="nav-item"> <a href="./admin_staff.php" class="nav-link link-body-emphasis"><i class='bx bxs-user'></i>Manage Staff</a> </li>
            <li class="nav-item"> <a href="./admin_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item active"> <a href="./admin_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <li class="nav-item"> <a href="./admin_generate_report.php" class="nav-link link-body-emphasis"><i class='bx bxs-report'></i>Generate Report</a> </li>
            <hr>
            <li class="nav-item"> <a href="./admin_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i class='bx bx-log-out'></i>Log Out</a> </li>
        </ul>
    </div>
    
    <?php
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query
$sql = "SELECT
    MIN(b.User_ID) AS User_ID,
    MIN(b.Accession_Code) AS Accession_Code,
    MIN(bk.Book_Title) AS Book_Title,
    MIN(bd.Quantity) AS Quantity,
    MIN(b.Date_Borrowed) AS Date_Borrowed,
    MIN(b.Due_Date) AS Due_Date,
    MIN(bd.tb_status) AS tb_status,
    MIN(bd.Borrower_ID) AS Borrower_ID,
    MIN(br.First_Name) AS First_Name,
    MIN(br.Middle_Name) AS Middle_Name,
    MIN(br.Last_Name) AS Last_Name
FROM
    tbl_borrowdetails AS bd
    INNER JOIN tbl_borrow AS b ON bd.Borrower_ID = b.Borrower_ID
    INNER JOIN tbl_books AS bk ON b.Accession_Code = bk.Accession_Code
    INNER JOIN tbl_borrower AS br ON b.Borrower_ID = br.Borrower_ID
GROUP BY
    bd.Borrower_ID;";

$result = $conn->query($sql);

// Output data of each row
while ($row = $result->fetch_assoc()) {
    $dateBorrowed = new DateTime($row["Date_Borrowed"]);
    $currentDate = new DateTime();
    $interval = $currentDate->diff($dateBorrowed);
    $monthsDifference = $interval->m + ($interval->y * 12);

    // Update status to 'Missing' if more than a month
    if ($monthsDifference > 1 && ($row["tb_status"] !== 'Returned')) {
        $row["tb_status"] = 'Missing';

        // Update the status in the database
        $updateSql = "UPDATE tbl_borrowdetails SET tb_status = 'Missing' WHERE Borrower_ID = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("i", $row["Borrower_ID"]);
        $updateStmt->execute();
        $updateStmt->close();
    }

    echo "<tr>";
    echo "<td>" . $row["Borrower_ID"] . "</td>";
    echo "<td>" . $row["First_Name"] . " " . $row["Middle_Name"] . " " . $row["Last_Name"] . "</td>";
    echo "<td>" . $row["Date_Borrowed"] . "</td>";
    echo "<td>" . $row["Due_Date"] . "</td>";
    echo "<td>" . $row["tb_status"] . "</td>";
    echo "<td>";
    echo "<form class='update-form' method='GET' action='staff_return.php'>";
    echo "<input type='hidden' name='borrowId' id='borrowId' value='" . $row["Borrower_ID"] . "'>";
    echo "</form>";
    echo "<input type='hidden' name='borrowerId' value='" . $row["Borrower_ID"] . "'>";

  
}
?>

   
</body>
</html>