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

// SQL query to retrieve requested books
$sql = "SELECT * FROM tbl_archive";
    $result = $conn->query($sql);


// Check if the form was submitted and the necessary POST data is set
if (isset($_POST['archive_btn'], $_POST['accession_code'], $_POST['book_title'], $_POST['qty'])) {
    // Retrieve POST data
    $accession_code = $_POST['accession_code'];
    $book_title = $_POST['book_title'];
    $qty = $_POST['qty'];

        // Database connection
        $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        // Insert data into tbl_archive with current timestamp
        $currentTime = date("Y-m-d H:i:s");
        $sqlInsert = "INSERT INTO tbl_archive (Accession_Code, Book_Title, User_ID, Quantity ,Date)
                      VALUES ('$accession_code', '$book_title', '{$_SESSION["User_ID"]}','$qty', '$currentTime')";
        if ($conn->query($sqlInsert) === TRUE) {
            echo "Data inserted into tbl_archive successfully";
        } else {
            echo "Error inserting data into tbl_archive: " . $conn->error;
        }
    
        // Update the status of the book to "Archived" based on Accession_Code
        $sqlUpdate = "UPDATE tbl_books SET tb_status = 'Archived' WHERE Accession_Code = '$accession_code'";
        if ($conn->query($sqlUpdate) === TRUE) {
            echo '<script>alert("Archived Book Successfully!");</script>';
                
        } else {
            echo "Error archiving book: " . $conn->error;
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
        <div class="user-header  d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
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
            <?php if (!empty($userData['image_data'])): ?>
                <!-- Assuming the image_data is in JPEG format, change the MIME type if needed -->
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else: ?>
                <!-- Change the path to your actual default image -->
                <img src="default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
        <strong><span><?php echo $_SESSION["admin_name"] . "<br/>" . $_SESSION["role"]; ?></span></strong> 
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
    
    <div class="board container">
        

    <h2>Book Information</h2>
   
    <div>
    <select id="statusFilter">
        <option value="Available">Available</option>
        <option value="Archived"selected>Archived</option>
        <option value="Request">Request</option>
    </select>

</div>

<table class="table table-striped">

    <tbody id="bookList"> 

    <?php
       
// Check if there are records
if ($result->num_rows > 0) {
    // Display the table header
    echo "<table class='table table-striped'>
            <thead>
                <tr>
                    <th>Archive ID</th>
                    <th>User ID</th>
                    <th>Accession Code</th>
                    <th>Book Title</th>
                    <th>Quantity</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>";

    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>".$row["Archive_ID"]."</td>
                <td>".$row["User_ID"]."</td>
                <td>".$row["Accession_Code"]."</td>
                <td>".$row["Book_Title"]."</td>
                <td>".$row["Quantity"]."</td>
                <td>".$row["Date"]."</td>
            </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "No records found.";
}
        // Close the database connection
        $conn->close();
        ?>

        </tbody>
    </table>
</div>

<script>
   document.getElementById('statusFilter').addEventListener('change', function() {
    var status = this.value;
    console.log('Selected status:', status);

    var url;
    if (status === 'Request') {
        url = 'admin_book_request.php';
    } else if (status === 'Archived') {
        url = 'admin_book_archived.php';
    } else {
        url = 'admin_books.php';
    }
    console.log('URL:', url);

    // Navigate to the selected URL
    window.location.href = url;
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