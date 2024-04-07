<?php
session_start();

// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}


// Check if the ID parameter is set in the URL
if(isset($_GET['id'])) {
    // Get the ID value from the URL
    $id = $_GET['id'];

        
        // Database connection
        $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
            // SQL query to retrieve available books
            $sql = "SELECT tbl_requestbooks.* FROM tbl_requestbooks WHERE Request_ID ='$id'";

            $result = $conn->query($sql);


    echo "ID received from previous page: " . $id;
} else {
    // If the ID parameter is not set in the URL
    echo "No ID parameter found in the URL.";
}

if(isset($_POST['submit'])) {
    $requestID = $_GET['id'];
    $authorsName = $_POST['Authors_ID'];
    
    echo "<br>Authors ID: " . $authorsName."<br>";

    $authorsID = substr(uniqid('A_', true), -6);
    $pubID = substr(uniqid('P_', true), -6);

    $bookTitle = $_POST['Book_Title'];
    $pubname = $_POST['Publisher_Name'];
    $edition = $_POST['tb_edition'];
    $yr = $_POST['Year_Published'];
    $qty = $_POST['Quantity'];
    $price = $_POST['price'];
    $stat = $_POST['tb_status'];
    $bibliography ="NA";
    $isbn = 1; // Assuming ISBN is always 1
    $sectionCode = $_POST["section"]; 
    $shelfNumber = $_POST["shelf"];

    echo "<br>".$bookTitle;
    echo "<br>".$pubname;
    echo "<br>".$edition;
    echo "<br>".$yr;
    echo "<br>".$qty;
    echo "<br>".$price;
    echo "<br>".$stat;

    // Prepare the SQL statement to insert data into tbl_authors
    $sql = "INSERT INTO tbl_authors (Authors_ID, Authors_Name, Nationality) 
    VALUES ('$authorsID', '$authorsName ', 'N/A' )";

    $pubsql = "INSERT INTO tbl_publisher (Publisher_Name, Publisher_Name, Address) 
    VALUES ('$pubID', '$pubname', 'NA')";

    $booksql = "INSERT INTO tbl_books (Book_Title, Authors_ID, Publisher_Name, Section_Code, Shelf_Number, tb_edition, Year_Published, ISBN, Bibliography, Quantity, Price, tb_status) 
    VALUES ('$bookTitle', '$authorsID', '$pubID', '$sectionCode', '$shelfNumber', '$edition', '$yr', '$isbn', '$bibliography', '$qty', '$price', 'Available')";

    $update = "UPDATE ";

// Execute the SQL statement
if ($conn->query($sql) === TRUE) {
    // If the insertion is successful
    $lastInsertedID = $conn->insert_id; // Get the auto-generated Authors_ID
 //   echo "New record created successfully. The Author's ID is: " . $lastInsertedID;
} else {
    // If an error occurred during insertion
    echo "<br>Error: " . $sql . "<br>" . $conn->error;
}

// Execute the SQL statement
if ($conn->query($pubsql) === TRUE) {
    // If the insertion is successful
    $lastInsertedID = $conn->insert_id; // Get the auto-generated Authors_ID
  //  echo "New record created successfully. The Author's ID is: " . $lastInsertedID;
} else {
    // If an error occurred during insertion
    echo "<br>Error: " . $pubsql . "<br>" . $conn->error;
}


// Execute the SQL statement
if ($conn->query($booksql) === TRUE) {
    // If the insertion is successful
    $lastInsertedID = $conn->insert_id; // Get the auto-generated Authors_ID
  //  echo "New record created successfully. The Author's ID is: " . $lastInsertedID;
} else {
    // If an error occurred during insertion
    echo "<br>Error: " . $booksql . "<br>" . $conn->error;
}

// Update tb_status based on Request_ID
$sql = "UPDATE tbl_requestbooks SET tb_status = 'Approved' WHERE Request_ID = '$requestID'";

if ($conn->query($sql) === TRUE) {
    echo '<script>alert("Record Updated Successfully!");</script>';
    echo '<script>window.location.href = "admin_books.php";</script>';

} else {
    echo "Error updating record: " . $conn->error;
}

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
            <li class="nav-item active"> <a href="./admin_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./admin_transactions.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transactions</a> </li>
            <li class="nav-item"> <a href="./admin_staff.php" class="nav-link link-body-emphasis"><i class='bx bxs-user'></i>Manage Staff</a> </li>
            <li class="nav-item"> <a href="./admin_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./admin_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <li class="nav-item"> <a href="./admin_generate_report.php" class="nav-link link-body-emphasis"><i class='bx bxs-cloud'></i>Generate Report</a> </li>
            <hr>
            <li class="nav-item"> <a href="./admin_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
    </div>
    
    <h2>Book Request Information</h2>

<div class="board container">
    <form method="POST" action="">

        <?php

   
     
  // Output data of each row
while ($row = $result->fetch_assoc()) {

    echo "<p><strong>Book Title:</strong> " . $row["Book_Title"] . "</p>";
    echo "<p><strong>Authors ID:</strong> " . $row["Authors_ID"] . "</p>";
    echo "<p><strong>Publisher ID:</strong> " . $row["Publisher_Name"] . "</p>";
    echo "<p><strong>Edition:</strong> " . $row["tb_edition"] . "</p>";
    echo "<p><strong>Year Published:</strong> " . $row["Year_Published"] . "</p>";
    echo "<p><strong>Quantity:</strong> " . $row["Quantity"] . "</p>";
    echo "<p><strong>Price:</strong> " . $row["price"] . "</p>";
    echo "<p><strong>Status:</strong> " . $row["tb_status"] . "</p>";

    echo "<input type='Visible' id='Authors_ID' name='Authors_ID' value='" . $row["Authors_ID"] . "'>";
    echo "<input type='Visible' name='Book_Title' value='" . $row["Book_Title"] . "'>";
    echo "<input type='Visible' name='Publisher_Name' value='" . $row["Publisher_Name"] . "'>";
    echo "<input type='Visible' name='tb_edition' value='" . $row["tb_edition"] . "'>";
    echo "<input type='Visible' name='Year_Published' value='" . $row["Year_Published"] . "'>";
    echo "<input type='Visible' name='Quantity' value='" . $row["Quantity"] . "'>";
    echo "<input type='Visible' name='price' value='" . $row["price"] . "'>";
    echo "<input type='Visible' name='tb_status' value='" . $row["tb_status"] . "'>";

    echo "<br><label for='section'>section:</label>";
    echo "<input type='number' id='section' name='section' min='1' max='5' required>";

    echo "<br><label for='shelf'>Shelf Number:</label>";
    echo "<input type='number' id='shelf' name='shelf' min='1' max='5' required>";
}

    
           
?>
<button type="submit" class="btn btn-primary" id="submit" name="submit">Procure Book</button>
<a href="admin_books.php" class="btn btn-primary">Cancel</a>
</form>

</div>



<script>
  document.getElementById('submit').addEventListener('click', function() {
    // Collect any necessary data from the page
    var bookId = "123"; // Example data, replace with actual data collection logic

    // Make an asynchronous request to a PHP script
    fetch('insert_data.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            bookId: bookId // Pass any data you collected to the server
        })
    })
    .then(response => {
        if (response.ok) {
            console.log('Data inserted successfully');
            // Optionally, perform any additional actions after successful insertion
        } else {
            console.error('Failed to insert data');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
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