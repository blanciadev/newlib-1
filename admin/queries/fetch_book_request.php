<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if accession code is provided
if(isset($_GET['accessionCode'])) {
    // Sanitize the input
    $accessionCode = mysqli_real_escape_string($conn, $_GET['accessionCode']);
    
    // SQL query to retrieve book details based on accession code
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
                tbl_books.Accession_Code = '$accessionCode'";
    
    // Execute the query and process the results
    $result = $conn->query($sql);
    
    // Prepare response data
    $response = [];

    if ($result->num_rows > 0) {
        // Fetch the book details
        $row = $result->fetch_assoc();
        $response = [
            'success' => true,
            'data' => $row
        ];
    } else {
        $response = [
            'success' => false,
            'message' => 'No book found with the provided accession code.'
        ];
    }

    // Close the database connection
    $conn->close();

    // Return the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit; // Terminate the script after sending the response
}
?>
