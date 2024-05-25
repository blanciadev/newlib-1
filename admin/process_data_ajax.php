<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the AJAX request is received
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // echo '<script>console.log("Process_data.ajax");</script>';

    // Retrieve data sent via AJAX
    $bookTitle = $_POST['Book_Title'];
    $pubname = $_POST['Publisher_Name'];
    $edition = $_POST['tb_edition'];
    $yr = $_POST['Year_Published'];
    $qty = $_POST['Quantity'];
    $price = $_POST['price'];
    $stat = "Pending";
    $country = $_POST['country'];
    $bibliography = "NA";
    $isbn = 1; // Assuming ISBN is always 1
    $sectionCode = $_POST["section"];
    $shelfNumber = $_POST["shelf"];
    $requestID =   $_SESSION['reqID'];
    $authorsName = $_POST['Authors_ID'];
    $customAccessionCode = $_POST['accessionCode'];
 
    $type = $_POST['type'];

 // Additional data from form fields
    $name = $_POST['add_name'];
    $address = $_POST['add_address'];
    $email = $_POST['add_email'];
    $contact = $_POST['add_contact'];
   
   
echo '<script>console.log("' . $name . '");</script>';
echo '<script>console.log("' . $address . '");</script>';
echo '<script>console.log("' . $email . '");</script>';
echo '<script>console.log("' . $contact . '");</script>';

    // Check if custom Accession Code is provided
    if (!empty($customAccessionCode)) {
        // Use the provided custom Accession Code
        $customAccessionCode = doubleval($customAccessionCode);
    } else {
        // Generate a new random 6-digit value
        $randomValue = rand(100000, 999999); // Generate random value between 100000 and 999999
        $customAccessionCode = doubleval($randomValue . '.2');
    }

     // Check if the author already exists in tbl_authors
     $checkAuthorSql = "SELECT Authors_ID FROM tbl_authors WHERE Authors_Name = '$authorsName'";
     $authorResult = $conn->query($checkAuthorSql);

     if ($authorResult->num_rows > 0) {
         // Author already exists, retrieve their ID
         $authorRow = $authorResult->fetch_assoc();
         $authorsID = $authorRow['Authors_ID'];
     } else {
         // Author doesn't exist, insert the new author into tbl_authors
         $authorsID = substr(uniqid('A_', true), -6); // Generate Authors_ID
         $insertAuthorSql = "INSERT INTO tbl_authors (Authors_ID, Authors_Name, Nationality) 
                             VALUES ('$authorsID', '$authorsName', 'N/A')";

         if ($conn->query($insertAuthorSql) !== TRUE) {
             throw new Exception("Error inserting author: " . $conn->error);
         }else{
            echo '<script>console.log("Author Insert Success");</script>';
         $authorsName = $authorsID;
     }
    } 

    // Check if the book already exists based on the title and edition
    $checkDuplicateBookSql = "SELECT * FROM tbl_books WHERE Book_Title = '$bookTitle' AND tb_edition = '$edition'";
    $result = $conn->query($checkDuplicateBookSql);

    if ($result->num_rows > 0) {
        // Book already exists, update the quantity
        $row = $result->fetch_assoc();
        $existingQty = $row['Quantity'];
        $newQty = $existingQty + $qty;

        $updateQuantitySql = "UPDATE tbl_books SET Quantity = '$newQty' WHERE Book_Title = '$bookTitle' AND tb_edition = '$edition'";
        if ($conn->query($updateQuantitySql) !== TRUE) {
            echo "Error updating book quantity: " . $conn->error;
        }

        echo '<script>console.log("Update Quantity Success");</script>';
        echo '<script>alert("Update Quantity Success!");</script>';

             // Update tb_status based on Request_ID
             $updateStatusSql = "UPDATE tbl_requestbooks SET tb_status = 'Approved' WHERE Request_ID = '$requestID'";
             if ($conn->query($updateStatusSql) === TRUE) {
                 echo '<script>console.log("Update Success");</script>';
                 echo '<script>console.log("Update Success");</script>';
                 
             } else {
                 echo "Error updating request status: " . $conn->error;
             }
    } else {
        // Check if the book already exists based on accession code
        $checkDuplicateAccessionCodeSql = "SELECT * FROM tbl_books WHERE Accession_Code = '$customAccessionCode'";
        $result = $conn->query($checkDuplicateAccessionCodeSql);

        if ($result->num_rows > 0) {
            // Accession Code duplicate detected, display message
            echo '<script>console.log("Accession Code Duplicate");</script>';
            echo '<script>console.log("Accession Code Duplicate");</script>';
        } else {
            // Insert the new book into tbl_books
            $booksql = "INSERT INTO tbl_books (Accession_Code, Book_Title, Authors_ID, Publisher_Name, Section_Code, shelf, tb_edition, Year_Published, ISBN, Bibliography, cl_type, Quantity, Price, tb_status) 
                        VALUES ('$customAccessionCode', '$bookTitle', '$authorsID', '$pubname', '$sectionCode', '$shelfNumber', '$edition', '$yr', '$isbn', '$bibliography','$type', '$qty', '$price', 'Available')";

            if ($conn->query($booksql) !== TRUE) {
                echo "Error inserting book: " . $conn->error;
            }else{
                echo '<script>console.log("Insert New Book Success");</script>';
                echo '<script>console.log("Insert New Book Success");</script>';
            }

            // Update tb_status based on Request_ID
            $updateStatusSql = "UPDATE tbl_requestbooks SET tb_status = 'Approved' WHERE Request_ID = '$requestID'";
            if ($conn->query($updateStatusSql) === TRUE) {
                echo '<script>console.log("Update Success");</script>';
                echo '<script>console.log("Update Success");</script>';
                
            } else {
                echo "Error updating request status: " . $conn->error;
            }

            $insertContributor = "INSERT INTO tbl_contributor (Accession_Code, Name, Address, Email, Contact_Number)
            VALUES ('$customAccessionCode','$name','$address','$email','$contact')";
             
             if ($conn->query($insertContributor) === TRUE) {
                echo '<script>console.log("Update Success");</script>';
                
            } else {
                echo "Error updating request status: " . $conn->error;
            }

        }
    }

} else {
    // If the request method is not POST or 'submit' is not set
    echo "Invalid request";
}

// Close the database connection
$conn->close();
?>
