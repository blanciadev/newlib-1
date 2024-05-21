<?php
session_start();
// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

// Get the year and month from the URL
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');

// Function to connect to the database
function db_connect() {
    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $conn;
}

function fetch_query_results($conn, $query, $params, $types = "") {
    $stmt = $conn->prepare($query);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="./admin.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .board-container {
            margin-top: 20px;
        }
        .print-btn-container {
            margin-bottom: 20px;
        }
        .table th, .table td {
            text-align: center;
        }
        @media print {
            .print-btn-container {
                display: none;
            }
        }
        .report{
            display: flex; 
            flex-wrap: wrap;
            flex-direction: column;
        }
    </style>
</head>
<body> 
    <div class="report container"><!--board container-->  
      
        <div class="print-btn-container">
            <button id="printBtn" class="btn btn-primary">Print Report</button>
            <a href="admin_generate_report.php" id="dashboardBtn" class="btn btn-primary">Go Back</a>
        </div> 
        <?php
            $conn = db_connect();

            // Queries to get the required data
            $queries = [
                "Books Borrowed" => [
                    "query" => "SELECT COUNT(*) AS BooksBorrowed, COUNT(DISTINCT Borrower_ID) AS UniqueVisitors 
                                FROM tbl_borrow 
                                WHERE YEAR(Date_Borrowed) = ? AND MONTH(Date_Borrowed) = ?",
                    "params" => [$year, $month],
                    "types" => "ii"
                ],
                "Logs" => [
                    "query" => "SELECT COUNT(DISTINCT Borrower_ID) AS TotalVisitors, COUNT(*) AS TotalLogs 
                                FROM tbl_log 
                                WHERE YEAR(`Date_Time`) = ? AND MONTH(`Date_Time`) = ?",
                    "params" => [$year, $month],
                    "types" => "ii"
                ],
                "Fines" => [
                    "query" => "SELECT Reason, SUM(Amount) AS TotalAmount, COUNT(DISTINCT Borrower_ID) AS UniqueBorrowers 
                                FROM tbl_fines 
                                WHERE YEAR(Date_Created) = ? AND MONTH(Date_Created) = ? AND Payment_Date IS NOT NULL 
                                GROUP BY Reason",
                    "params" => [$year, $month],
                    "types" => "ii"
                ]
            ];

            // Fetching and displaying data for Books Borrowed
            echo '<div class="row mb-4">';
            echo '<div class="col-12">';
            echo '<h3 class="text-center mb-3">Books Borrowed</h3>';
            echo '<table class="table table-bordered">';
            echo '<thead class="table-dark">
            <tr><th>Unique Borrowers</th><th>Total Books Borrowed</th></tr></thead><tbody>';

            $result = fetch_query_results($conn, $queries["Books Borrowed"]["query"], $queries["Books Borrowed"]["params"], $queries["Books Borrowed"]["types"]);
            $row = $result->fetch_assoc();
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['UniqueVisitors']) . '</td>';
            echo '<td>' . htmlspecialchars($row['BooksBorrowed']) . '</td>';
            echo '</tr>';
            echo '</tbody></table>';
            echo '</div></div>';

            // Fetching and displaying data for Logs
            echo '<div class="row mb-4">';
            echo '<div class="col-12">';
            echo '<h3 class="text-center mb-3">Logs</h3>';
            echo '<table class="table table-bordered">';
            echo '<thead class="table-dark"><tr><th>Total Visitors</th><th>Total Logs</th></tr></thead><tbody>';

            $result = fetch_query_results($conn, $queries["Logs"]["query"], $queries["Logs"]["params"], $queries["Logs"]["types"]);
            $row = $result->fetch_assoc();
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['TotalVisitors']) . '</td>';
            echo '<td>' . htmlspecialchars($row['TotalLogs']) . '</td>';
            echo '</tr>';
            echo '</tbody></table>';
            echo '</div></div>';

            // Fetching and displaying data for Fines
            echo '<div class="row mb-4">';
            echo '<div class="col-12">';
            echo '<h3 class="text-center mb-3">Fines</h3>';
            echo '<table class="table table-bordered">';
            echo '<thead class="table-dark"><tr><th>Reason</th><th>Borrowers</th><th>Total Amount</th></tr></thead><tbody>';

            $result = fetch_query_results($conn, $queries["Fines"]["query"], $queries["Fines"]["params"], $queries["Fines"]["types"]);
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['Reason']) . '</td>';
                echo '<td>' . htmlspecialchars($row['UniqueBorrowers']) . '</td>';
                echo '<td>' . htmlspecialchars($row['TotalAmount']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
            echo '</div></div>';

            $conn->close();
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('printBtn').addEventListener('click', function () {
                window.print();
            });
        });
    </script>
</body>
</html>
