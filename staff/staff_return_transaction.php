<?php
session_start();
// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

// Database connection
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Check if the borrowId parameter is set in the URL
if (isset($_GET['borrowId'])) {
    // Sanitize the borrowId parameter
    $borrowId = filter_var($_GET['borrowId'], FILTER_SANITIZE_STRING);

    // Set the session variable
    $_SESSION['BorrowDetails_ID'] = $borrowId;
    
} else {
    // Handle the case when borrowId is not provided in the URL
    // For example, redirect back or show an error message
    echo "Borrow ID not provided.";
    exit; // Stop script execution if necessary
}


// Initialize $bd_Id to an empty string
$bd_Id = $_SESSION['BorrowDetails_ID'];

// Prepare the SQL statement with a placeholder for the search input
$sql = "SELECT DISTINCT
b.User_ID, 
b.Accession_Code, 
bk.Book_Title, 
bd.Quantity, 
b.Date_Borrowed, 
b.Due_Date, 
bd.tb_status, 
bd.Borrower_ID, 
b.Borrow_ID
FROM
tbl_borrowdetails AS bd
INNER JOIN
tbl_borrow AS b
ON 
    bd.Borrower_ID = b.Borrower_ID AND
    bd.BorrowDetails_ID = b.Borrow_ID
INNER JOIN
tbl_books AS bk
ON 
    b.Accession_Code = bk.Accession_Code
INNER JOIN
tbl_borrower AS br
ON 
    b.Borrower_ID = br.Borrower_ID AND
    bd.Borrower_ID = br.Borrower_ID
WHERE
bd.BorrowDetails_ID = $bd_Id";



// Prepare the statement
$stmt = $conn->prepare($sql);

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();




 
   

 // Define the HTML code for the toast element
echo '<div class="toastNotif hide">
<div class="toast-content">
    <i class="bx bx-check check"></i>
    <div class="message">
        <span class="text text-1"></span>
        <!-- this message can be changed to "Success" and "Error"-->
        <span class="text text-2"></span>
        <!-- specify based on the if-else statements -->
    </div>
</div>
<i class="bx bx-x close"></i>
<div class="progress"></div>
</div>';

// Define JavaScript functions to handle the toast
echo '<script>
function showToast(type, message) {
    var toast = document.querySelector(".toastNotif");
    var progress = document.querySelector(".progress");
    var text1 = toast.querySelector(".text-1");
    var text2 = toast.querySelector(".text-2");
    
    if (toast && progress && text1 && text2) {
        // Update the toast content based on the message type
        if (type === "success") {
            text1.textContent = "Success";
            toast.classList.remove("error");
        } else if (type === "error") {
            text1.textContent = "Error";
            toast.classList.add("error");
        } else {
            console.error("Invalid message type");
            return;
        }
        
        // Set the message content
        text2.textContent = message;
        
        // Show the toast and progress
        toast.classList.add("showing");
        progress.classList.add("showing");
        
        // Hide the toast and progress after 5 seconds
        setTimeout(() => {
            toast.classList.remove("showing");
            progress.classList.remove("showing");
           
        }, 5000);
    } else {
        console.error("Toast elements not found");
    }
}

function closeToast() {
    var toast = document.querySelector(".toastNotif");
    var progress = document.querySelector(".progress");
    toast.classList.remove("showing");
    progress.classList.remove("showing");
}

 function redirectToPage(url, delay) {
    setTimeout(() => {
        window.location.href = url;
    }, delay);
}


</script>';



// Check if $_SESSION['fine'] is set before using it
if(isset($_SESSION['fine'])) {
    // Initialize $fine with the value of $_SESSION['fine']
    $fine = $_SESSION['fine'];
} else {
    // If $_SESSION['fine'] is not set, initialize $fine with a default value
    $fine = 0; // You can set this to whatever default value you prefer
}


// Function to calculate fine based on due date and book status
// function calculateFine($dueDate, $dateBorrowed)
// {
//     // Get current timestamp
//     $currentTimestamp = time();

//     // Calculate number of days since borrowed
//     $daysSinceBorrowed = floor(($currentTimestamp - strtotime($dateBorrowed)) / (60 * 60 * 24));

