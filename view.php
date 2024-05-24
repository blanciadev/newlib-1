<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- <link href="./staff.css" rel="stylesheet"> -->
    <link rel="icon" href="../images/lib-icon.png">
</head>
<body>
    <div class="board1 container-fluid">
        <div class="header">
            <div class="text">
                <div class="title">
                    <h2>Client Dashboard</h2>
                    <p>Welcome! <strong>Client!</strong></p>
                </div>
            </div>
            <div class="datetime">
                <p id="currentTime" style="font-size:1rem;font-weight: 700; margin:0%;"></p>
                <p id="currentDate" style="font-size: 10pt;margin:0%;"></p>
            </div>
        </div>

        <form action="" method="GET">  
            <div class="input-group">
                <input type="text" class="form-control" id="borrower_id" name="borrower_id" placeholder="Enter Borrower ID">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>

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

                <tbody id="resultTable">
                <?php
// Database connection
$conn = new mysqli("localhost", "root", "root", "db_library_2", 3308);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['borrower_id'])) {
    $borrower_id = intval($_GET['borrower_id']);
// Pagination configuration
$records_per_page = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $records_per_page;

// Fetch data
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
            bd.Borrower_ID = $borrower_id
        LIMIT $offset, $records_per_page";

$result = $conn->query($sql);

// Display data
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Borrower_ID'] . "</td>";
        echo "<td>" . $row['First_Name'] . " " . $row['Middle_Name'] . " " . $row['Last_Name'] . "</td>";
        echo "<td>" . $row['Book_Title'] . "</td>";
        echo "<td>" . $row['Quantity'] . "</td>";
        echo "<td>" . $row['Date_Borrowed'] . "</td>";
        echo "<td>" . $row['Due_Date'] . "</td>";
        echo "<td>" . $row['tb_status'] . "</td>";
        echo "<td><!-- Your action buttons here --></td>";
        echo "</tr>";
    }

    // Pagination links
    $sql_count = "SELECT COUNT(*) AS total FROM tbl_borrowdetails WHERE Borrower_ID = $borrower_id";
    $result_count = $conn->query($sql_count);
    $row_count = $result_count->fetch_assoc();
    $total_pages = ceil($row_count['total'] / $records_per_page);

    echo "<tr><td colspan='8'>";
    for ($i = 1; $i <= $total_pages; $i++) {
        echo "<a href='?borrower_id=$borrower_id&page=$i'>$i</a> ";
    }
    echo "</td></tr>";
} else {
    echo "<tr><td colspan='8'>No results found</td></tr>";
}
}

// Close connection
$conn->close();
?>

                </tbody>
            </table>
        </div>
        <a href="./index.php" class="nav-link link-body-emphasis"><i class='bx bxs-lock'></i>Return To Login</a>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
</body>
</html>
