<?php
session_start();
// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
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

    <style>
        /* CSS for the shelfDropdown */
/* CSS for the shelfDropdown */
#shelfDropdown {
  display: inline-block;
  position: relative;
  cursor: pointer;
  background-color: #fff;
  border: 1px solid #ced4da;
  border-radius: 4px;
 
  padding: 6px 12px;
}

/* CSS for the dropdown arrow */
#shelfDropdown::after {
  content: '\25BC'; /* Unicode character for down arrow */
  position: absolute;
  top: 55%;
  right: 13px;
  transform: translateY(-50%);
}

/* CSS for the dropdown menu */
.dropdown-menu {
  display: none;
  position: absolute;
  z-index: 1;
  background-color: #fff;

  padding: 5px 0;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
}

/* CSS for the dropdown menu items */
.dropdown-menu a {
  display: block;
  padding: 5px 15px;
  color: #333;
  text-decoration: none;
  transition: background-color 0.3s ease;
}

/* Hover effect for dropdown menu items */
.dropdown-menu a:hover {
  background-color: #f8f9fa;
}

/* Show dropdown menu on hover */
#shelfDropdown:hover .dropdown-menu {
  display: block;
}


    </style>
</head>

<body>
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" ><!--sidenav container-->
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <h2>Villa<span>Read</span>Hub</h2> 
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon"/>
        </a><!--header container--> 
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
            }
            ?>
            <?php if (!empty($userData['image_data'])): ?>
                <!-- Assuming the image_data is in JPEG format, change the MIME type if needed -->
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else: ?>
                <!--default image -->
                <img src="../images/default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php $fname = $userData["First_Name"]; $lname = $userData["Last_Name"]; $userName = $fname." ". $lname;  echo $userName . "<br/>" . $_SESSION["role"]; ?></span></strong></div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item "> <a href="./admin_dashboard.php" class="nav-link link-body-emphasis "> <i class='bx bxs-home'></i>Dashboard </a> </li>
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
    <div class="board1 container"><!--board container-->
    <div class="header1">
            <div class="text">
                    <div class="back-btn">
                        <a href="./staff_books.php"><i class='bx bx-arrow-back'></i></a>
                    </div>
                <div class="title">
                    <h2>Catalog</h2>
                </div>
            </div>
            <div class="searchbar">
                <form action="">
                    <input type="search" id="searchInput"  placeholder="Search..." required>
                    <i class='bx bx-search' id="search-icon"></i>
                </form>
            </div>
    </div>
    <div class="bookCatalog">
        <div class="catalogOptions"> <!-- kurt di ko kakuha ani ay, dapat pag tuplok ani mag make syag list of categories nga example Fictional gi click, Fictional lang na category(dropdown ni sya) and then pag click niya na dadto ang list sa books  naka based sa shelf number ni sya but naa syay order ako lang isend sa gc -->
            <div class="d-flex w-100">
            <div class="catalogOptions">
            <form id="sectionForm">
    <div class="d-flex w-100">
    <button type="button" class="btn btn-primary btnOption" data-target="FIL" value="FIL">Filipiniana</button>

<button type="button" class="btn btn-primary btnOption" data-target="REF" value="REF">Reference</button>

<button type="button" class="btn btn-primary btnOption" data-target="CIR" value="CIR">Circulation</button>

<button type="button" class="btn btn-primary btnOption" data-target="FIC" value="FIC">Fiction</button>

<button type="button" class="btn btn-primary btnOption" data-target="ASRTD" value="ASRTD">Assorted Books</button>

<button type="button" class="btn btn-primary btnOption" data-target="Authors" value="Authors">Authors</button>

<button type="button" class="btn btn-primary btnOption" data-target="Publishers" value="Publishers">Publishers</button>

    </div>
</form>

</div>

        </div>
        <hr>
        <div class="catalogCon container">
            <br>
            <div id="shelfDropdown">DropDown</div>
          
            <div class="bookDisplay container">
   
    <div id="bookList">
        <!-- Fetched books will be displayed here -->
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>



    <script>
  var sectionCode = ''; 
  var selectedShelf = '';
  $(document).ready(function() {
    // Simulate click on Filipiniana button
    $('.btnOption[data-target="FIL"]').addClass('active'); // Add 'active' class to the Filipiniana button
    fetchShelfs('FIL'); // Fetch shelf categories for Filipiniana section initially

    // Event listener for dropdown change
    $('#shelfDropdown').on('change', '#shelf', function() {
        var selectedShelf = $(this).val();
        console.log("Selected shelf:", selectedShelf);

        // AJAX request to fetch books based on selected shelf
        fetchBooks(selectedShelf);
    });

    // Event listener for button click
    $('.btnOption').click(function() {
        // Remove 'active' class from all buttons
        $('.btnOption').removeClass('active');
        
        // Add 'active' class to the clicked button
        $(this).addClass('active');
        
        var sectionCode = $(this).data('target');
        console.log("Selected section code:", sectionCode); // Log the sectionCode
        
        // Log the value of the clicked button
        var buttonValue = $(this).attr('value');
        console.log("Clicked button value:", buttonValue);
        
        fetchShelfs(sectionCode); // Fetch shelf categories for the selected section code
    });
});

function fetchShelfs(sectionCode) {
    // AJAX request to fetch shelf categories
    $.ajax({
        url: 'fetch_shelfs.php',
        method: 'POST',
        data: { sectionCode: sectionCode },
        dataType: 'html',
        success: function(response) {
            console.log("Response:", response); // Log the response
            // Update dropdown menu with fetched shelf categories
            $('#shelfDropdown').html(response);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching shelf categories:', error);
        }
    });
}

function fetchBooks(selectedShelf) {
    // AJAX request to fetch books based on selected shelf
    $.ajax({
        url: 'fetch_bookcatalog.php',
        method: 'POST',
        data: { selectedShelf: selectedShelf },
        dataType: 'html',
        success: function(response) {
            console.log("Books Response:", response); // Log the response
            // Display fetched books in the bookList div
            $('#bookList').html(response);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching books:', error);
        }
    });
}

// Event listener for Authors button click
$('button[data-target="Authors"]').click(function() {
    var target = $(this).data('target');
    fetchData(target);
});

// Event listener for Publishers button click
$('button[data-target="Publishers"]').click(function() {
    var target = $(this).data('target');
    fetchData(target);
});

function fetchData(target) {
    // AJAX request to fetch data based on the target
    $.ajax({
        url: 'fetch_auth.php', // Replace 'fetch_data.php' with the appropriate server-side script
        method: 'POST',
        data: { target: target }, // Send the target as data
        dataType: 'html',
        success: function(response) {
    console.log(target + " Response:", response); // Log the response for Authors or Publishers
    // Append the fetched data to the bookList div
    $('#bookList').html(response);
},

        error: function(xhr, status, error) {
            console.error('Error fetching ' + target + ':', error);
        }
    });
}


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


    </script>
</body>
</html>