//     // Subtract 3 days to account for the rental time valid only
//     $daysOverdue = max(0, $daysSinceBorrowed - 3); // Ensure it's non-negative

//     // Initialize fine
//     $fine = 0;
//     define('RETURNED_ON_TIME', 0);
//     echo '<script>';
//     echo 'console.log("Due Date: ' . $dueDate . '");';
//     echo 'console.log("Days Overdue: ' . $daysOverdue . '");';
//     echo 'console.log("Days Since Borrowed: ' . $daysSinceBorrowed . '");';
//     echo '</script>';
    

//     // Calculate the fine based on overdue status and book status
//     switch (true) {
//         case $daysOverdue > 0:
//             // Add default penalty fine of 30 pesos
//             $fine += 0;
//             // Add per-day fine of 15 pesos for each subsequent day of overdue
//             $fine += ($daysOverdue - 1) * 5;
//             break;
//         default:
//             // No additional fine for books in GOOD CONDITION or if none of the expected statuses are selected
//             break;
//     }


//     // Output the total fine after all calculations
//     echo "Total Fine: " . $fine . "<br>";

//     // Store the fine in session or database, if needed
//     $_SESSION['fine'] = $fine;
//     return $fine;
// }


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Get current timestamp
$currentTimestamp = time();
$dateBorrowed = $_SESSION['Due_Date'];

// Calculate number of days since borrowed
$daysSinceBorrowed = floor(($currentTimestamp - strtotime($dateBorrowed)) / (60 * 60 * 24));

// Subtract 3 days to account for the rental time valid only
$daysOverdue = max(0, $daysSinceBorrowed - 3); // Ensure it's non-negative

// Initialize fine
$fine = 0;
define('RETURNED_ON_TIME', 0);
echo '<script>';
echo 'console.log("Due Date: ' . $dateBorrowed . '");'; // Output the Due Date for logging
echo 'console.log("Days Overdue: ' . $daysOverdue . '");';
echo 'console.log("Days Since Borrowed: ' . $daysSinceBorrowed . '");';
echo '</script>';

