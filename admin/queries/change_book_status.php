<?php
session_start();
// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

// Check if the accession code is set in the POST request
if (!isset($_POST['accessionCode'])) {
    echo "Accession code is not provided.";
    exit(); // Stop script execution if accession code is not provided
}

$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize the accession code to prevent SQL injection
$accessionCode = mysqli_real_escape_string($conn, $_POST['accessionCode']);

// Update the status to 'Archived' for the specified accession code
$sql = "UPDATE tbl_books SET tb_status = 'Archived' WHERE Accession_Code = '$accessionCode'";
if ($conn->query($sql) === TRUE) {
    echo "Status updated successfully";
    // Output debug message to console
    echo "<script>console.log('Status updated successfully');</script>";
} else {
    echo "Error updating status: " . $conn->error;
    // Output debug message to console
    echo "<script>console.error('Error updating status: " . $conn->error . "');</script>";
}

$conn->close();
?>
