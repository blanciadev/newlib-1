<?php
session_start();
// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308); // database connection

// Fetch data from tbl_log
$sql = "SELECT DATE_FORMAT(Date_Time, '%Y-%m') AS Month, COUNT(*) AS Visits
        FROM tbl_log
        GROUP BY DATE_FORMAT(Date_Time, '%Y-%m')
        ORDER BY DATE_FORMAT(Date_Time, '%Y-%m') ASC";
$result = mysqli_query($conn, $sql);

// Initialize arrays to hold the labels and data for the chart
$labels = [];
$data = [];


// Process the fetched data
while ($row = mysqli_fetch_assoc($result)) {
    // Add the month to labels array
    $labels[] = $row['Month'];
    
    // Add the number of visits to data array
    $data[] = $row['Visits'];
}

// Convert labels and data arrays to JSON format
$labelsJSON = json_encode($labels);
$dataJSON = json_encode($data);



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
            
            <div class="user-header  d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
              <!-- Display user image -->
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

        </div>
        <div class="content">
    <div class="overview">
        <h3>Overview</h3>
        <div class="ovw-con">
        <?php
                    // CHANGE THE PORT IF NEEDED
                    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308); // database connection

                    // Query to get the total quantity of all books
                    $totalQuantityQuery = "SELECT SUM(Quantity) AS total_quantity FROM tbl_books";
                    $totalQuantityResult = mysqli_query($conn, $totalQuantityQuery);

                    // Check if the query was successful and fetch the total quantity
                    if ($totalQuantityResult && mysqli_num_rows($totalQuantityResult) > 0) {
                        $totalQuantityData = mysqli_fetch_assoc($totalQuantityResult);
                        $totalQuantity = $totalQuantityData['total_quantity'];
                        
                        // Display the total quantity of all books
                        echo "<h4>Total  of Books: " . $totalQuantity . "</h4>";
                    } else {
                        echo "No books found";
                    }

                    ?>
            <div class="line"></div>
           
           
           <?php
// Assuming you have established a database connection named $conn
$currentDate = date("Y-m-d");

// Query to count visits for the current date using the Date&Time column
$totalVisitsQuery = "SELECT COUNT(*) AS total_visits FROM tbl_log WHERE DATE(`Date_Time`) = '$currentDate'";
$totalVisitsResult = mysqli_query($conn, $totalVisitsQuery);

// Check if the query was successful and fetch the total visits
if ($totalVisitsResult && mysqli_num_rows($totalVisitsResult) > 0) {
    $totalVisitsData = mysqli_fetch_assoc($totalVisitsResult);
    $totalVisitsCount = $totalVisitsData['total_visits'];
    
    // Display the total visits for the current date
    echo "<h4>Total Visits Today: " . $totalVisitsCount . "</h4>";
} else {
    echo "<h4>0</h4>"; // No visits found for the current date
}
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
// Query to fetch data from tbl_requestbooks where status is 'Pending'
$requestBooksQuery = "SELECT * FROM tbl_requestbooks WHERE tb_status = 'Pending'";
$requestBooksResult = mysqli_query($conn, $requestBooksQuery);

if (!$requestBooksResult) {
    echo "Error fetching request books: " . mysqli_error($conn);
} else {
    // Check if there are any rows returned
    if (mysqli_num_rows($requestBooksResult) > 0) {
        // Fetch and display each row
        while ($row = mysqli_fetch_assoc($requestBooksResult)) {
            echo '<div class="book-details">';
            echo '<h4><strong>' . $row['Book_Title'] . '</strong></h4><br>';
            echo '<p><strong>Authors ID:</strong> ' . $row['Authors_ID'] . '</p>';
            echo '<p><strong>Publisher ID:</strong> ' . $row['Publisher_ID'] . '</p>';
            echo '<p><strong>Year Published:</strong> ' . $row['Year_Published'] . '</p>';
            echo '<p><strong>Quantity:</strong> ' . $row['Quantity'] . '</p>';
            echo '<hr>';
            echo '</div>';
        }
    } else {
        echo "No pending request books found.";
    }
}



// Close connection
mysqli_close($conn);
?>


        </div>
    </div>

    <div class="stats">
    <h3>Statistics</h3>
                <div class="stats-con">
                    <canvas id="myChart" width="500"></canvas>
                </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  
<script>
        // Access PHP-generated JSON data
        const labels = <?php echo $labelsJSON; ?>;
        const data = <?php echo $dataJSON; ?>;

        const ctx = document.getElementById('myChart');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels, // Use labels from PHP
                datasets: [{
                    label: 'Monthly Visits',
                    data: data, // Use data from PHP
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
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