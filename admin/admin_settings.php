<?php
session_start();
// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307); // Database connection

// Retrieve User_ID from session
$userID = $_SESSION["User_ID"];

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
                 window.location.href = "staff_log.php";
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



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the connection is still alive, reconnect if needed
if (!mysqli_ping($conn)) {
    mysqli_close($conn);
    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307);
}else{
    if (isset($_POST['uploadImageBtn'])) {
        $imageFile = $_FILES['imageFile'];
        // Check if there was an error uploading the file
        if ($imageFile['error'] === UPLOAD_ERR_OK) {
            // Validate file type and size
            $fileType = $imageFile['type'];
            $fileSize = $imageFile['size'];

            // Restrict file types to PNG and JPEG
            if ($fileType != 'image/png' && $fileType != 'image/jpeg') {
                // echo '<script>alert("Error: Only PNG and JPEG files are allowed.");</script>';
                 echo '<script>
        // Call showToast with "success" message type after successful insertion
        showToast("error", "Error: Only PNG and JPEG files are allowed.");
    </script>';
            } elseif ($fileSize > 5242880) { // 5MB (in bytes)
                // echo '<script>alert("Error: The file size exceeds the limit (5MB).");</script>';
                 echo '<script>
        // Call showToast with "success" message type after successful insertion
        showToast("error", "Error: The file size exceeds the limit (5MB)");
    </script>';
            } else {
                // Read the file data
                $imageData = file_get_contents($imageFile['tmp_name']);

                // Perform database update
                $sql = "UPDATE tbl_employee SET image_data = ? WHERE User_ID = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "si", $imageData, $userID);
                if (mysqli_stmt_execute($stmt)) {
                    // echo '<script>alert("Image Updated successfully."); window.location.href = "staff_dashboard.php";</script>';
                         echo '<script>
                        // Call showToast with "success" message type after successful insertion
                        showToast("success", "Image Updated successfully.");
                        // Redirect to this page after 3 seconds
                        redirectToPage("admin_dashboard.php", 3000);
                    </script>';

                } else {
                    echo '<script>alert("Error updating image data.");</script>';
                     echo '<script>
        // Call showToast with "success" message type after successful insertion
        showToast("error", "Error updating image data.");
    </script>';
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            // echo '<script>alert("Error uploading file: ' . $imageFile['error'] . '");</script>';
                 echo '<script>
        // Call showToast with "success" message type after successful insertion
        showToast("error", "Error: The file size exceeds the limit (5MB)");
    </script>';
        }
    } 
 
// Check if the profile update form is submitted
if (isset($_POST['updateProfile'])) {
    // Profile update logic
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $contactNumber = $_POST['contactNumber'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    // Perform necessary validations if required

    // Update user's data in the database
    // $conn should be your database connection object
    $updateQuery = "UPDATE tbl_employee 
                    SET First_Name = ?, Middle_Name = ?, Last_Name = ?,
                        Contact_Number = ?, E_mail = ?, tb_address = ? 
                    WHERE User_ID = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);

    mysqli_stmt_bind_param($stmt, "ssssssi", $firstName, $middleName, $lastName, 
        $contactNumber, $email, $address, $userID);
    if (mysqli_stmt_execute($stmt)) {
        // echo '<script>alert("Profile Updated successfully."); window.location.href = "admin_dashboard.php";</script>';
     echo '<script>
                        // Call showToast with "success" message type after successful insertion
                        showToast("success", "Image Updated successfully.");
                        // Redirect to this page after 3 seconds
                        redirectToPage("admin_dashboard.php", 3000);
                    </script>';
    } else {
        echo '<script>alert("Error updating profile: ' . mysqli_error($conn) . '");</script>';
    }
    mysqli_stmt_close($stmt); // Close the statement
}

// Check if the password update form is submitted
if (isset($_POST['updatePassword'])) {
    // Password update logic
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Perform necessary validations if required

    // Check if the new password and confirm password match
    if ($newPassword === $confirmPassword) {
        // Check if the old password matches the user's password in the database
        $sqlCheckPassword = "SELECT tb_password FROM tbl_employee WHERE User_ID = ?";
        $stmtCheckPassword = mysqli_prepare($conn, $sqlCheckPassword);
        mysqli_stmt_bind_param($stmtCheckPassword, "i", $userID);
        mysqli_stmt_execute($stmtCheckPassword);
        mysqli_stmt_bind_result($stmtCheckPassword, $storedPassword);
        mysqli_stmt_fetch($stmtCheckPassword);
        mysqli_stmt_close($stmtCheckPassword);

        // Verify the old password using password_verify function
        if (($oldPassword == $storedPassword)) {
            // Check if old password is different from the new password
            if ($oldPassword != $newPassword) {
                // Old password matches and it's different from the new password, update the user's password in the database
                $updatePasswordQuery = "UPDATE tbl_employee 
                                        SET tb_password = ? 
                                        WHERE User_ID = ?";
                $stmtUpdatePassword = mysqli_prepare($conn, $updatePasswordQuery);

            

                mysqli_stmt_bind_param($stmtUpdatePassword, "si", $newPassword, $userID);
                if (mysqli_stmt_execute($stmtUpdatePassword)) {
                    // echo '<script>alert("Profile Updated successfully."); window.location.href = "admin_dashboard.php";</script>';
           echo '<script>
                        // Call showToast with "success" message type after successful insertion
                        showToast("success", "Image Updated successfully.");
                        // Redirect to this page after 3 seconds
                        redirectToPage("admin_dashboard.php", 3000);
                    </script>';
                } else {
                    // echo '<script>alert("Error updating password: ' . mysqli_error($conn) . '");</script>';
                 echo '<script>
        // Call showToast with "success" message type after successful insertion
        showToast("error", "Error updating password");
    </script>';
                }
                mysqli_stmt_close($stmtUpdatePassword); // Close the statement
            } else {
                // echo '<script>alert("Old and new passwords cannot be the same.");</script>';
              echo '<script>
        // Call showToast with "success" message type after successful insertion
        showToast("error", "Old and new passwords cannot be the same.");
    </script>';
            }
        } else {
            // echo '<script>alert("Old password does not match!");</script>';
      echo '<script>
        // Call showToast with "success" message type after successful insertion
        showToast("error", "Old password does not match!");
    </script>';
        }
    } else {
        // echo '<script>alert("New password and confirm password do not match!");</script>';
      echo '<script>
        // Call showToast with "success" message type after successful insertion
        showToast("error", "New password and confirm password do not match!");
    </script>';
    }
}
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VillaReadHub - Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="./admin.css" rel="stylesheet">
     <link href="./toast.css" rel="stylesheet">
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
            <li class="nav-item active"> <a href="./admin_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./admin_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
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

    <div class="settingForms">
        <form id="imageUploadForm" action="" method="post" enctype="multipart/form-data">
            <input type="file" name="imageFile" id="imageFile">
            <button type="submit" name="uploadImageBtn">Upload Image</button><br>
        </form>
    
        <form id="userProfileForm" action="" method="post">
                   
                    <!-- Display user data -->
                    <h4>Personal Details</h4>
                    <div class="mb-3">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo $userData['First_Name']; ?>" >
                    </div>
                    <div class="mb-3">
                        <label for="middleName" class="form-label">Middle Name</label>
                        <input type="text" class="form-control" id="middleName" name="middleName" value="<?php echo $userData['Middle_Name']; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo $userData['Last_Name']; ?>" >
                    </div> 
                    <div class="mb-3">
                        <label for="contactNumber" class="form-label">Contact Number</label>
                        <input type="tel" class="form-control" id="contactNumber" name="contactNumber" value="<?php echo $userData['Contact_Number']; ?>" >
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $userData['E_mail']; ?>" >
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" value="<?php echo $userData['tb_address']; ?>" >
                    </div>
                       <!-- Update button -->
                       <button type="submit" class="btn btn-primary" name="updateProfile" value="Update Profile">Update Profile</button>
  
              <br><br>
                    <!-- Password Change Section -->
                    <h4>Reset your Password</h4>
                    <div class="mb-3">
                        <label for="oldPassword" class="form-label">Old Password</label>
                        <input type="password" class="form-control" id="oldPassword" name="oldPassword" required>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                    </div>

                    <!-- Update button -->
                    <button type="submit" class="btn btn-primary" name="updatePassword" value="Update Password">Update Password</button>

                </form>
            </div>
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
    </div>
</div>
        

   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>
    <script> 
        //Toast Notification 
        const btn = document.querySelector(".showToast"),
            toast = document.querySelector(".toastNotif"),
            close = document.querySelector(".close"),
            progress = document.querySelector(".progress");

        btn.addEventListener("click", () => { // showing toast
            console.log("showing toast")
            toast.classList.add("showing");
            progress.classList.add("showing");
            setTimeout(() => {
                toast.classList.remove("showing");
                progress.classList.remove("showing");
                console.log("hide toast after 5s")
            }, 5000);
        });

        close.addEventListener("click", () => { // closing toast
            toast.classList.remove("showing");
            progress.classList.remove("showing");
        });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('[name="updateProfile"]').addEventListener('click', function() {
        // Remove the 'required' attribute from password fields
        document.getElementById('oldPassword').removeAttribute('required');
        document.getElementById('newPassword').removeAttribute('required');
        document.getElementById('confirmPassword').removeAttribute('required');
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