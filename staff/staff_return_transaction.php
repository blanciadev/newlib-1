<?php
session_start();



// Database connection
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307); 
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the borrowId parameter is set in the URL
if(isset($_GET['borrowId'])) {
    // Set the session variable after sanitizing it
    $_SESSION['BorrowDetails_ID'] = filter_var($_GET['borrowId'], FILTER_SANITIZE_STRING);
    // Optionally, you can redirect back to the same page or perform any other action
    header("Location: staff_return_transaction.php");
    exit();
}

// Initialize $bd_Id to an empty string
$bd_Id = $_SESSION['BorrowDetails_ID'];


        // Prepare the SQL statement with a placeholder for the search input
        $sql = "SELECT bd.BorrowDetails_ID, b.User_ID, 
        b.Accession_Code, bk.Book_Title, bd.Quantity, b.Date_Borrowed, 
        b.Due_Date, br.Borrower_ID, bd.tb_status
        FROM
        tbl_borrowdetails AS bd
        INNER JOIN
        tbl_borrow AS b ON bd.Borrower_ID = b.Borrow_ID
        INNER JOIN
        tbl_books AS bk ON b.Accession_Code = bk.Accession_Code
        INNER JOIN
        tbl_borrower AS br ON b.Borrower_ID = br.Borrower_ID
                WHERE   bd.BorrowDetails_ID ='$bd_Id'";



// Prepare the statement
$stmt = $conn->prepare($sql);

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Function to calculate fine based on due date
function calculateFine($dueDate, $dateBorrowed) {
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

    // echo "Total Fine: " . $fine . "<br>";

    return $fine;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get the BorrowDetails_ID from the session
    $bd_Id = $_SESSION['BorrowDetails_ID'];

    // Update the database with the payment status or any other necessary updates
  //  $sql = "UPDATE tbl_borrowdetails SET payment_status = 'Paid' WHERE BorrowDetails_ID = ?";
   // $stmt = $conn->prepare($sql);
   // $stmt->bind_param("i", $bd_Id);
    
    // if ($stmt->execute()) {
    //     // Payment updated successfully
    //     echo "Payment processed successfully!";
    // } else {
    //     // Error occurred while updating payment status
    //     echo "Error processing payment: " . $conn->error;
    // }
        echo "Reachable";
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
           
            <!-- <strong><span><?php echo $_SESSION["staff_name"] ."<br/>"; echo $_SESSION["role"]; ?></span> </strong>  -->
       
        </div>
        <hr>
        <div>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./staff_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item "> <a href="./staff_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./staff_borrow_dash.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Borrow</a> </li>
            <li class="nav-item active"> <a href="./staff_return.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Return</a> </li>
            <li class="nav-item"> <a href="./staff_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./staff_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <hr>
            <li class="nav-item"> <a href="./staff_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
        </div>
    </div>

    <div class="board container"><!--board container-->
    <h2>Request List Proccess</h2>
    
  

<!-- Container for displaying search results with a fixed height and scrollable content -->
<div class="container mt-3" style="max-height: 400px; overflow-y: auto;">
   
            <?php
           if ($result->num_rows > 0) {
                // Output data of each row
                while($row = $result->fetch_assoc()) {
                  
                    echo "<p>Borrower Details ID : " .$row["BorrowDetails_ID"]."</p>"; 
                    echo "<p>Borrower ID : " .$row["Borrower_ID"]."</p>"; 
                    echo "<p>Accession Code : " .$row["Accession_Code"]."</p>"; 
                    echo "<p>Book Title : " .$row["Book_Title"]."</p>"; 
                    echo "<p>Quantity : " .$row["Quantity"]."</p>"; 

                    echo "<p><strong>Date Borrowed : </strong>" . $row["Date_Borrowed"] . "</p>"; 
                    echo "<p><strong>Due Date : </strong>" . $row["Due_Date"] . "</p>"; 
                    
                    echo "<p>Status : " .$row["tb_status"]."</p>"; 

                        // Calculate fine
                    

                        echo "Due Date from Database: " . $row["Due_Date"] . "<br>";
                        echo "Date Borrowed from Database: " . $row["Date_Borrowed"] . "<br><br>";
                
                                    
                //     echo $dueDate = $row["Due_Date"]; 
                //    echo  $dateBorrowed = $row["Date_Borrowed"];
                    
                //     echo  $fine = calculateFine($dueDate, $dateBorrowed);

                $fine = calculateFine($row["Due_Date"], $row["Date_Borrowed"]);
                   

                echo "Fine: " . $fine . "<br>";

              
                }
            } else {
                echo "No records found for the provided Borrower ID.";
            }
            ?>

            <form class='update-form' method='POST' action=''>
                <input type='hidden' name='borrowId' id='borrowId' value='<?php echo $row["BorrowDetails_ID"]; ?>'>
                <button type="submit" class="btn btn-primary">Proceed to Payment</button>
            </form>


        
</div>
<script>
    function updateAndSetSession(borrowId) {
        // Redirect to staff_return_transaction.php with the borrowId parameter
        window.location.href = "staff_return_transaction.php?borrowId=" + borrowId;
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
