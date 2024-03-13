<?php

session_start();





// Database connection
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307); 
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize the search query
$searchInput = "";
$searchQuery = "";

// Check if a search query is provided
if(isset($_GET['searchInput'])) {
    // Sanitize the input to prevent SQL injection
    $searchInput = $_GET['searchInput'];
 //   $_SESSION['BorrowerDetails_ID'];
}


// Prepare the SQL statement with a placeholder for the search input
$sql = "SELECT
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
tbl_borrow AS b
ON 
    bd.Borrower_ID = b.Borrow_ID
INNER JOIN
tbl_books AS bk
ON 
    b.Accession_Code = bk.Accession_Code
INNER JOIN
tbl_borrower AS br
ON 
    b.Borrower_ID = br.Borrower_ID
WHERE
bd.Borrower_ID ='$searchInput' ";



// Prepare the statement
$stmt = $conn->prepare($sql);

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VillaReadHub - Return List</title>
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
    <h2>Request List</h2>
    
  
    <div class="container mt-2">
    <input type="text" id="searchInput" class="form-control me-2" placeholder="Search...">
</div>

<!-- Container for displaying search results with a fixed height and scrollable content -->
<div class="container mt-3" style="max-height: 400px; overflow-y: auto;">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Borrow ID</th>
                <th>Borrower ID</th>
                <th>Accession Code</th>
                <th>Book Title</th>
                <th>Quantity</th>
                <th>Date Borrowed</th>
                <th>Due Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody id="searchResults">
            <?php
            if ($result) {
                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>".$row["BorrowDetails_ID"]."</td>";
                    echo "<td>".$row["Borrower_ID"]."</td>";
                    echo "<td>".$row["Accession_Code"]."</td>";
                    echo "<td>".$row["Book_Title"]."</td>";
                    echo "<td>".$row["Quantity"]."</td>";
                    echo "<td>".$row["Date_Borrowed"]."</td>";
                    echo "<td>".$row["Due_Date"]."</td>";
                    echo "<td>".$row["tb_status"]."</td>";
                    echo "<td>";
                    echo "<form class='update-form' method='GET' action='staff_borrow_details.php'>"; 
                    echo "<input type='hidden' name='borrowId' id='borrowId' value='".$row["BorrowDetails_ID"]."'>";

                    // Conditionally render the button based on the status
                    echo "<input type='hidden' name='borrowerId' value='".$row["Borrower_ID"]."'>";

                    if ($row["tb_status"] === 'Pending') {
                        echo "<button type='button' class='btn btn-primary btn-sm update-btn' data-borrow-id='".$row["BorrowDetails_ID"]."' onclick='updateAndSetSession(" . $row["BorrowDetails_ID"] . ")'>UPDATE</button>";
                    } else {
                        echo "<button type='button' class='btn btn-secondary btn-sm' disabled>Returned</button>";
                    }

                    echo "<div class='update-message'></div>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "No records found for the provided Borrower ID.";
            }
            ?>
        </tbody>
    </table>
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
