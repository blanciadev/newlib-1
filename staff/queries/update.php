<?php
session_start();

// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

// Check if the request is sent using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $borrowerID = $_SESSION['bID'];
  
// Generate JavaScript code to log the borrowerID to the browser's console
echo "<script>console.log('FETCH: " . $borrowerID . "');</script>";
     
        $firstName = $_POST['firstName'];
        $middleName = $_POST['middleName'];
        $lastName = $_POST['lastName'];
        $contactNumber = $_POST['contactNumber'];
        $email = $_POST['email'];
        $affiliation = $_POST['affiliation'];

        // Database connection
        $conn_update = mysqli_connect("localhost", "root", "root", "db_library_2", 3307);
        if ($conn_update->connect_error) {
            die("Connection failed: " . $conn_update->connect_error);
        }

      // Prepare the SQL statement to update the data
$sql_update = "UPDATE tbl_borrower SET First_Name=?, Middle_Name=?, Last_Name=?, Contact_Number=?, Email=?, affiliation=? WHERE Borrower_ID = ?";

// Prepare and bind the parameters
$stmt = $conn_update->prepare($sql_update);
$stmt->bind_param("ssssssi", $firstName, $middleName, $lastName, $contactNumber, $email, $affiliation, $borrowerID);

        // Execute the statement
        if ($stmt->execute()) {
            // Success message
            echo "Data updated successfully.";
            // Log to console
            echo "<script>console.log('Data updated successfully.');</script>";
            // Alert dialog
            echo "<script>alert('Record updated successfully.');</script>";
        } else {
            // Error message
            echo "Error updating data: " . $conn_update->error;
            // Log to console
            echo "<script>console.error('Error updating data: " . $conn_update->error . "');</script>";
        }

        // Close the connection
        $conn_update->close();
   
} else {
    // Invalid request method
    echo "Invalid request method.";
}
?>
