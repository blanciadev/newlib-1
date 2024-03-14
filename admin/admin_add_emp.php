<?php
// Check if data is received through query parameters
if (isset($_GET['firstName']) && isset($_GET['lastName']) && isset($_GET['role']) &&
    isset($_GET['contactNumber']) && isset($_GET['email']) && isset($_GET['address'])) {
    
    // Extract data from query parameters
    $firstName = $_GET['firstName'];
    $lastName = $_GET['lastName'];
    $role = $_GET['role'];
    $contactNumber = $_GET['contactNumber'];
    $email = $_GET['email'];
    $address = $_GET['address'];

    // Database connection
    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // SQL query to insert new employee with the received data
    $sql = "INSERT INTO tbl_employee (First_Name, Last_Name, tb_role, Contact_Number, E_mail, tb_address ) 
            VALUES ('$firstName', '$lastName', '$role', '$contactNumber', '$email', '$address')";

    if (mysqli_query($conn, $sql)) {
        echo "New employee added successfully!";
    } else {
        echo "Error adding employee: " . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    // Handle case where data is not received
    echo "No data received for employee addition.";
}
?>