// Calculate the fine based on overdue status and book status
switch (true) {
    case $daysOverdue > 0:
        // Add default penalty fine of 30 pesos
        $fine += 0;
        // Add per-day fine of 15 pesos for each subsequent day of overdue
        $fine += ($daysOverdue - 1) * 5;
        break;
    default:
        // No additional fine for books in GOOD CONDITION or if none of the expected statuses are selected
        break;
}


   // Output the total fine after all calculations
   echo "Total Fine: " . $fine . "<br>";

   // Get the payment status
   $Reason = $_POST['paymentStatus'];

   // Handle different payment status options
   switch ($Reason) {
       case 'DAMAGE':
           $value = 1000;
           $fine += $value;
           $_SESSION['fine'] += $fine;  // Update fine directly in the session
           break;
       case 'GOOD CONDITION':
           $value = 0;
           $fine += $value;
           $_SESSION['fine'] += $fine;
           break;
       case 'LOST':
           $value = 2000;
           $fine += $value;
           $_SESSION['fine'] += $fine;
           break;
       default:
           // Handle the case where the payment status is not recognized
           echo "Invalid payment status selected.";
           break;
   }

   
   // Output session fine for debugging
   echo '<script>';
   echo 'console.log("Session Fine is IN Staff' . $fine. '");';
   echo '</script>';

    // Database connection
    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get the BorrowDetails_ID from the session
    //  $bd_Id = $_SESSION['BorrowDetails_ID'];
    $currentDate = date("Y-m-d");

    // Update tbl_borrowdetails status
    $sql1 = "UPDATE tbl_borrowdetails SET tb_status = 'Returned' WHERE BorrowDetails_ID = ?";
    $stmt1 = $conn->prepare($sql1);
    if (!$stmt1) {
        die("Error in preparing statement 1: " . $conn->error);
    }
    $stmt1->bind_param("i", $bd_Id);

    // Update tbl_borrow status
    $sql2 = "UPDATE tbl_borrow SET tb_status = 'Returned' WHERE Borrow_ID = ?";
    $stmt2 = $conn->prepare($sql2);
    if (!$stmt2) {
        die("Error in preparing statement 2: " . $conn->error);
    }
    $stmt2->bind_param("i", $bd_Id);

    // Update tbl_returningdetails status
    $sql3 = "UPDATE tbl_returningdetails SET tb_status = 'Returned' WHERE BorrowDetails_ID = ?";
    $stmt3 = $conn->prepare($sql3);
    if (!$stmt3) {
        die("Error in preparing statement 3: " . $conn->error);
    }
    $stmt3->bind_param("i", $bd_Id);

    // // Update tbl_returned with current date and status
    // $sql4 = "UPDATE tbl_returned SET Date_Returned = ?, tb_status = 'Resolved' WHERE = ?)
    // )";

    // $stmt4 = $conn->prepare($sql4);
    // if (!$stmt4) {
    //     die("Error in preparing statement 4: " . $conn->error);
    // }

    // $stmt4->bind_param("si", $currentDate, $bd_Id);

    // Get Borrower_ID from session


    $borrowerId = $_SESSION['BorrowDetails_ID'];

    // Get current date and time
    $currentDateTime = date("Y-m-d H:i:s");

    if($fine != 0){
        echo '<script>';
        echo 'console.log("Fine is not Equals to 0");';
        echo '</script>';
        
    // Prepare SQL statement to insert fine information
    $sql5 = "INSERT INTO tbl_fines (Borrower_ID, ReturnDetails_ID, Amount, Reason, Payment_Status, Date_Created, Payment_Date) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt5 = $conn->prepare($sql5);

    if (!$stmt5) {
        die("Error in preparing statement 5: " . $conn->error);
    }
    $paymentStatus = "Paid";
     // Bind parameters and execute statement
     $stmt5->bind_param("iiissss", $borrowerId, $bd_Id, $fine, $Reason, $paymentStatus, $currentDate, $currentDateTime);
     $status5 = $stmt5->execute();

    }else{
        echo '<script>';
echo 'console.log("Fine is 0");';
echo '</script>';
    }

    
$_SESSION['fine'] = $fine;

    
echo '<script>';
echo 'console.log("Session Fine is IN Staff' . $_SESSION['fine']. '");';
echo '</script>';


   
    // Set the payment status
    $paymentStatus = "Paid";
    $_SESSION['stat'] =  $paymentStatus;
    $accessionCode = $_SESSION['Accession_Code'];
    $qtyb = $_SESSION['qty'];
    $sqlUpdateQuantity = "UPDATE tbl_books SET Quantity = Quantity + ? WHERE Accession_Code = ?";
    $stmtUpdateQuantity = $conn->prepare($sqlUpdateQuantity);

    if ($stmtUpdateQuantity) {
        // Bind parameters
        $stmtUpdateQuantity->bind_param("is", $qtyb, $accessionCode);

        // Execute the statement
        if ($stmtUpdateQuantity->execute()) {
        } else {
            echo "Error updating quantity: " . $stmtUpdateQuantity->error;
        }

        // Close the statement
        $stmtUpdateQuantity->close();
    } else {
        echo "Error in preparing the statement: " . $conn->error;
    }



    // Execute the queries
    $status1 = $stmt1->execute();
    $status2 = $stmt2->execute();
    $status3 = $stmt3->execute();
    // $status4 = $stmt4->execute();
  

    // Check each query execution status
    if ($status1 && $status2 && $status3  ) {
        // All queries executed successfully
   
        echo '<script>
        // Call showToast with "success" message type after successful insertion
        showToast("success", "Transaction Complete");
        // Redirect to print_return.php after 3 seconds with fine as a query parameter
         redirectToPage("queries/print_return.php?fine=' . urlencode($fine) . '", 1500);
      </script>';
      

        // echo '<script>alert("Record Updated successfully."); window.location.href = "queries/print_return.php";</script>';
    
    } else {
        // Error occurred while executing queries
        echo "Error updating status:";
        if (!$status1) {
            echo " Error in statement 1: " . $stmt1->error;
        }
        if (!$status2) {
            echo " Error in statement 2: " . $stmt2->error;
        }
        if (!$status3) {
            echo " Error in statement 3: " . $stmt3->error;
        }
        // if (!$status4) {
        //     echo " Error in statement 4: " . $stmt4->error;
        // }
     
    }


    // Close the database connection
    $conn->close();
}



