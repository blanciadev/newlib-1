
<?php

session_start();

$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308); 

// Check if the borrower_id session variable is set
if(isset($_SESSION['borrower_id'])) {
    // Retrieve the borrower_id from the session
    $borrower_id = $_SESSION['borrower_id'];
    $User_ID = $_SESSION["User_ID"];
    // Now you can use $borrower_id in your code as needed
    echo "Borrower ID: " . $borrower_id;
} else {
    // Handle the case where the session variable is not set
    echo "Borrower ID not found in session.";
}

// Check if the accession code session variable is set
if(isset($_SESSION['Accession_Code'])) {
    $user_id = $_SESSION['User_ID'] ;
    $accession_code = $_SESSION['Accession_Code'];
   
    // Retrieve book details from the database
    $sql = "SELECT tbl_books.*, tbl_authors.Authors_Name 
            FROM tbl_books
            INNER JOIN tbl_authors ON tbl_books.Authors_ID = tbl_authors.Authors_ID 
            WHERE tbl_books.Accession_Code = '$accession_code'";

    $result = $conn->query($sql);

    $Status = 'Pending';
    $currentDate = date('Y-m-d');
    // Calculate due date as 3 days later
    $dueDate = date('Y-m-d', strtotime('+3 days', strtotime($currentDate)));

} else {
    // Accession code is not available in the session, handle accordingly
    echo "Accession code is not available";
}

// Check if the submit button is clicked
if(isset($_POST['submit'])) {
    $quantity = $_POST['quantity'];

    // Check if the selected quantity is greater than zero
    if($quantity > 0) {
        // Retrieve the available quantity of the book
        $sql_get_quantity = "SELECT Quantity FROM tbl_books WHERE Accession_Code = '$accession_code'";
        $result_get_quantity = $conn->query($sql_get_quantity);

        if ($result_get_quantity->num_rows > 0) {
            $row = $result_get_quantity->fetch_assoc();
            $available_quantity = $row["Quantity"];

            // Check if the requested quantity is available
            if ($quantity <= $available_quantity) {
                // Calculate the remaining quantity after borrowing
                $remaining_quantity = $available_quantity - $quantity;

                // Update the quantity in the database
                $sql_update_quantity = "UPDATE tbl_books SET Quantity = '$remaining_quantity' WHERE Accession_Code = '$accession_code'";
                if ($conn->query($sql_update_quantity) === TRUE) {
                    // Quantity updated successfully
                 
                    // Prepare and execute the INSERT statements for tbl_borrow and tbl_borrowdetails
                    $sql_borrow = "INSERT INTO tbl_borrow (User_ID, Borrower_ID, Accession_Code, Date_Borrowed, Due_Date, tb_status) 
                                   VALUES ('$user_id', '$borrower_id', '$accession_code', '$currentDate', '$dueDate', '$Status')";
                    

                    $sql_borrowdetails = "INSERT INTO tbl_borrowdetails (Borrower_ID, Accession_Code, Quantity, tb_status) 
                                          VALUES ('$borrower_id', '$accession_code', '$quantity', '$Status')";

                    // Prepare and execute the INSERT statements for tbl_returned and tbl_returningdetails
                    $sql_returned = "INSERT INTO tbl_returned (User_ID, Borrower_ID, Date_Returned, tb_status) 
                    VALUES ('$user_id', '$borrower_id', NULL, 'Pending')";
                    if (!$conn->query($sql_returned)) {
                    echo "Error inserting into tbl_returned: " . $conn->error;
                    exit; // Stop execution if an error occurs while inserting into tbl_returned
                    }

                    $sql_returningdetails = "INSERT INTO tbl_returningdetails (BorrowDetails_ID, tb_status)
                            VALUES ('$borrower_id', 'Borrowed')";
                    if (!$conn->query($sql_returningdetails)) {
                    echo "Error inserting into tbl_returningdetails: " . $conn->error;
                    exit; // Stop execution if an error occurs while inserting into tbl_returningdetails
                    }


                
                    if ($conn->query($sql_borrow) === TRUE && $conn->query($sql_borrowdetails) === TRUE) {
                        // Redirect user or display success message as per your requirement
                        echo '<script>alert("Record Updated successfully."); window.location.href = "print_borrow.php";</script>';
 
                    } else {
                        echo "Error: " . $sql_borrow . "<br>" . $conn->error;
                    }
                } else {
                    echo "Error updating quantity: " . $conn->error;
                }
            } else {
                echo "Insufficient books. Requested quantity exceeds available quantity.";
            }
        } else {
            echo "Error retrieving available quantity.";
        }
    } else {
        echo "Invalid quantity. Please select at least one book to borrow.";
    }
}

