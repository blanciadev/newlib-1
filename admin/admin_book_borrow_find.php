<?php
session_start();

// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

$result = null; // Initialize $result variable

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   // Sanitize input to prevent SQL injection
   $conn =  mysqli_connect("localhost", "root", "root", "db_library_2", 3308); //database connection
   $Accession_Code = mysqli_real_escape_string($conn, $_REQUEST['Accession_Code']);
   
   // Store the accession code in a session variable
   $_SESSION['Accession_Code'] = $Accession_Code;
   
   // Query to retrieve book details based on Accession Code
   $sql = "SELECT
               tbl_books.*, 
               tbl_authors.Authors_Name, 
               tbl_books.Accession_Code
           FROM
               tbl_books
           INNER JOIN
               tbl_authors
           ON 
               tbl_books.Authors_ID = tbl_authors.Authors_ID
           WHERE
               Accession_Code = '$Accession_Code'";
   
   $result = $conn->query($sql);
   
   // Check if the query returned any rows
   if ($result && $result->num_rows > 0) {
       // Close the database connection
       $conn->close();
       // Redirect to staff_book_borrow_process.php
       header("Location: admin_book_borrow_process.php");
       exit(); // Ensure script execution stops after redirection
   } else {
       // Close the database connection
       $conn->close();
       // Display an alert for invalid Accession Code
       echo '<script>alert("Invalid Accession Code. No book found.");</script>';
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
    <div class='books-container'>
    <h1>Search Book by Accession Code</h1>

    <form id="dataform" action="" method="POST">

        <label for="Accession_Code">Accession Code:</label>
        <input type="text" id="Accession_Code" name="Accession_Code" placeholder="Enter Accession Code" required
   
   <?php
    // Check if there's a previous value in session, and if so, populate the input field with it
    if (isset($_SESSION['Accession_Code']) && !empty($_SESSION['Accession_Code'])) {
        echo ' value="' . htmlspecialchars($_SESSION['Accession_Code']) . '"';
    }
    ?>>

         
      <button type="submit" class="btn btn-primary" id="book_borrow" onclick="submitForm()">Get Book</button>
      </form>
  
        </div>
    </div>
    <script>
    // Get the input field and form
    const inputField = document.getElementById('Accession_Code');
    const form = document.getElementById('searchForm');

    // Add event listener to input field for keyup event
    inputField.addEventListener('keyup', function(event) {
        // If Enter key is pressed (key code 13) and the input field is not empty, submit the form
        if (event.keyCode === 13 && inputField.value.trim() !== "") {
            event.preventDefault(); // Prevent the default form submission behavior
            form.submit();
        }
    });

    // Add event listener to book_borrow button
    document.getElementById("book_borrow").addEventListener("click", function() {
        // Check if the accession code input field is not empty before redirecting
        if (inputField.value.trim() !== "") {
            window.location.href = "admin_book_borrow_process.php";
        } else {
            // If the accession code input field is empty, display an alert or handle it accordingly
            alert("Accession code is required.");
        }
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