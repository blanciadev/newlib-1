<?php

session_start();

// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
// Retrieve the bookDetails array from the URL
$bookDetails = isset($_GET['bookDetails']) ? json_decode($_GET['bookDetails']) : [];
echo "<script>console.log('Book Details:', " . json_encode($bookDetails) . ");</script>";

// Check if the borrower_id session variable is set
if (isset($_SESSION['borrower_id'])) {

    // Retrieve the borrower_id from the session
    $borrower_id = $_SESSION['borrower_id'];
    $User_ID = $_SESSION["User_ID"];

    // Now you can use $borrower_id in your code as needed

} else {
    // Handle the case where the session variable is not set
    echo '<script>alert("Borrower ID unavailable"); window.location.href = "staff_borrow_dash.php";</script>';
}

// Check if $bookDetails is not empty before proceeding
if (!empty($bookDetails)) {
    // Initialize an array to store book Accession Codes
    $bookAccessionCodes = [];

    // Loop through each book detail
    foreach ($bookDetails as $accessionCode) {
        // Add the book Accession Code to the array
        $bookAccessionCodes[] = "'" . $accessionCode . "'";
    }

    // Convert the array of book Accession Codes to a comma-separated string for the SQL query
    $bookAccessionCodesStr = implode(",", $bookAccessionCodes);
    // Save the book Accession Codes string into a session variable
    $_SESSION['bookAccessionCodesStr'] = $bookAccessionCodesStr;

    echo "<script>console.log('Book CODES:', " . json_encode($_SESSION['bookAccessionCodesStr']) . ");</script>";

    // Check if $bookAccessionCodesStr is not empty before executing the SQL query
    if (!empty($bookAccessionCodesStr)) {
        // Assuming $conn is your database connection
        // Make sure $conn is defined and connected to the database

        // Retrieve book details from the database
        $sql = "SELECT tbl_books.*, tbl_authors.Authors_Name 
                FROM tbl_books
                INNER JOIN tbl_authors ON tbl_books.Authors_ID = tbl_authors.Authors_ID 
                WHERE tbl_books.Accession_Code IN ($bookAccessionCodesStr)";

        $result = $conn->query($sql);

        // Check if the query returned any rows
        if ($result && $result->num_rows > 0) {
        } else {
            echo "No books found";
        }
        // Process the query result as needed

        $Status = 'Pending';
        $currentDate = date('Y-m-d');
    } else {
        // $bookAccessionCodesStr is empty, handle accordingly
        echo "No book Accession Codes available";
    }
} else {
    // $bookDetails is empty, handle accordingly
    echo "No book details available";
}


