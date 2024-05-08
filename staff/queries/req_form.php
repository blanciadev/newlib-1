<?php

session_start(); // Start session if not already started

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Retrieve user ID from session
    $userID = $_SESSION['User_ID'];
    $bookTitle = $_POST['bookTitle'];
    $author = $_POST['author'];
    $publisher = $_POST['publisher'];
    $edition = $_POST['edition'];
    $year = $_POST['year'];
    $quantity = $_POST['quantity'];
    $status = "Pending";
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    $country = $_POST['country'];

    // Retrieve selected section and shelf number from form data
    $selectedSection = $_POST['selectedSection'];
    $selectedShelf = $_POST['selectedShelf'];

    // Handle author input
    if ($_POST['author'] === 'Other') {
        // Use the value from the "New Author" input field
        $author = $_POST['newAuthor'];
    } else {
        // Use the selected author from the dropdown
        $author = $_POST['author'];
    }

    // Validate form data (you may need more robust validation)
    if (empty($bookTitle) || empty($author) || empty($publisher) || empty($quantity)) {
        $errorMessage = "Please fill in all fields.";
        echo json_encode(array("success" => false, "error" => $errorMessage));
        echo '<script>console.log("' . $errorMessage . '");</script>';
        exit();
    } else {
        // Handle "Other Edition" input
        if ($edition === "Other") {
            // Check if the 'otherEdition' input is set and not empty
            if (isset($_POST['otherEdition']) && !empty($_POST['otherEdition'])) {
                $edition = $_POST['otherEdition']; // Use the input value for edition
            } else {
                $errorMessage = "Please provide other edition information.";
                echo json_encode(array("success" => false, "error" => $errorMessage));
                echo '<script>console.log("' . $errorMessage . '");</script>';
                exit();
            }
        }

        // Insert data into the database
        $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308); //database connection

        // Assuming you have a database connection named $conn
        $query = "INSERT INTO tbl_requestbooks (User_ID, Book_Title, Authors_Name, Publisher_Name, price, tb_edition, Year_Published, Quantity, country, tb_status, Section_Code, shelf) 
        VALUES ('$userID', '$bookTitle', '$author', '$publisher', '$price', '$edition', '$year', '$quantity', '$country', '$status', '$selectedSection', '$selectedShelf')";

        $result = mysqli_query($conn, $query);

        if ($result) {
            echo json_encode(array("success" => true));
            echo '<script>console.log("Success");</script>';
        } else {
            $errorMessage = "Failed to insert data into the database.";
            echo json_encode(array("success" => false, "error" => $errorMessage));
            echo '<script>console.log("' . $errorMessage . '");</script>';
        }
        var_dump($_POST);
    }
} else {
    echo '<script>console.log("No Data retrieved");</script>';
}

?>
