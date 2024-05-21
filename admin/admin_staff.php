<?php

require "../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();



// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

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
             //  window.location.href = "admin_staff.php";
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
</script>';

// Database connection
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_GET['deactivated_id'])) {
    $deactivatedId = $_GET['deactivated_id'];
    // Construct the SQL query to deactivated the record
    $updateSql = "UPDATE tbl_employee SET Status = 'Deactivated' WHERE User_ID = $deactivatedId";

    if (mysqli_query($conn, $updateSql)) {
        // Deletion successful, redirect to the same page to refresh the data
        // echo '<script>alert("Deactivated.");</script>';
        // echo '<script>window.location.href = "admin_staff.php";</script>';
     
        echo '<script>
        // Call showToast with "success" message type after successful insertion
        showToast("success", "Account Deactivated.");
    </script>';
        // exit(); // Ensure to exit after header redirection
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}

if(isset($_GET['reactivate_id'])) {
    $reactivateId = $_GET['reactivate_id'];
    // Construct the SQL query to reactivate the record
    $updateSql = "UPDATE tbl_employee SET Status = 'Active' WHERE User_ID = $reactivateId";

    if (mysqli_query($conn, $updateSql)) {
        // Reactivation successful, redirect to the same page to refresh the data
       
        echo '<script>
        // Call showToast with "success" message type after successful insertion
        showToast("success", "Account Activated");
    </script>';

        // exit(); // Ensure to exit after header redirection
    } else {
        echo "Error reactivating record: " . mysqli_error($conn);
    }
}
if (isset($_POST['submit'])) {
    // Database connection
    $conn = new mysqli("localhost", "root", "root", "db_library_2", 3308);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get form data
    $firstName = $_POST['empFirstName']; 
    $midName = $_POST['midName'];
    $lastName = $_POST['empLastName'];
    $role = $_POST['empRole'];
    $contactNumber = $_POST['empContactNumber'];
    $email = $_POST['empEmail'];
    $address = $_POST['empAddress'];
    $token = 0;
    $password = $lastName . '@' . $role;

    // Insert data into the database
    $sql = "INSERT INTO tbl_employee (tb_password, First_Name, Middle_Name, Last_Name, tb_role, Contact_Number, E_mail, tb_address, token) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssi", $password, $firstName, $midName, $lastName, $role, $contactNumber, $email, $address, $token);

        if ($stmt->execute()) {
            echo '<script>
                showToast("success", "Default password will be your Lastname@role.");
                </script>';

            // Create a new PHPMailer instance
            $mail = new PHPMailer(true);
    
            try {
                // SMTP configuration for Gmail
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'villareadhub@gmail.com'; // Your Gmail email address
                $mail->Password = 'ulmh emwr tsbw ijao'; // Your Gmail password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                // Email content
                $mail->setFrom('villareadhub@gmail.com', 'Administrator'); // Set sender email and name
                $mail->addAddress($email); // Add recipient email
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = 'Employee Details';
                $mail->Body = '<h1>Employee Details</h1>' .
                    '<p>First Name: ' . htmlspecialchars($firstName) . '</p>' .
                    '<p>Middle Name: ' . htmlspecialchars($midName) . '</p>' .
                    '<p>Last Name: ' . htmlspecialchars($lastName) . '</p>' .
                    '<p>Role: ' . htmlspecialchars($role) . '</p>' .
                    '<p>Contact Number: ' . htmlspecialchars($contactNumber) . '</p>' .
                    '<p>Email: ' . htmlspecialchars($email) . '</p>' .
                    '<p>Address: ' . htmlspecialchars($address) . '</p>';

                // Send email
                if ($mail->send()) {
                    echo '<script>console.log("Email sent successfully");</script>';
                } else {
                    echo '<script>console.error("Error sending email: ' . $mail->ErrorInfo . '");</script>';
                }
            } catch (Exception $e) {
                echo '<script>console.error("Error sending email: ' . $mail->ErrorInfo . '");</script>';
            }
        } else {
            // Handle specific error for duplicate entry
            if ($conn->errno == 1062) { // Duplicate entry error code
                echo '<script>
                showToast("error", "Duplicate entry detected. Email already exists.");
                </script>';
            } else {
                throw new Exception($conn->error);
            }
        }
    } catch (Exception $e) {
        echo '<script>
        showToast("error", "Error: ' . $e->getMessage() . '");
        </script>';
    } finally {
        $stmt->close();
        $conn->close();
    }
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="./admin.css" rel="stylesheet">
    <link href="toast.css" rel="stylesheet">
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
            <li class="nav-item"> <a href="./admin_generate_report.php" class="nav-link link-body-emphasis"><i class='bx bxs-report'></i>Generate Report</a> </li>
            <hr>
            <li class="nav-item"> <a href="./admin_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="" data-bs-toggle="modal" data-bs-target="#logOut" class="nav-link link-body-emphasis"><i class='bx bx-log-out'></i>Log Out</a> </li>
        </ul>
    </div>
    <div class="board container-fluid">
        <div class="header1">
            <div class="text">
                <div class="title">
                    <h2>Staff Management</h2>
                </div>
            </div> 
            <div class="searchbar">
                <form action="">
                    <i class='bx bx-search' id="search-icon"></i>
                    <input type="search" id="searchInput"  placeholder="Search..." required>
                    
                </form>
            </div>
        </div>
        <div class="books container-fluid"> 
            <table class="table table-striped table-sm"> 
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th style="width: 100%;">Full Name</th> 
                        <th>Role</th>
                        <th>Contact Number</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Action</th>
                        <th></th>
                    </tr>
                </thead> 
                <tbody>

            
            <?php
                // Check if the query executed successfully
                $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Query to fetch data from your database
                $sql = "SELECT User_ID, First_Name, Middle_Name, Last_Name, tb_role, Contact_Number, E_mail, tb_address, status FROM tbl_employee";
                $result = mysqli_query($conn, $sql);

                if ($result) { 

                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<tr>';
                        echo '<td>' . $row['User_ID'] . '</td>';
                        echo '<td>' . $row['First_Name'] . ' ' . $row['Middle_Name'] . ' ' . $row['Last_Name'] .'</td>';  
                        echo '<td>' . $row['tb_role'] . '</td>';
                        echo '<td>' . $row['Contact_Number'] . '</td>';
                        echo '<td>' . $row['E_mail'] . '</td>';
                        echo '<td>' . $row['tb_address'] . '</td>';
                    
                        // Add buttons for rows where Role is 'Staff'
                        if ($row['tb_role'] === 'Staff') {
                            // Check if the user status is "Deactivated"
                            $status = $row['status'];
                            
                            // Check if the status is "Deactivated" and disable the button accordingly
                            $disabled = ($status === 'Deactivated') ? 'disabled' : ''; 

                            // Add condition to show the button only if the status is "Deactivated"
                            if ($status === 'Deactivated') { 
                                // Form for reactivation
                                echo "<td><form class='reactivate-form' method='GET' action='admin_staff.php'>";
                                echo "<input type='hidden' name='reactivate_id' value='" . $row['User_ID'] . "'>";
                                echo "<button type='submit' class='btn btn-success btn-sm reactivate-btn' onclick='return confirmReactivate(" . $row['User_ID'] . ")'>Activate</button>";
                                echo "</form></td>";
                            } else {
                                // Form for deactivation
                                echo "<td><form class='deactivated-form' method='GET' action='admin_staff.php'>";
                                echo "<input type='hidden' name='deactivated_id' value='" . $row['User_ID'] . "'>";
                                // Add the $disabled variable to the button
                                echo "<button type='submit' class='btn btn-danger btn-sm deactivated-btn' $disabled onclick='return confirmDeactivate(" . $row['User_ID'] . ")'>Deactivate</button>";
                                echo "</form></td>";
                            }
                            
                        } else {
                            echo '<td></td>'; // Empty cell for other roles
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
        <div class="btn-con"> 
        <!-- Button to trigger modal -->
        <button type="button" class="btn btn-primary" onclick="openEmployeeModal()">Add Employee</button>
    </div>
    </div> 
    
 

<!-- Modal -->
<div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
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
                        <label for="midName" class="form-label">Middle Initial</label>
                        <input type="text" class="form-control" id="midName" name="midName">
                    </div>
                    <div class="mb-3">
                        <label for="empLastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="empLastName" name="empLastName" required>
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
    <!--Logout Modal -->
    <div class="modal fade" id="logOut" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Logging Out...</h1>
            </div>
            <div class="modal-body">
                Do you want to log out?
            </div>
            <div class="modal-footer d-flex flex-row justify-content-center">
                <a href="javascript:history.go(0)"><button type="button" class="btn" data-bs-dismiss="modal">Cancel</button></a>
                <a href="../logout.php"><button type="button" class="btn">Log Out</button></a>
            </div>
            </div>
        </div>
    </div>


                <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>

<script>
    // JavaScript code for search functionality
    document.getElementById("searchInput").addEventListener("input", function() {
            let searchValue = this.value.toLowerCase();
            let rows = document.querySelectorAll("tbody tr");
            rows.forEach(row => {
                let cells = row.querySelectorAll("td");
                let found = false;
                cells.forEach(cell => {
                    if (cell.textContent.toLowerCase().includes(searchValue)) {
                        found = true;
                    }
                });
                if (found) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });

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