if (isset($_POST['submit'])) {
    // Calculate due date as 3 days later by default
    $dueDate = date('Y-m-d', strtotime('+3 days', strtotime($currentDate)));

    $uid = $_SESSION["User_ID"];

    if ($_POST['due_date'] == 'custom') {
        $dueDate = null; // Set $dueDate to null if 'custom' option is selected
    } else {
        // Convert the date format from 'MM/DD/YYYY' to 'YYYY-MM-DD'
        $dueDate = date('Y-m-d', strtotime($_POST['due_date']));
    }



    $User_ID = $_SESSION["User_ID"];
    $_SESSION["due"] = $dueDate;
    // Check if $bookAccessionCodesStr is set
    if (isset($_SESSION['bookAccessionCodesStr']) && !empty($_SESSION['bookAccessionCodesStr'])) {
        echo "<script>console.log('Page Submit');</script>";

        // Split the string of Accession Codes into an array
        $bookAccessionCodes = explode(",", $_SESSION['bookAccessionCodesStr']);

        // Prepare and execute the INSERT statement for tbl_borrow for each book
        foreach ($bookAccessionCodes as $accessionCode) {
            echo "<script>console.log('Book Accession Code:', '" . $accessionCode . "');</script>"; // Debugging

            // Update the quantity in the database
            $sql_update_quantity = "UPDATE tbl_books SET Quantity = Quantity - 1 WHERE Accession_Code = $accessionCode";

            if ($conn->query($sql_update_quantity) === TRUE) {
                echo "<script>console.log('Quantity updated for Accession Code: $accessionCode');</script>";

                // Prepare and execute the INSERT statement for tbl_borrow
                $sql_borrow = "INSERT INTO tbl_borrow (User_ID, Borrower_ID, Accession_Code, Date_Borrowed, Due_Date, tb_status) 
                               VALUES ('$User_ID', '$borrower_id', $accessionCode, '$currentDate', ";

                if ($dueDate === null) {
                    $sql_borrow .= "NULL"; // Inserting null for custom due date
                } else {
                    $sql_borrow .= "'$dueDate'";
                }

                $sql_borrow .= ", '$Status')";

                if ($conn->query($sql_borrow) === TRUE) {
                    echo "<script>console.log('Inserted into  for Accession Code: $accessionCode');</script>";

                    // Prepare and execute the INSERT statement for tbl_borrowdetails
                    $sql_borrowdetails = "INSERT INTO tbl_borrowdetails (Borrower_ID, Accession_Code, Quantity, tb_status) 
                                          VALUES ('$borrower_id', $accessionCode, '1', '$Status')";

                    if ($conn->query($sql_borrowdetails) === TRUE) {
                        // Insertion successful
                        echo "<script>console.log('Inserted into  for Accession Code: $accessionCode');</script>";
                    } else {
                        echo "Error inserting into : " . $conn->error;
                    }

                    // Prepare and execute the INSERT statement for tbl_borrowdetails
                    $sql_returndetails = "INSERT INTO tbl_returningdetails (BorrowDetails_ID, tb_status) 
                            VALUES ('$borrower_id', 'Borrowed')";

                    if ($conn->query($sql_returndetails) === TRUE) {
                        // Insertion successful
                        echo "<script>console.log('Returning Details done');</script>";
                    } else {
                        echo "Error inserting into : " . $conn->error;
                    }

                    // Prepare and execute the INSERT statement for tbl_borrowdetails
                    $sql_return = "INSERT INTO tbl_returned (User_ID, Borrower_ID, Date_Returned, tb_status) 
                    VALUES ('$uid', '$borrower_id', NULL, 'Pending')";

                    if ($conn->query($sql_return) === TRUE) {
                        // Insertion successful
                        echo "<script>console.log('Returning Details done');</script>";
                    } else {
                        echo "Error inserting into : " . $conn->error;
                    }

                } else {
                    echo "Error inserting into : " . $conn->error;
                }
            } else {
                echo "Error updating quantity: " . $conn->error;
            }
        }

        // Redirect user or display success message after borrowing all books
        echo '<script>alert("All selected books have been borrowed successfully."); window.location.href = "queries/print_borrow.php";</script>';
    } else {
        echo "No book details available";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.css">

    <!-- jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- jQuery UI library -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <!-- jQuery UI Datepicker CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="./staff.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png ">
</head>

<body>
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary"><!--sidenav container-->
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <h2>Villa<span>Read</span>Hub</h2>
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon" />
        </a><!--header container-->
        <div class="user-header  d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
            <!-- Display user image -->
            <?php
            $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
            $userID = $_SESSION["User_ID"];
            $sql = "SELECT User_ID, First_Name, Middle_Name, Last_Name, tb_role, Contact_Number, E_mail, tb_address, image_data 
                    FROM tbl_employee 
                    WHERE User_ID = $userID";
            $result = mysqli_query($conn, $sql);
            if (!$result) {
                echo "Error: " . mysqli_error($conn);
            } else {
                $userData = mysqli_fetch_assoc($result);
            }
            ?>
            <?php if (!empty($userData['image_data'])) : ?>
                <!-- Assuming the image_data is in JPEG format, change the MIME type if needed -->
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else : ?>
                <!-- Change the path to your actual default image -->
                <img src="default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php echo $userData['First_Name'] . "<br/>" . $_SESSION["role"]; ?></span></strong>
        </div>

        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./staff_dashboard.php" class="nav-link link-body-emphasis "> <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./staff_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./staff_transaction_dash.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transaction</a> </li>
            <li class="nav-item"> <a href="./staff_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./staff_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <hr>
            <li class="nav-item"> <a href="./staff_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
    </div>

    <div class="container"><!--main container-->
        <div class="books-container">
            <div class="header1">
                <!-- Header content -->
                <h2 class="mb-4">Book Borrow Process</h2>
            </div>

            <form method="POST" action="">
                <?php
                // Retrieve book details from the database
                $sql = "SELECT tbl_books.*, tbl_authors.Authors_Name 
            FROM tbl_books
            INNER JOIN tbl_authors ON tbl_books.Authors_ID = tbl_authors.Authors_ID 
            WHERE tbl_books.Accession_Code IN ($bookAccessionCodesStr)";
                $result = $conn->query($sql);

                // Check if the result set is not empty
                if ($result && $result->num_rows > 0) {
                    // Fetch each row from the result set
                    while ($row = $result->fetch_assoc()) {
                ?>
                        <div class="card mb-4" style="width: 500px">
                            <div class="card-body">
                                <h5 class="card-title"><strong>Title:</strong> <?php echo $row['Book_Title']; ?></h5>
                                <p class="card-text"><strong>Author:</strong> <?php echo $row['Authors_Name']; ?></p>
                                <p class="card-text"><strong>Availability:</strong> <?php echo $row['Quantity']; ?></p>
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity:</label>
                                    <!-- Input field for quantity with max attribute set to available quantity -->
                                    <input type="number" id="quantity" name="quantity[]" min="1" max="<?php echo $row['Quantity']; ?>" value="1" readonly class="form-control form-control-sm">
                                    <!-- Hidden input field to store the book ID or accession code for processing -->
                                    <input type="hidden" name="accession_code[]" value="<?php echo $row['Accession_Code']; ?>">
                                </div>
                                <p class="card-text"><strong>Date Today:</strong> <?php echo $currentDate; ?></p>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                    <div class="mb-3" style="width: 200px;">
                        <label for="due_date" class="form-label">Date Return:</label>
                        <!-- Datepicker input field -->
                        <input type="text" name="due_date" id="due_date" class="form-control" required>
                    </div>

                <?php
                } else {
                    // Book not found or error occurred
                    echo "<p class='alert alert-warning'>No books found with the provided Accession Codes</p>";
                }
                ?>

                <!-- Success and error messages -->
                <?php if (!empty($successMessage)) : ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $successMessage; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errorMessage)) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $errorMessage; ?>
                    </div>
                <?php endif; ?>

                <!-- Submit and cancel buttons -->
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary me-2" id="submit" name="submit">Submit</button>
                    <a href="staff_return.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(function() {
            // Initialize datepicker
            $("#due_date").datepicker();
        });
    </script>


    <script>
        document.getElementById("cancelButton").addEventListener("click", function() {
            window.location.href = "staff_return.php";
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>
    <script>
        let date = new Date().toLocaleDateString('en-US', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            weekday: 'long',
        });
        // document.getElementById("currentDate").innerText = date; 

        setInterval(() => {
            let time = new Date().toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: 'numeric',
                second: 'numeric',
                hour12: 'true',
            })
            // document.getElementById("currentTime").innerText = time; 

        }, 1000)


        let navItems = document.querySelectorAll(".nav-item"); //adding .active class to navitems 
        navItems.forEach(item => {
            item.addEventListener('click', () => {
                document.querySelector('.active')?.classList.remove('active');
                item.classList.add('active');


            })

        })
    </script>
</body>

</html>