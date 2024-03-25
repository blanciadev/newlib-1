<?php
session_start();

// Handle form submission for password update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Database connection
    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Retrieve the email from session
    $resetEmail = $_SESSION['_email'];

    // Query to retrieve the current password from the database
    $query = "SELECT tb_password FROM tbl_employee WHERE E_mail = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $resetEmail);
    $stmt->execute();
    $stmt->store_result();

    // Check if the query executed successfully
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($currentPassword);
        $stmt->fetch();

        // Verify if the old password matches the current password
        if ($oldPassword == $currentPassword) {
            // Check if the new password matches the confirm password
            if ($newPassword === $confirmPassword) {
                // Update the password in the database
                $updateQuery = "UPDATE tbl_employee SET tb_password = ? WHERE E_mail = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param("ss", $newPassword, $resetEmail);

                if ($updateStmt->execute()) {
                    echo '<script>alert("Password updated successfully!");</script>';
                    header("Location: ../index.php");
                } else {
                    echo "Error updating password: " . $conn->error;
                }
            } else {
                echo '<script>alert("New password and confirm password do not match!");</script>';
            }
        } else {
            echo '<script>alert("Incorrect old password!");</script>';
        }
    } else {
        echo "Error retrieving password: " . $conn->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
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
    <link href="./staff.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png ">
</head>
<body>
   

    <div class="board container"><!--board container-->

    <form id="userProfileForm" action="" method="post">
  
    <!-- Password Change Section -->
    <div class="col-md-4">
        <label for="oldPassword" class="form-label">Old Password</label>
        <input type="password" class="form-control" id="oldPassword" name="oldPassword" required>
    </div>
    <div class="col-md-4">
        <label for="newPassword" class="form-label">New Password</label>
        <input type="password" class="form-control" id="newPassword" name="newPassword" required>
    </div>
    <div class="col-md-4">
        <label for="confirmPassword" class="form-label">Confirm Password</label>
        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
    </div>

    <!-- Update button -->
    <button type="submit" class="btn btn-primary">Update Password</button>
    </form>

    </div>
        

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