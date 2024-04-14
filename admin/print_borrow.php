<?php
// Start the session
session_start();


// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}else{

    $User_ID = $_SESSION["borrower_id"];
$bookAccessionCodes = $_SESSION['bookAccessionCodesStr'];
echo "<script>console.log('Book CODES:', " . json_encode( $bookAccessionCodes) . ");</script>";

  // Include database connection
  $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308); 

  // Retrieve book details from the database
  $sql = "SELECT tbl_books.*, tbl_authors.Authors_Name 
          FROM tbl_books
          INNER JOIN tbl_authors ON tbl_books.Authors_ID = tbl_authors.Authors_ID 
          WHERE tbl_books.Accession_Code IN ($bookAccessionCodes)";
  $result = $conn->query($sql);

unset($_SESSION['bookAccessionCodesStr']);

}

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

        .info-item {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
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
    <h2>Book Borrow Receipt</h2>
    <span class="info-label">Borrower ID:</span> <?php echo $User_ID; ?>
    <div class="row">
   
    
        <?php
     
          
        // Check if there are results
        if ($result->num_rows > 0) {
            // Loop through each book retrieved from the database
            while ($row = $result->fetch_assoc()) {
        ?>
         
        <div class="col-md-4">
            <div class="info-item"><hr>
                <span class="info-label">Accession Code:</span> <?php echo $row['Accession_Code']; ?>
            </div>
            <div class="info-item">
                <span class="info-label">Book Title:</span> <?php echo $row['Book_Title']; ?>
            </div>
            <div class="info-item">
                <span class="info-label">Author:</span> <?php echo $row['Authors_Name']; ?>
            </div>
            <div class="info-item">
                <span class="info-label">Quantity: 1 </span> 
            </div>
            <div class="info-item">
                <span class="info-label">Date Borrowed:</span> <?php echo date('Y-m-d'); ?>
            </div>
            <div class="info-item">
                <span class="info-label">Due Date:</span> <?php echo date('Y-m-d', strtotime('+3 days')); ?>
            </div><hr>
        </div>
        <?php
            }
        }
        ?>
    </div>
</div>
v
    <div class="container-fluid">
    <!-- Bootstrap print button -->
    <button id="printButton" class="print-button btn btn-primary" onclick="printPage()">Print</button>

    <a href="admin_transactions.php" id="goToStaffLogButton" class="go-to-staff-log-button btn btn-primary">Go to Admin Transaction</a>
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
