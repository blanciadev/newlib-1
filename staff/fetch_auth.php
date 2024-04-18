<?php
// Establish database connection
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the target is received
if (isset($_POST['target'])) {
    // Sanitize the received target to prevent SQL injection
    $target = mysqli_real_escape_string($conn, $_POST['target']);

    // Prepare SQL query based on the target
    $sql = "";
    if ($target === "Authors") {
        $sql = "SELECT Authors_Name, Nationality FROM tbl_authors";
    } elseif ($target === "Publishers") {
        $sql = "SELECT Publisher_Name FROM tbl_books";
    }

    // Execute the SQL query
    $result = mysqli_query($conn, $sql);

    // Check if query execution was successful
    if ($result) {
        echo "<h4><strong>Authors :</strong> </h4><hr>";
        // Output fetched data as HTML content
        while ($row = mysqli_fetch_assoc($result)) {
            // Echo the data based on the target
            if ($target === "Authors") {
                $author = $row['Authors_Name'];
                $nationality = $row['Nationality'];
                echo "<div>";
                echo "<p><strong>Author's Name:</strong> $author</p>";
                echo "<p><strong>Nationality:</strong> $nationality</p>";
                echo "</div><hr>";
            } elseif ($target === "Publishers") {
                $publisher = $row['Publisher_Name'];
                echo "<div>";
                echo "<p><strong>Publisher Name:</strong> $publisher</p>";
                echo "</div><hr>";
            }
        }
    } else {
        // If there's an error in executing the query, return an error message
        echo "Error: " . mysqli_error($conn);
    }
} else {
    // If the target is not received, return an error message
    echo "Error: Target not received.";
}

// Close database connection
mysqli_close($conn);
?>
