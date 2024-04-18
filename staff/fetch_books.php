<?php
// Establish database connection
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the selected shelf is received
if (isset($_POST['selectedShelf'])) {
   
    // Sanitize the received selected shelf to prevent SQL injection
    $selectedShelf = mysqli_real_escape_string($conn, $_POST['selectedShelf']);
    
    // Prepare SQL query to fetch books based on the selected shelf
    $sql = "
    SELECT
        tbl_books.Book_Title, 
        tbl_books.Accession_Code, 
        tbl_shelf.Category, 
        tbl_section.Section_Name, 
        tbl_books.Publisher_Name, 
        tbl_books.shelf, 
        tbl_books.Year_Published, 
        tbl_books.Quantity, 
        tbl_authors.Authors_Name
    FROM
        tbl_books
        INNER JOIN
        tbl_section
        ON 
            tbl_books.Section_Code = tbl_section.Section_Code
        INNER JOIN
        tbl_shelf
        ON 
            tbl_books.shelf = tbl_shelf.Category AND
            tbl_section.Section_Code = tbl_shelf.shelf_code
        INNER JOIN
        tbl_authors
        ON 
            tbl_books.Authors_ID = tbl_authors.Authors_ID
    WHERE
        tbl_books.shelf = '$selectedShelf'";
    
    // Execute the SQL query
    $result = mysqli_query($conn, $sql);

    // Check if query execution was successful
    if ($result) {
        echo "<h2> Category : $selectedShelf</h2>";
        echo "<br><hr>";
        // Output fetched books as HTML content
        while ($row = mysqli_fetch_assoc($result)) {
            $bookTitle = $row['Book_Title'];
            $accessionCode = $row['Accession_Code'];
            $publisherName = $row['Publisher_Name'];
            $yearPublished = $row['Year_Published'];
            $quantity = $row['Quantity'];
            $authorName = $row['Authors_Name'];
          
            echo "<div>";
            echo "<p><strong>Title:</strong> $bookTitle</p>";
            echo "<p><strong>Accession Code:</strong> $accessionCode</p>";
            echo "<p><strong>Publisher:</strong> $publisherName</p>";
            echo "<p><strong>Year Published:</strong> $yearPublished</p>";
            echo "<p><strong>Quantity:</strong> $quantity</p>";
            echo "<p><strong>Author:</strong> $authorName</p>";
            echo "</div><hr>";

        }
    } else {
        // If there's an error in executing the query, return an error message
        echo "Error: " . mysqli_error($conn);
    }
} else {
    // If the selected shelf is not received, return an error message
    echo "Error: Selected shelf is not received.";
}

// Close database connection
mysqli_close($conn);
?>
