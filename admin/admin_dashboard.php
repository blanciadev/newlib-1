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
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item active"> <a href="./admin_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./admin_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./admin_transactions.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transactions</a> </li>
            <li class="nav-item"> <a href="./admin_staff.php" class="nav-link link-body-emphasis"><i class='bx bxs-user'></i>Manage Staff</a> </li>
            <li class="nav-item"> <a href="./admin_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./admin_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <li class="nav-item"> <a href="./admin_backup-restore.php" class="nav-link link-body-emphasis"><i class='bx bxs-cloud'></i>Backup & Restore</a> </li>
            <hr>
            <li class="nav-item"> <a href="./admin_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
        
        
    </div>
    <div class="board container"><!--board container--> 
        <div class="header">
            <div class="text">
                <div class="title">
                    <h2>Dashboard</h2>
                </div>
                <div class="datetime">
                    <p id = "currentDate"></p>
                    <p id = "currentTime"></p>
                </div>
            </div>
            
            <div class="user-header mr-3 d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
                <img src="https://github.com/mdo.png" alt="" width="50" height="50" class="rounded-circle me-2">
                <p>(ADMIN)</p>
            </div>

        </div>
        <div class="content">
    <div class="overview">
        <h3>Overview</h3>
        <div class="ovw-con">
            <?php
            // Database connection
            $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);

            // Query to get total books count
            $totalBooksQuery = "SELECT COUNT(*) AS totalBooks FROM tbl_books";
            $totalBooksResult = mysqli_query($conn, $totalBooksQuery);
            $totalBooksCount = mysqli_fetch_assoc($totalBooksResult)['totalBooks'];

            // Display total books count
            echo "<h4>{$totalBooksCount}</h4>";
            echo "<h4>Total Books</h4>";
            ?>
            <div class="line"></div>
            <?php
            // Query to get total visits count
            $totalVisitsQuery = "SELECT COUNT(*) AS totalVisits FROM tbl_log";
            $totalVisitsResult = mysqli_query($conn, $totalVisitsQuery);
            $totalVisitsCount = mysqli_fetch_assoc($totalVisitsResult)['totalVisits'];

            // Display total visits count
            echo "<h4>{$totalVisitsCount}</h4>";
            echo "<h4>Total Visits</h4>";
            ?>
        </div>
    </div>

    <div class="request-books">
        <h3>Pending Request</h3>
        <div class="request-books-con">
        <?php
// Database connection
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);


// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query to fetch data from tbl_requestbooks
$requestBooksQuery = "SELECT * FROM tbl_requestbooks";
$requestBooksResult = mysqli_query($conn, $requestBooksQuery);

if (!$requestBooksResult) {
    echo "Error fetching request books: " . mysqli_error($conn);
} else {
    // Check if there are any rows returned
    if (mysqli_num_rows($requestBooksResult) > 0) {
        // Display table header
      
       
       // Fetch and display each row
while ($row = mysqli_fetch_assoc($requestBooksResult)) {
    echo '<div>';
    echo '<h4><strong>' . $row['Book_Title'] . '</strong></h4>';
    echo '<p><strong>Authors ID:</strong> ' . $row['Authors_ID'] . '</p>';
    echo '<p><strong>Publisher ID:</strong> ' . $row['Publisher_ID'] . '</p>';
    echo '<p><strong>Year Published:</strong> ' . $row['Year_Published'] . '</p>';
    echo '<p><strong>Quantity:</strong> ' . $row['Quantity'] . '</p>';
    echo '</div>';
}


    } else {
        echo "No request books found.";
    }
}

// Close connection
mysqli_close($conn);
?>


        </div>
    </div>

    <div class="stats">
        <h3>Statistics</h3>
        <div class="stats-con"></div>
    </div>
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