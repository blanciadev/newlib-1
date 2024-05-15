<?php
// Start the session
session_start();
// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

$fine = isset($_GET['fine']) ? $_GET['fine'] : 0;

// Access the stored session data
$accessionCode = $_SESSION['Accession_Code'];
$bookTitle = $_SESSION['Book_Title'];
$quantity = $_SESSION['Quantity'];
$borrowDetailsId = $_SESSION['BorrowDetails_ID'];
$date = $_SESSION['Date_Borrowed'];
$due = $_SESSION['Due_Date'];
$stat = $_SESSION['stat'];

echo '<script>';
echo 'console.log("Fine is ' . $fine . '");';
echo '</script>';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
        .container-fluid {
            position: relative;
            display: flex;
            justify-content: center; /* Horizontally center the container */
            align-items: center; /* Vertically center the container */
            height: 10vh; /* Adjust the height as needed */
        
        }
        .container-fluid button {
            margin: 0 10px; /* Adjust the horizontal margin to create space between buttons */
        } 

 /* Hide the print button when printed */
         @media print {
            .print-button,
            .go-to-staff-log-button {
                display: none !important;
            }
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

    <div class="container-fluid">
    <!-- Bootstrap print button -->
    <button id="printButton" class="print-button btn btn-primary" onclick="printPage()">Print</button>

    <!-- Go to Staff Log button -->
    <a href="../admin_transactions.php" id="goToStaffLogButton" class="go-to-staff-log-button btn btn-primary">Go to Staff Log</a>
    </div>
    <script>
    // Function to hide the button before printing starts
    window.onbeforeprint = function() {
        document.getElementById('goToStaffLogButton').style.display = 'none';
    };

    // Function to show the button after printing ends
    window.onafterprint = function() {
        document.getElementById('goToStaffLogButton').style.display = 'block';
    };

    function printPage() {
        window.print();
        // Hide the print button after clicking
        document.getElementById('printButton').style.display = 'none';
    }
</script>



</body>
</html>
