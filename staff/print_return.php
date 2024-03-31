<?php
// Start the session
session_start();

// Access the stored session data
$accessionCode = $_SESSION['Accession_Code'];
$bookTitle = $_SESSION['Book_Title'];
$quantity = $_SESSION['Quantity'];
$borrowDetailsId = $_SESSION['BorrowDetails_ID'];
$date = $_SESSION['Date_Borrowed'];
$due = $_SESSION['Due_Date'];
$fine =  $_SESSION['fine'];
$stat = $_SESSION['stat'];


// Now you can use these variables as needed in this file
// echo "Accession Code: " . $accessionCode . "<br>";
// echo "Book Title: " . $bookTitle . "<br>";
// echo "Quantity: " . $quantity . "<br>";
// echo "Borrow Details ID: " . $borrowDetailsId . "<br>";
// echo "Date Borrowed: ". $date . "<br>";
// echo "Due Date: " . $due. "<br>";
// echo "Fine: " . $fine. "<br>";
// echo "Status: " . $stat. "<br>";
// Remember to clear or unset these session variables when they are no longer needed
// unset($_SESSION['Accession_Code']);
// unset($_SESSION['Book_Title']);
// unset($_SESSION['Quantity']);
// unset($_SESSION['BorrowDetails_ID']);
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
            <span class="info-label">Accession Code:</span> <?php echo $accessionCode; ?>
        </div>
        <div class="info-item">
            <span class="info-label">Book Title:</span> <?php echo $bookTitle; ?>
        </div>
        <div class="info-item">
            <span class="info-label">Quantity:</span> <?php echo $quantity; ?>
        </div>
        <div class="info-item">
            <span class="info-label">Borrow Details ID:</span> <?php echo $borrowDetailsId; ?>
        </div>
        <div class="info-item">
            <span class="info-label">Date Borrowed:</span> <?php echo $date; ?>
        </div>
        <div class="info-item">
            <span class="info-label">Due Date:</span> <?php echo $due; ?>
        </div>
        <div class="info-item">
            <span class="info-label">Fine:</span> <?php echo $fine; ?>
        </div>
        <div class="info-item">
            <span class="info-label">Status:</span> <?php echo $stat; ?>
        </div>
    </div>
</body>
</html>
