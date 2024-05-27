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
function db_connect()
{
    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $conn;
}

function fetch_query_results($conn, $query, $params, $types = "")
{
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

    .scrollable-table {
            max-height: 250px; /* Adjust the height as needed */
            overflow-y: auto;
        }
        body {
            font-family: 'Poppins', sans-serif;
        }

        .board-container {
            margin-top: 20px;
        }

        .print-btn-container {
            margin-bottom: 20px;
        }

        .table th,
        .table td {
            text-align: center;
        }

        @media print {
            .print-btn-container {
                display: none;
            }
        }

        .report {
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

        $queries = [
            "All Records" => [
                "query" => "SELECT 
                    b.User_ID, 
                    b.Accession_Code, 
                    bk.Book_Title, 
                    bd.Quantity, 
                    b.Date_Borrowed, 
                    b.Due_Date, 
                    bd.tb_status, 
                    bd.Borrower_ID, 
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
                        b.Borrower_ID = br.Borrower_ID AND
                        bd.Borrower_ID = br.Borrower_ID",
                 "params" => [], // No parameters needed
                 "types" => "" // No types needed
            ],
            "Returned Books" => [
                "query" => "SELECT
                b.User_ID, 
                b.Accession_Code, 
                bk.Book_Title, 
                bd.Quantity, 
                b.Date_Borrowed, 
                b.Due_Date, 
                bd.tb_status, 
                bd.Borrower_ID, 
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
                    b.Borrower_ID = br.Borrower_ID AND
                    bd.Borrower_ID = br.Borrower_ID
            WHERE
	bd.tb_status = 'Returned'",
                "params" => [], // No parameters needed
                "types" => "" // No types needed
            ],
            "Missing Books" => [
                "query" => "SELECT
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
            WHERE
                b.tb_status = 'Pending'
                AND DATEDIFF(CURDATE(), b.Due_Date) > 30
            GROUP BY
                bd.Borrower_ID; "
            ,
                "params" => [], // No parameters needed
                "types" => "" // No types needed
            ]
        ];
      
    
  
    // Fetching and displaying data for All Records
    echo '<div class="row mb-4">';
    echo '<div class="col-12">';
    echo '<h3 class="text-center mb-3">All Records</h3>';
    echo '<div class="table-responsive scrollable-table">';
    echo '<table class="table table-bordered">';
    echo '<thead class="table-dark">
        <tr><th>User ID</th><th>Accession Code</th><th>Book Title</th><th>Quantity</th><th>Date Borrowed</th><th>Due Date</th><th>Status</th><th>Borrower ID</th><th>Borrow ID</th></tr></thead><tbody>';
    
    $result = fetch_query_results($conn, $queries["All Records"]["query"], $queries["All Records"]["params"], $queries["All Records"]["types"]);
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['User_ID']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Accession_Code']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Book_Title']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Quantity']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Date_Borrowed']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Due_Date']) . '</td>';
        echo '<td>' . htmlspecialchars($row['tb_status']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Borrower_ID']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Borrow_ID']) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '</div></div></div>';
    
    // Fetching and displaying data for Returned Books
    echo '<div class="row mb-4">';
    echo '<div class="col-12">';
    echo '<h3 class="text-center mb-3">Returned Books</h3>';
    echo '<div class="table-responsive scrollable-table">';
    echo '<table class="table table-bordered">';
    echo '<thead class="table-dark">
        <tr><th>User ID</th><th>Accession Code</th><th>Book Title</th><th>Quantity</th><th>Date Borrowed</th><th>Due Date</th><th>Status</th><th>Borrower ID</th><th>Borrow ID</th></tr></thead><tbody>';
    
    $result = fetch_query_results($conn, $queries["Returned Books"]["query"], $queries["Returned Books"]["params"], $queries["Returned Books"]["types"]);
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['User_ID']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Accession_Code']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Book_Title']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Quantity']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Date_Borrowed']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Due_Date']) . '</td>';
        echo '<td>' . htmlspecialchars($row['tb_status']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Borrower_ID']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Borrow_ID']) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '</div></div></div>';
    
    // Fetching and displaying data for Missing Books
    echo '<div class="row mb-4">';
    echo '<div class="col-12">';
    echo '<h3 class="text-center mb-3">Missing Books</h3>';
    echo '<div class="table-responsive scrollable-table">';
    echo '<table class="table table-bordered">';
    echo '<thead class="table-dark">
        <tr><th>User ID</th><th>Accession Code</th><th>Book Title</th><th>Quantity</th><th>Date Borrowed</th><th>Due Date</th><th>Status</th><th>Borrower ID</th></tr></thead><tbody>';
    
    $result = fetch_query_results($conn, $queries["Missing Books"]["query"], $queries["Missing Books"]["params"], $queries["Missing Books"]["types"]);
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['User_ID']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Accession_Code']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Book_Title']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Quantity']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Date_Borrowed']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Due_Date']) . '</td>';
        echo '<td>' . htmlspecialchars($row['tb_status']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Borrower_ID']) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '</div></div></div>';
    
$conn->close();
?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('printBtn').addEventListener('click', function() {
                window.print();
            });
        });
    </script>
</body>

</html>