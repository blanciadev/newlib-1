<?php
session_start();
// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the selected status from the query string or set default to 'Available'
$status = isset($_GET['status']) ? $_GET['status'] : 'Available';

// Get the current page number from the query string or set default to 1
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// Number of records per page
$recordsPerPage = 3;

// Calculate the offset for pagination
$offset = ($page - 1) * $recordsPerPage;

// SQL query with pagination
$sql = "SELECT
            tbl_books.Accession_Code, 
            tbl_books.Book_Title, 
            tbl_books.Authors_ID, 
            tbl_books.Publisher_Name, 
            tbl_books.Section_Code, 
            tbl_books.shelf, 
            tbl_books.tb_edition, 
            tbl_books.Year_Published, 
            tbl_books.ISBN, 
            tbl_books.Bibliography, 
            tbl_books.Quantity, 
            tbl_books.tb_status, 
            tbl_books.Price, 
            tbl_section.Section_uid, 
            tbl_section.Section_Name, 
            tbl_section.Section_Code, 
            tbl_authors.Authors_Name
        FROM
            tbl_books
        INNER JOIN
            tbl_section ON tbl_books.Section_Code = tbl_section.Section_uid
        INNER JOIN
            tbl_authors ON tbl_books.Authors_ID = tbl_authors.Authors_ID
        WHERE
            tbl_books.tb_status = '$status'
            
       ";

$result = $conn->query($sql);

// Output data of each row
while ($row = $result->fetch_assoc()) {
    echo "<tr><td>" . $row["Accession_Code"] . "</td>
        <td>" . $row["Book_Title"] . "</td>
        <td>" . $row["Authors_Name"] . "</td>
        <td>" . $row["Publisher_Name"] . "</td>
        <td>" . $row["Section_Code"] . "</td>
        <td>" . $row["shelf"] . "</td>
        <td>" . $row["tb_edition"] . "</td>
        <td>" . $row["Year_Published"] . "</td> 
        <td>" . $row["Quantity"] . "</td>
        <td>" . $row["Price"] . "</td>
        <td>" . $row["tb_status"] . "</td>
        <td>";

    if ($row["tb_status"] != 'Archived') {
        echo "<button class='btn btn-primary archive-btn' onclick='archiveBook(\"" . $row["Accession_Code"] . "\")'>Archive</button>";
    } else {
        echo "<button class='btn btn-primary' disabled>Archived</button>";
    }
    echo "</td></tr>";
}

$conn->close();
?>
