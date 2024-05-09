<?php
session_start();
// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}


// Database connection
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_GET['deactivated_id'])) {
    $deactivatedId = $_GET['deactivated_id'];
    // Construct the SQL query to deactivated the record
    $updateSql = "UPDATE tbl_employee SET Status = 'Deactivated' WHERE User_ID = $deactivatedId";

    if (mysqli_query($conn, $updateSql)) {
        // Deletion successful, redirect to the same page to refresh the data
        echo '<script>alert("Record deactivatedd successfully.");</script>';
        echo '<script>window.location.href = "admin_staff.php";</script>';
        exit(); // Ensure to exit after header redirection
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}

if (isset($_POST['submit'])) {
    // Database connection
    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307);
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
    $token = 0;
    $password = $lastName . '@' . $role;

    $sql = "INSERT INTO tbl_employee (tb_password, First_Name, Middle_Name, Last_Name, tb_role, Contact_Number, E_mail, tb_address, token ) 
            VALUES ('$password','$firstName', '$lastName','$midName', '$role', '$contactNumber', '$email', '$address', $token )";

    try {
        if (mysqli_query($conn, $sql)) {
            // Display a banner for successful insertion
            echo '<script>alert("Default password will be your Lastname@role. Insertion Successful!");</script>';
            // Redirect the user back to the same page
            echo '<script>window.location.href = "admin_staff.php";</script>';
            exit();
        }
        else {
            // Handle other errors
            throw new Exception(mysqli_error($conn));
        }
    } catch (Exception $e) {
        // Handle the exception gracefully
        echo '<script>alert("Error: ' . $e->getMessage() . '");</script>';
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
<div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" ><!--sidenav container-->
        <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <h2>Villa<span>Read</span>Hub</h2> 
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon"/>
        </a><!--header container-->
       
        <div class="user-header  d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
        <?php
            $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307);
            $userID = $_SESSION["User_ID"];
            $sql = "SELECT User_ID, First_Name, Middle_Name, Last_Name, tb_role, Contact_Number, E_mail, tb_address, image_data 
                    FROM tbl_employee 
                    WHERE User_ID = $userID";
            $result = mysqli_query($conn, $sql);
            if (!$result) {
                echo "Error: " . mysqli_error($conn);
            } else {
                $userData = mysqli_fetch_assoc($result);
            // Fetch the First_Name from $userData
    $firstName = $userData['First_Name'];
    $role = $userData['tb_role'];

            }
            ?>
            <?php if (!empty($userData['image_data'])): ?>
                <!-- Assuming the image_data is in JPEG format, change the MIME type if needed -->
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else: ?>
                <!-- Change the path to your actual default image -->
                <img src="default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php echo  $firstName . "<br/>" .  $role; ?></span></strong>
    </div>
    <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item "> <a href="./admin_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
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


  
<!-- Button to trigger modal -->
<button type="button" class="btn btn-primary" onclick="openEmployeeModal()">Add Employee</button>
  </div>
   
   
   
    <h2>Staff Management</h2>
  
  
  
    <div class="board container"><!--board container-->

<?php
// Check if the query executed successfully
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch data from your database
$sql = "SELECT User_ID, First_Name, Middle_Name, Last_Name, tb_role, Contact_Number, E_mail, tb_address, status FROM tbl_employee";
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
        if ($row['tb_role'] === 'Staff') {
            // Check if the user status is "Deactivated"
            $status = $row['status'];
            
            // Check if the status is "Deactivated" and disable the button accordingly
            $disabled = ($status === 'Deactivated') ? 'disabled' : '';
        
            echo "<td><form class='deactivated-form' method='GET' action='admin_staff.php'>";
            echo "<input type='hidden' name='deactivated_id' value='" . $row['User_ID'] . "'>";
            // Add the $disabled variable to the button
            echo "<button type='submit' class='btn btn-danger btn-sm deactivated-btn' $disabled onclick='return confirmdeactivated(" . $row['User_ID'] . ")'>Deactivate</button>";
            echo "</form></td>";
        } else {
            echo '<td></td>'; // Empty cell for other roles
        }
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
}

// Close the database connection
mysqli_close($conn);
?>

</div>

<!-- Modal -->
<div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form to input employee data -->
                <form id="addEmployeeForm" method="POST" action="">
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
                    <!-- Submit button -->
                    <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>


                <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>

<script>

 function openEmployeeModal() {
        $('#employeeModal').modal('show');
    }


    function submitEmployeeForm() {
        // Serialize form data
        var formData = $('#addEmployeeForm').serialize();

        // Send form data via AJAX
        $.ajax({
            type: 'POST',
            url: 'admin_staff.php',
            data: formData,
            success: function(response) {
                // Handle success response
                console.log('Form submitted successfully:', response);
                // Optionally, close the modal or perform any other actions
                $('#employeeModal').modal('hide');
            },
            error: function(xhr, status, error) {
                // Handle error response
                console.error('Error submitting form:', error);
            }
        });
    }




 
   
</script>




</body>

</html>