?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VillaReadHub - Return List Process</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="staff.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png ">
</head>

<body>
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" id="navMenu"><!--sidenav container-->
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
          <strong><span><?php echo $userData['First_Name'] . "<br/>" . $_SESSION["role"]; ?></span></strong>   </div>     <hr>
        <div>
            <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
                <li class="nav-item"> <a href="./staff_dashboard.php" class="nav-link link-body-emphasis "> <i class='bx bxs-home'></i>Dashboard </a> </li>
                <li class="nav-item "> <a href="./staff_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
                <li class="nav-item"> <a href="./staff_transaction_dash.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transaction</a> </li>
                <li class="nav-item"> <a href="./staff_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
                <li class="nav-item"> <a href="./staff_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
                <hr>
                <li class="nav-item"> <a href="./staff_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
                <li class="nav-item"> <a href="logout.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
            </ul>
        </div>
    </div>

    <div class="board container"><!--board container-->
        <h2>Request List Proccess</h2>



        <!-- Container for displaying search results with a fixed height and scrollable content -->
        <div class="container mt-3" style="max-height: 900px; overflow-y: auto;">

            <?php

            // Initialize $bd_Id to the BorrowDetails_ID from session
            $bd_Id = $_SESSION['BorrowDetails_ID'];

            // Prepare the SQL statement with a placeholder for the BorrowDetails_ID
            $sql = "SELECT DISTINCT
            b.User_ID, 
            b.Accession_Code, 
            bk.Book_Title, 
            bd.Quantity, 
            b.Date_Borrowed, 
            b.Due_Date, 
            bd.tb_status, 
            bd.Borrower_ID, 
            b.Borrow_ID
        FROM
            tbl_borrowdetails AS bd
            INNER JOIN
            tbl_borrow AS b
            ON 
                bd.Borrower_ID = b.Borrower_ID AND
                bd.BorrowDetails_ID = b.Borrow_ID
            INNER JOIN
            tbl_books AS bk
            ON 
                b.Accession_Code = bk.Accession_Code
            INNER JOIN
            tbl_borrower AS br
            ON 
                b.Borrower_ID = br.Borrower_ID AND
                bd.Borrower_ID = br.Borrower_ID
        WHERE
            bd.BorrowDetails_ID =  ?";

            // Prepare and bind the statement
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $bd_Id);

            // Execute the statement
            $stmt->execute();

            // Get the result
            $result = $stmt->get_result();

            // Close the prepared statement (we'll reuse $stmt for the form later)
            $stmt->close();
            $conn->close();
            ?>

            <!-- Container for displaying search results with a fixed height and scrollable content -->
            <div class="container mt-3" style="max-height: 900px; overflow-y: auto;">
    <?php
    if ($result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            $_SESSION['Accession_Code'] = $row["Accession_Code"];
            $_SESSION['Book_Title'] = $row["Book_Title"];
            $_SESSION['Quantity'] = $row["Quantity"];
            $_SESSION['Date_Borrowed'] = $row["Date_Borrowed"];
            $_SESSION['Due_Date'] = $row["Due_Date"];
            $_SESSION['status'] = $row["tb_status"];
            $_SESSION['qty'] = $row["Quantity"];
            ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Book Information</h5>
                    <p class="card-text"><strong>Accession Code:</strong> <?php echo $row["Accession_Code"]; ?></p>
                    <p class="card-text"><strong>Book Title:</strong> <?php echo $row["Book_Title"]; ?></p>
                    <p class="card-text"><strong>Quantity:</strong> <?php echo $row["Quantity"]; ?></p>
                    <p class="card-text"><strong>Date Borrowed:</strong> <?php echo $row["Date_Borrowed"]; ?></p>
                    <p class="card-text"><strong>Due Date:</strong> <?php echo $row["Due_Date"]; ?></p>
                   <?php
                    $bookStatus = "LOST"; // Example status, replace with actual logic
                    // $fine = calculateFine($row["Due_Date"], $row["Date_Borrowed"]);
                    ?>
                    <p class="card-text"><strong>Fine:</strong> <?php echo $fine; ?></p>

                    <form class="update-form" method="POST" action="">
                        <div class="form-group">
                            <label for="paymentStatus">Book Status:</label><br>
                            <div class="form-check">
                                <input type="radio" id="damage" name="paymentStatus" value="DAMAGE" class="form-check-input">
                                <label for="damage" class="form-check-label">Damage</label><br>
                            </div>
                        
                            <div class="form-check">
                                <input type="radio" id="goodCondition" name="paymentStatus" value="GOOD CONDITION" class="form-check-input">
                                <label for="goodCondition" class="form-check-label">Good Condition</label><br>
                            </div>
                            <div class="form-check">
                                <input type="radio" id="lost" name="paymentStatus" value="LOST" class="form-check-input">
                                <label for="lost" class="form-check-label">Lost</label><br>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Proceed to Payment</button>
                    </form>
                </div>
            </div>
        <?php
        }
    } else {
        echo "No records found for the provided Borrower ID.";
    }
    ?>
