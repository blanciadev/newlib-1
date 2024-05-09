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
    <title>Catalog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="./staff.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png ">

   
</head>
<body>
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary"><!--sidenav container-->
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <h2>Villa<span>Read</span>Hub</h2>
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon" />
        </a><!--header container-->
        <div class="user-header  d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
            <!-- Display user image -->
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
            }
            ?>
            <?php if (!empty($userData['image_data'])) : ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else : ?>
                <img src="../images/default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php echo $userData['First_Name'] . "<br/>" . $_SESSION["role"]; ?></span></strong>
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./staff_dashboard.php" class="nav-link link-body-emphasis "> <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item active"> <a href="./staff_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./staff_transaction_dash.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transaction</a> </li>
            <li class="nav-item"> <a href="./staff_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./staff_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <hr>
            <li class="nav-item"> <a href="./staff_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="" data-bs-toggle="modal" data-bs-target="#logOut" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
    </div>
    <div class="board1 container">
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
                    <input type="search" id="searchInput" placeholder="Search..." required>
                    <i class='bx bx-search' id="search-icon"></i>
                </form>
            </div>
        </div>
        <div class="bookCatalog">
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
            <hr>
            <div class="catalogCon container">
                <br>
                <div id="shelfAccordion" class="accordion"><!-- Accordion items will be generated here --></div>
                <div id="bookList"><!-- Fetched books will be displayed here --></div>
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
                <div class="modal-body"> Do you want to log out?</div>
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
        // Wait for the document to load
        document.addEventListener("DOMContentLoaded", function() {
            // Get all accordion buttons
            var accordionButtons = document.querySelectorAll('.accordion-button');

            // Add click event listener to each accordion button
            accordionButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    // Toggle the 'collapsed' class on the button
                    this.classList.toggle('collapsed');

                    // Get the target collapse element
                    var targetId = this.getAttribute('data-bs-target');
                    var targetCollapse = document.querySelector(targetId);

                    // Toggle the 'show' class on the target collapse element
                    targetCollapse.classList.toggle('show');
                });
            });
        });

        var sectionCode = '';
        var selectedShelf = '';
                    
        $(document).ready(function() {
            // Simulate click on Filipiniana button
            $('.btnOption[data-target="FIL"]').addClass('active'); // Add 'active' class to the Filipiniana button
            fetchShelfs('FIL'); // Fetch shelf categories for Filipiniana section initially

            // Event listener for accordion button click
            $('#shelfAccordion').on('click', '.accordion-button', function() {
                var selectedShelf = $(this).text(); // Get the text of the clicked accordion button
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

                fetchShelfs(sectionCode); // Fetch shelf categories for the selected section code
            });
        });

        function fetchShelfs(sectionCode) {
            $.ajax({
                url: 'queries/fetch_shelfs.php',
                method: 'POST',
                data: {
                    sectionCode: sectionCode
                },
                dataType: 'html',
                    success: function(response) {
                        $('#shelfAccordion').html(response);
                            console.log(selectedShelf);
                    
                            // Initialize Bootstrap accordion after adding the content
                            $('#shelfAccordion').find('.accordion-collapse').collapse('hide');
                            $('#shelfAccordion').find('.accordion-button').removeClass('collapsed');
                    },
                    error: function(xhr, status, error) {
                            console.error('Error fetching shelf categories:', error);
                    }
            });
        }

        function fetchBooks(selectedShelf) {
            // AJAX request to fetch books based on selected shelf
            $.ajax({
                url: 'queries/fetch_books.php',
                method: 'POST',
                data: {
                    selectedShelf: selectedShelf                        
                },
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
                url: 'queries/fetch_auth.php', 
                method: 'POST',
                data: {
                    target: target
                }, // Send the target as data
                dataType: 'html',
                    success: function(response) {
                        console.log(target + " Response:", response); 
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