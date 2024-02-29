<?php

session_start();
// Check if Accession Code is provided via GET or POST
if(isset($_REQUEST['accession_code'])) {
    // Sanitize input to prevent SQL injection
    
    $conn =  mysqli_connect("localhost","root","root","db_library", 3307); //database connection
    
    $accession_code = mysqli_real_escape_string($conn, $_REQUEST['accession_code']);
    
    // Query to retrieve book details based on Accession Code
    $sql = "SELECT books.*, authors.Authors_Name 
            FROM books 
            INNER JOIN authors ON books.Authors_ID = authors.Authors_ID 
            WHERE Accession_Code = '$accession_code'";
    
    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            // Display book details
       
            echo "<div class='books-container'>";
           
            echo "</div>";
        } else {
            // Book not found
            echo "No book found with the provided Accession Code";
        }
    } else {
        // SQL query error
        echo "Error: " . $conn->error;
    }
    // Close the database connection
    $conn->close();
} else {
    // Accession Code is not provided
    echo "Accession Code is required";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VillaReadHub - Return</title>
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
            <strong><span><?php echo $_SESSION["staff_name"] ."<br/>"; echo $_SESSION["role"]; ?></span> </strong> 
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./staff_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./staff_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./staff_borrow_dash.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Borrow</a> </li>
            <li class="nav-item active"> <a href="./staff_return.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Return</a> </li>
            <li class="nav-item"> <a href="./staff_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./staff_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <hr>
            <li class="nav-item"> <a href="./staff_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
    </div>

    <div class="board container"><!--board container-->
    <div class='books-container'>
    <h1>Search Book by Accession Code</h1>
    <form action="staff_book_borrow_find.php" method="GET">
        <label for="accession_code">Accession Code:</label>
        <input type="text" id="accession_code" name="accession_code" placeholder="Enter Accession Code" autofocus>
    </form>

    <?php
        if ($result && $result->num_rows > 0) {
            echo "<h2>Books Found</h2>";
            echo "<div class='books-container'>";
            while ($book_details = $result->fetch_assoc()) {
                echo "<div class='book'>";
                echo "<p><strong>Title:</strong> " . $book_details['Book_Title'] . "</p>";
                echo "<p><strong>Author:</strong> " . $book_details['Authors_Name'] . "</p>";
                echo "<p><strong>Status:</strong> " . $book_details['tb_status'] . "</p>";
                echo "</div>";
            }
            echo "</div>";
        } else {
            // Book not found or error occurred
            echo "<p>No book found with the provided Accession Code</p>";
        }
        ?>


    </div>
    </div>
    <script>
        // Get the input field and form
        const inputField = document.getElementById('accession_code');
        const form = document.getElementById('searchForm');

        // Add event listener to input field for keyup event
        inputField.addEventListener('keyup', function(event) {
            // Prevent the default form submission behavior
            event.preventDefault();
            // If Enter key is pressed (key code 13), submit the form
            if (event.keyCode === 13) {
                form.submit();
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