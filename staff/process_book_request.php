<?php
include '../auth.php'; // Include authentication code

// Check if success parameter is set
if (isset($_GET['success']) && $_GET['success'] == "true") {
    // Success message
    $successMessage = "Book request submitted successfully.";
} elseif (isset($_GET['error'])) {
    // Error message
    $errorMessage = urldecode($_GET['error']);
} else {
    // Default behavior
    $errorMessage = "Unknown error occurred.";
}

// Get form data from URL parameters
$userID = $_SESSION['user_id'];
$bookTitle = $_GET['bookTitle'];
$author = $_GET['author'];
$publisher = $_GET['publisher'];
$quantity = $_GET['quantity'];
$status = $_GET['status'];

// SQL query to insert data into a table
$sql = "INSERT INTO request_books (user_id, Book_Title, Author, Publisher, Quantity, tb_status) 
        VALUES ('$userID', '$bookTitle', '$author', '$publisher', '$quantity', '$status')";

// Execute the query
if (mysqli_query($conn, $sql)) {
    // If insertion was successful, set success message
    $successMessage = "Book request submitted successfully.";
} else {
    // If insertion failed, set error message
    $errorMessage = "Error: " . mysqli_error($conn);
}
?>
