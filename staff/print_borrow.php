<?php
// Start the session
session_start();

// Access the stored book data from session variables
$borrower_id = $_SESSION['borrower_id'];
$accession_code = $_SESSION['accession_code'];
$title = $_SESSION['title'];
$author = $_SESSION['author'];
$availability = $_SESSION['availability'];
$due_date = $_SESSION['due_date'];
// Access more data as needed

// Display the book data
// echo "<h2>Book Details</h2>";
// echo "<p>Borrower ID: $borrower_id</p>";
// echo "<p>Accession Code: $accession_code</p>";
// echo "<p>Title: $title</p>";
// echo "<p>Author: $author</p>";
// echo "<p>Availability: $availability</p>";
// echo "<p>Due Date: $due_date</p>";
// Display more details as needed
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .info-item {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Book Details</h2>
        <div class="info-item">
            <span class="info-label">Accession Code:</span> <?php echo isset($_SESSION['accession_code']) ? $_SESSION['accession_code'] : ''; ?>
        </div>
        <div class="info-item">
            <span class="info-label">Book Title:</span> <?php echo isset($_SESSION['title']) ? $_SESSION['title'] : ''; ?>
        </div>
        <div class="info-item">
            <span class="info-label">Quantity:</span> <?php echo isset($_SESSION['quantity']) ? $_SESSION['quantity'] : ''; ?>
        </div>
        <div class="info-item">
            <span class="info-label">Borrow Details ID:</span> <?php echo isset($_SESSION['borrow_details_id']) ? $_SESSION['borrow_details_id'] : ''; ?>
        </div>
        <div class="info-item">
            <span class="info-label">Date Borrowed:</span> <?php echo isset($_SESSION['date_borrowed']) ? $_SESSION['date_borrowed'] : ''; ?>
        </div>
        <div class="info-item">
            <span class="info-label">Due Date:</span> <?php echo isset($_SESSION['due_date']) ? $_SESSION['due_date'] : ''; ?>
        </div>
        <div class="info-item">
            <span class="info-label">Fine:</span> <?php echo isset($_SESSION['fine']) ? $_SESSION['fine'] : ''; ?>
        </div>
        <div class="info-item">
            <span class="info-label">Status:</span> <?php echo isset($_SESSION['status']) ? $_SESSION['status'] : ''; ?>
        </div>
    </div>
</body>
</html>
