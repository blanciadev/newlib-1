<?php
session_start();


// Database connection
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the "FIRE" button is clicked
if(isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    // Construct the SQL query to delete the record
    $deleteSql = "DELETE FROM tbl_employee WHERE User_ID = $deleteId";
    
    if (mysqli_query($conn, $deleteSql)) {
        // Deletion successful, redirect to the same page to refresh the data
        header("Location: admin_staff.php");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}



if(isset($_POST['submit'])) {
    // Database connection
    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get form data
    $firstName = $_POST['empFirstName'];
    $lastName = $_POST['empLastName'];
    $midName = $_POST['midName'];
    $role = $_POST['empRole'];
    $contactNumber = $_POST['empContactNumber'];
    $email = $_POST['empEmail'];
    $address = $_POST['empAddress'];
    $password = $_POST['empPassword'];

    // Hash the password for security
    

    $sql = "INSERT INTO tbl_employee (tb_password, First_Name, Middle_Name, Last_Name, tb_role, Contact_Number, E_mail, tb_address ) 
            VALUES ('$password','$firstName', '$lastName','$midName', '$role', '$contactNumber', '$email', '$address' )";

    // Debugging: output the SQL query for inspection
    echo "SQL Query: $sql";

    if (mysqli_query($conn, $sql)) {
        // Display a banner for successful insertion
        echo '<div id="successBanner" style="display: none; background-color: #4CAF50; color: white; padding: 10px; text-align: center;">';
        echo 'Insertion Successful!';
        echo '</div>';
        
        // Show the banner and redirect after 3 seconds
        echo '<script>';
        echo 'document.getElementById("successBanner").style.display = "block";'; // Show the banner
        echo 'setTimeout(function(){ window.location.href = "admin_staff.php"; }, 3000);'; // Redirect after 3 seconds
        echo '</script>';
        
        exit();
    } else {
        // Debugging: output the error message
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
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
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary"><!--sidenav container-->
        <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <h2>Villa<span>Read</span>Hub</h2>
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon" />
        </a><!--header container-->
        <div class="user-header mr-3 d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
            <img src="https://github.com/mdo.png" alt="" width="50" height="50" class="rounded-circle me-2">
            <p>(ADMIN)</p>
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./admin_dashboard.php" class="nav-link link-body-emphasis "> <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./admin_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./admin_transactions.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transactions</a> </li>
            <li class="nav-item active"> <a href="./admin_staff.php" class="nav-link link-body-emphasis"><i class='bx bxs-user'></i>Manage Staff</a> </li>
            <li class="nav-item"> <a href="./admin_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./admin_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <li class="nav-item"> <a href="./admin_generate_report.php" class="nav-link link-body-emphasis"><i class='bx bxs-cloud'></i>Generate Report</a> </li>
            <hr>
            <li class="nav-item"> <a href="./admin_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>


        <!-- Add New Employee Button -->
        <button id="addEmployeeBtn" class="btn btn-primary">Add New Employee</button>
    </div>

    <div class="board container"><!--board container-->

        <?php
        // Check if the query executed successfully
      
                    
        // Database connection
        $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // Query to fetch data from your database
        $sql = "SELECT User_ID, First_Name, Middle_Name, Last_Name, tb_role, Contact_Number, E_mail, tb_address FROM tbl_employee";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            echo '<table class="table table-bordered">';
            echo '<thead><tr><th>User ID</th><th>First Name</th><th>Middle Name</th><th>Last Name</th><th>Role</th><th>Contact Number</th><th>Email</th><th>Address</th><th>Action</th></tr></thead>';
            echo '<tbody>';
        
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . $row['User_ID'] . '</td>';
                echo '<td>' . $row['First_Name'] . '</td>';
                echo '<td>' . $row['Middle_Name'] . '</td>';
                echo '<td>' . $row['Last_Name'] . '</td>';
                echo '<td>' . $row['tb_role'] . '</td>';
                echo '<td>' . $row['Contact_Number'] . '</td>';
                echo '<td>' . $row['E_mail'] . '</td>';
                echo '<td>' . $row['tb_address'] . '</td>';
        
                // Add a button for rows where Role is 'Staff'
                echo "<form class='delete-form' method='GET' action='admin_staff.php'>";
                if ($row['tb_role'] === 'Staff') {
                    echo "<form class='delete-form' method='GET' action='admin_staff.php'>";
                    echo "<input type='hidden' name='delete_id' id='delete_id_" . $row["User_ID"] . "' value='" . $row["User_ID"] . "'>";
                    echo "<td><button type='submit' class='btn btn-danger btn-sm delete-btn' onclick='return confirmDelete(" . $row["User_ID"] . ")'>DELETE</button></td>";
                    echo "</form>";
                } else {
                    echo '<td></td>'; // Empty cell for other roles
                }
                
               
                
              
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }
        
        ?>

        <form id="addEmployeeForm" method="POST" action="">
            <!-- Hidden container for adding a new employee -->
            <div id="addEmployeeContainer" class="container-fluid mt-3" style="display: none;">
                <br><br><br>
                <h4>Add New Employee</h4>

                <!-- Add input fields for employee details -->
                <div class="mb-3">
                    <label for="empFirstName" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="empFirstName" name="empFirstName" required>
                </div>
                <div class="mb-3">
                    <label for="empLastName" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="empLastName" name="empLastName" required>
                </div>
                <div class="mb-3">
                    <label for="midName" class="form-label">Middle Name</label>
                    <input type="text" class="form-control" id="midName" name="midName" required>
                </div>
                <div class="mb-3">
                    <label for="empRole" class="form-label">Role</label>
                    <select class="form-control" id="empRole" name="empRole" required>
                        <option value="Admin">Admin</option>
                        <option value="Staff">Staff</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="empPassword" class="form-label">Password</label>
                    <input type="password" class="form-control" id="empPassword" name="empPassword" required>
                </div>

                <div class="mb-3">
                    <label for="empContactNumber" class="form-label">Contact Number</label>
                    <input type="text" class="form-control" id="empContactNumber" name="empContactNumber" required>
                </div>
                <div class="mb-3">
                    <label for="empEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="empEmail" name="empEmail" required>
                </div>
                <div class="mb-3">
                    <label for="empAddress" class="form-label">Address</label>
                    <textarea class="form-control" id="empAddress" name="empAddress" rows="3" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary" name="submit">Submit</button>

            </div>
        </form>
        
   
<script>
function confirmDelete(userId) {
    console.log("Delete button clicked for User ID: " + userId);
    var deleteId = document.getElementById('delete_id_' + userId).value;
    if (confirm("Are you sure you want to delete this record?")) {
        window.location.href = "admin_staff.php?delete_id=" + deleteId;
    }
}




    // Function to show the add employee container
    function showAddEmployeeContainer() {
        document.getElementById('addEmployeeContainer').style.display = 'block';
    }
 // Event listener for the Add New Employee button
 document.getElementById('addEmployeeBtn').addEventListener('click', showAddEmployeeContainer);

   
</script>




</body>

</html>