</div>

 <div class="toastNotif" class="hide">
        <div class="toast-content">
            <i class='bx bx-check check'></i>

            <div class="message">
                <span class="text text-1">Success</span><!-- this message can be changed to "Success" and "Error"-->
                <span class="text text-2"></span> <!-- specify based on the if-else statements -->
            </div>


        </div>
        <i class='bx bx-x close'></i>
        <div class="progress"></div>
    </div>


                <script>
                    function printAndToggleMenu() {
                        // Get the navigation menu element
                        var navMenu = document.getElementById('navMenu');

                        // Hide the navigation menu
                        navMenu.style.display = 'none';

                        // Trigger the print dialog
                        window.print();
                    }
                </script>



            </div>

            <script>
                function updateAndSetSession(borrowId) {
                    // Redirect to staff_return_transaction.php with the borrowId parameter
                    window.location.href = "staff_return_transaction.php?borrowId=" + borrowId;
                }
            </script>




<!--  THIS CODE IS REDUNDANT NOO NEED FOR SEARCH FUNCTIONALITY  -->

            <!-- <script>
                // Get the search input field
                const searchInput = document.getElementById("searchInput");

                // Add event listener for input field keyup event
                searchInput.addEventListener("keyup", function() {
                    // Get the search input value
                    const inputValue = searchInput.value;

                    // Send an AJAX request to the server
                    const xhr = new XMLHttpRequest();
                    xhr.open("GET", "staff_return.php?searchInput=" + inputValue, true); // Pass the searchInput value
                    xhr.onload = function() {
                        if (xhr.status >= 200 && xhr.status < 300) {
                            // Update the search results container with the response from the server
                            document.getElementById("searchResults").innerHTML = xhr.responseText;
                        } else {
                            console.error(xhr.statusText);
                        }
                    };
                    xhr.send();
                });
            </script> -->



            <!-- <script>
                document.getElementById("requestButton").addEventListener("click", function() {
                    window.location.href = "staff_request_form.php";
                });
            </script> -->



            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>
            <!-- <script>
                let date = new Date().toLocaleDateString('en-US', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric',
                    weekday: 'long',
                });
                document.getElementById("currentDate").innerText = date;

                setInterval(() => {
                    let time = new Date().toLocaleTimeString('en-US', {
                        hour: 'numeric',
                        minute: 'numeric',
                        second: 'numeric',
                        hour12: 'true',
                    })
                    document.getElementById("currentTime").innerText = time;

                }, 1000)


                let navItems = document.querySelectorAll(".nav-item"); //adding .active class to navitems 
                navItems.forEach(item => {
                    item.addEventListener('click', () => {
                        document.querySelector('.active')?.classList.remove('active');
                        item.classList.add('active');


                    })

                }) -->
            </script>
</body>

</html>