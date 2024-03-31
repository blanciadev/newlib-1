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
if(isset($_GET['borrowIdadmin'])) {
    // Set the session variable after sanitizing it
    $_SESSION['BorrowDetails_ID'] = filter_var($_GET['borrowIdadmin'], FILTER_SANITIZE_STRING);
    // Optionally, you can redirect back to the same page or perform any other action
  //  header("Location: admin_book_return.php");
    
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
        bd.BorrowDetails_ID
    FROM
        tbl_borrowdetails AS bd
    INNER JOIN
        tbl_borrow AS b ON bd.Borrower_ID = b.Borrower_ID
    INNER JOIN
        tbl_books AS bk ON b.Accession_Code = bk.Accession_Code
    INNER JOIN
        tbl_borrower AS br ON b.Borrower_ID = br.Borrower_ID AND bd.Borrower_ID = br.Borrower_ID
    WHERE
        bd.BorrowDetails_ID = '$bd_Id'";



// Prepare the statement
$stmt = $conn->prepare($sql);

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();
// Function to calculate fine based on due date and book status
function calculateFine($dueDate, $dateBorrowed, $bookStatus) {
    // Get current timestamp
    $currentTimestamp = time();

    // Calculate number of days since borrowed
    $daysSinceBorrowed = floor(($currentTimestamp - strtotime($dateBorrowed)) / (60 * 60 * 24));
    
    // Subtract 3 days to account for the rental time valid only
    $daysOverdue = max(0, $daysSinceBorrowed - 3); // Ensure it's non-negative

    // Initialize fine
    $fine = 0;

    echo "Due Date: " . $dueDate . "<br>";
    echo "Days Overdue: " . $daysOverdue . "<br>";
    echo "Days Since Borrowed: " . $daysSinceBorrowed . "<br>";

    if ($daysOverdue > 0) {
        // Add default penalty fine of 30 pesos
        $fine += 30;
      //  echo "Added default penalty fine of 30 pesos<br>";
        
        // Add per-day fine of 15 pesos for each subsequent day of overdue
        $fine += ($daysOverdue - 1) * 15;
      //  echo "Added per-day fine of 15 pesos for each subsequent day of overdue<br>";
    } else {
        echo "No overdue fine<br>";
    }

    // Apply additional penalties based on book status
    if ($bookStatus == "LOST") {
        $fine += 50;
    } elseif ($bookStatus == "PARTIALLY DAMAGE") {
        $fine += 20;
    } elseif ($bookStatus == "GOOD CONDITION") {
        // No additional fine for books in good condition
    }

    // echo "Total Fine: " . $fine . "<br>";
    $_SESSION['fine'] = $fine;
    return $fine;
}


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fine = $_SESSION['fine'];
    // Database connection
    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get the BorrowDetails_ID from the session
    $bd_Id = $_SESSION['BorrowDetails_ID'];
    $currentDate = date("Y-m-d");

    // Update tbl_borrowdetails status
    $sql1 = "UPDATE tbl_borrowdetails SET tb_status = 'Paid' WHERE BorrowDetails_ID = ?";
    $stmt1 = $conn->prepare($sql1);
    if (!$stmt1) {
        die("Error in preparing statement 1: " . $conn->error);
    }
    $stmt1->bind_param("i", $bd_Id);
    
    // Update tbl_borrow status
    $sql2 = "UPDATE tbl_borrow SET tb_status = 'Paid' WHERE Borrow_ID = (
                SELECT Borrower_ID FROM tbl_borrowdetails WHERE BorrowDetails_ID = ?
            )";
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
    
     // Update tbl_returned with current date and status
     $sql4 = "UPDATE tbl_returned SET Date_Returned = ?, tb_status = 'Resolved' WHERE Borrow_ID IN (
        SELECT Borrow_ID FROM tbl_borrow WHERE Borrower_ID = (SELECT Borrower_ID FROM tbl_borrowdetails WHERE BorrowDetails_ID = ? LIMIT 1)
    )";
    
        $stmt4 = $conn->prepare($sql4);
        if (!$stmt4) {
        die("Error in preparing statement 4: " . $conn->error);
        }

        $stmt4->bind_param("si", $currentDate, $bd_Id);

            // Get Borrower_ID from session
            $borrowerId = $_SESSION['borrower_id'];

            // Get current date and time
            $currentDateTime = date("Y-m-d H:i:s");

            // Prepare SQL statement to insert fine information
            $sql5 = "INSERT INTO tbl_fines (Borrower_ID, ReturnDetails_ID, Amount, Payment_Status, Date_Created, Payment_Date) 
                    VALUES (?, ?, ?,  ?, ?, ?)";
            
            $stmt5 = $conn->prepare($sql5);
            if (!$stmt5) {
                die("Error in preparing statement 5: " . $conn->error);
            }

            // Bind parameters and execute statement
            $stmt5->bind_param("iiisss", $borrowerId, $bd_Id, $fine, $paymentStatus, $currentDate, $currentDateTime);
          
            $paymentStatus = "Paid"; // Assuming Payment_Status is always "Resolved"

            
            
            $accessionCode = $_SESSION['Accession_Code'];
            $qtyb = $_SESSION['qty'];
            $sqlUpdateQuantity = "UPDATE tbl_books SET Quantity = Quantity + ? WHERE Accession_Code = ?";
    $stmtUpdateQuantity = $conn->prepare($sqlUpdateQuantity);

    if ($stmtUpdateQuantity) {
        // Bind parameters
        $stmtUpdateQuantity->bind_param("is", $qtyb, $accessionCode);

        // Execute the statement
        if ($stmtUpdateQuantity->execute()) {
            echo "Quantity updated successfully.";
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
$status4 = $stmt4->execute();
$status5 = $stmt5->execute();

// Check each query execution status
if ($status1 && $status2 && $status3 && $status4 && $status5) {
    // All queries executed successfully
    echo "Status updated successfully!";
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
    if (!$status4) {
        echo " Error in statement 4: " . $stmt4->error;
    }
    if (!$status5) {
        echo " Error in statement 5: " . $stmt5->error;
    }
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
    <title>VillaReadHub - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="./admin.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png ">
</head>
<body>
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" ><!--sidenav container-->
        <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <h2>Villa<span>Read</span>Hub</h2> 
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon"/>
        </a><!--header container-->
        <div class="user-header mr-3 d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
                <img src="https://github.com/mdo.png" alt="" width="50" height="50" class="rounded-circle me-2">
                <p>(ADMIN)</p>
            </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./admin_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./admin_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item active"> <a href="./admin_transactions.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transactions</a> </li>
            <li class="nav-item"> <a href="./admin_staff.php" class="nav-link link-body-emphasis"><i class='bx bxs-user'></i>Manage Staff</a> </li>
            <li class="nav-item"> <a href="./admin_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./admin_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <li class="nav-item"> <a href="./admin_generate_report.php" class="nav-link link-body-emphasis"><i class='bx bxs-cloud'></i>Generate Report</a> </li>
            <hr>
            <li class="nav-item"> <a href="./admin_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
        
        
    </div>
    <div class="board container"><!--board container--> 
            
    <h2>Request List Proccess</h2>
    
  

<!-- Container for displaying search results with a fixed height and scrollable content -->
<div class="container mt-3" style="max-height: 900px; overflow-y: auto;">
   
    <?php
    
    if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            echo "<p>Borrower Details ID : " .$row["BorrowDetails_ID"]."</p>"; 
            echo "<p>Borrower ID : " .$row["Borrower_ID"]."</p>"; 
            echo "<p>Accession Code : " .$row["Accession_Code"]."</p>"; 
            $_SESSION['Accession_Code'] = $row["Accession_Code"];
            echo "<p>Book Title : " .$row["Book_Title"]."</p>"; 
            echo "<p>Quantity : " .$row["Quantity"]."</p>"; 
            $_SESSION['qty'] = $row["Quantity"];
            echo "<p><strong>Date Borrowed : </strong>" . $row["Date_Borrowed"] . "</p>"; 
            echo "<p><strong>Due Date : </strong>" . $row["Due_Date"] . "</p>"; 
                    
            echo "<p>Status : " .$row["tb_status"]."</p>"; 
            echo "Due Date from Database: " . $row["Due_Date"] . "<br>";
            echo "Date Borrowed from Database: " . $row["Date_Borrowed"] . "<br><br>";
                
            echo "<input type='hidden' name='Accession_Code' value='".$row["Accession_Code"]."'>";

            $bookStatus = "LOST";

            $fine = calculateFine($row["Due_Date"], $row["Date_Borrowed"], $bookStatus);
            echo "Fine: " . $fine . "<br>";

            // Radio buttons for selecting book status
            echo "<form class='update-form' method='POST' action=''>";
            echo "<input type='hidden' name='borrowIdadmin' id='borrowIdadmin' value='" . $row["BorrowDetails_ID"] . "'>";
            echo "<label for='bookStatus'>Book Status:</label><br>";
            echo "<input type='radio' id='damage' name='bookStatus' value='DAMAGE'>";
            echo "<label for='damage'>Damage</label><br>";
            echo "<input type='radio' id='partialDamage' name='bookStatus' value='PARTIALLY DAMAGE'>";
            echo "<label for='partialDamage'>Partially Damage</label><br>";
            echo "<input type='radio' id='goodCondition' name='bookStatus' value='GOOD CONDITION'>";
            echo "<label for='goodCondition'>Good Condition</label><br>";
            echo "<input type='radio' id='lost' name='bookStatus' value='LOST'>";
            echo "<label for='lost'>Lost</label><br>";
            echo "<button type='submit' class='btn btn-primary'>Proceed to Payment</button>";
            echo "</form>";
        }
    } else {
        echo "No records found for the provided Borrower ID.";
    }
    ?>
        
</div>

<script>
    function updateAndSetSession(borrowIdadmin) {
        // Redirect to staff_return_transaction.php with the borrowId parameter
        window.location.href = "staff_return_transaction.php?borrowIdadmin=" + borrowIdadmin;
    }
</script>






<script>
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
</script>



<script>
    document.getElementById("requestButton").addEventListener("click", function() {
        window.location.href = "staff_request_form.php";
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