$conn->close();
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VillaReadHub - Return</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="./staff.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png ">
</head>
<body>
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" ><!--sidenav container-->
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <h2>Villa<span>Read</span>Hub</h2> 
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon"/>
        </a><!--header container-->
        <div class="user-header mt-4 d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
            <img src="https://github.com/mdo.png" alt="" width="50" height="50" class="rounded-circle me-2">
            <strong><span><?php echo $_SESSION["staff_name"] ."<br/>"; echo $_SESSION["role"]; ?></span> </strong> 
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./staff_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./staff_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./staff_transaction_dash.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transaction</a> </li>
             <li class="nav-item"> <a href="./staff_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./staff_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <hr>
            <li class="nav-item"> <a href="./staff_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
    </div>

  
    <div class='books-container'>
   
    <h1>Search Book by Accession Code</h1>

    <form method="POST" action="">

    <?php
 
        if ($result && $result->num_rows > 0) {
            echo "<h2>Books Found</h2>";
            echo "<div class='books-container'>";
            // Fetch each row from the result set
            while ($row = $result->fetch_assoc()) {


                // Store each book's data in session variables
                $_SESSION['borrower_id'] = $borrower_id;
                $_SESSION['accession_code'] = $accession_code;
                $_SESSION['title'] = $row['Book_Title'];
                $_SESSION['author'] = $row['Authors_Name'];
                $_SESSION['availability'] = $row['Quantity'];
                $_SESSION['due_date'] = $dueDate;


                echo "<div class='book'>";
                echo "<p>Borrower ID : " .  $borrower_id . "</p>"; 
                echo "<p><strong>Accession Code:</strong> " . $accession_code . "</p>";
                echo "<p><strong>Title:</strong> " . $row['Book_Title'] . "</p>";
                echo "<p><strong>Author:</strong> " . $row['Authors_Name'] . "</p>";
                echo "<p><strong>Availability: </strong> " . $row['Quantity'] . "</p>";
                echo "<label for='quantity'>Quantity:</label>";
                // Input field for quantity with max attribute set to available quantity
                echo "<input type='number' id='quantity' name='quantity' min='1' max='" . $row['Quantity'] . "' value='1' required>";
                // Hidden input field to store the book ID or accession code for processing
                echo "<input type='hidden' name='accession_code' value='" . $accession_code . "'>";
                echo "<p><strong>Date Today:</strong> " . $currentDate . "</p>";
                echo "<p><strong>Due Date:</strong> " . $dueDate . "</p>";
                echo "</div>";
            }
            echo "</div>";
        } else {
            // Book not found or error occurred
            echo "<p>No book found with the provided Accession Code</p>";
        }
    ?>

<button type="submit" class="btn btn-primary" id="submit" name="submit">Submit</button>
<a href="staff_return.php" class="btn btn-primary">Cancel</a>

</form>

</div>

<div class="container">
        <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>
    </div>
    

    <script>
document.getElementById("cancelButton").addEventListener("click", function() {
    window.location.href = "staff_return.php";
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>
<script> 
    let date = new Date().toLocaleDateString('en-US', {  
        day:   'numeric',
        month: 'long',
        year:  'numeric' ,  
        weekday: 'long', 
    });   
    document.getElementById("currentDate").innerText = date; 

    setInterval( () => {
        let time = new Date().toLocaleTimeString('en-US',{ 
        hour: 'numeric',
        minute: 'numeric', 
        second: 'numeric',
        hour12: 'true',
    })  
    document.getElementById("currentTime").innerText = time; 

    }, 1000)
    

    let navItems = document.querySelectorAll(".nav-item");  //adding .active class to navitems 
    navItems.forEach(item => {
        item.addEventListener('click', ()=> { 
            document.querySelector('.active')?.classList.remove('active');
            item.classList.add('active');


        })

    })


</script>
</body>
</html>