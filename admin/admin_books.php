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
</head>

<body>
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary"><!--sidenav container-->
        <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <h2>Villa<span>Read</span>Hub</h2>
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon" />
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
            <?php if (!empty($userData['image_data'])) : ?>
                <!-- Assuming the image_data is in JPEG format, change the MIME type if needed -->
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else : ?>
                <!-- Change the path to your actual default image -->
                <img src="default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php echo  $firstName . "<br/>" .  $role; ?></span></strong>
        </div>
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

    <div class="container">
        <div class="row">
            <div class="col-md-13">
                <h2 class="mt-4 mb-3">Book Information</h2>
                <div class="form-group">
                    <select id="statusFilter" class="form-select mb-3">
                        <option value="Available" selected>Available</option>
                        <option value="Archived">Archived</option>
                        <option value="Request">Request</option>
                    </select>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>Accession Code</th>
                                <th>Book Title</th>
                                <th>Authors</th>
                                <th>Publisher</th>
                                <th>Section</th>
                                <th>Shelf #</th>
                                <th>Edition</th>
                                <th>Year Published</th>
                                <th>ISBN</th>
                                <th>Bibliography</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="bookTableBody">
                            <!-- Book records will be dynamically loaded here -->
                        </tbody>
                    </table>
                </div>

                <div class="btn-group">
                    <button class="btn btn-primary" id="requestButton">Request List</button>
                    <a href="./staff_bookCatalog.php" class="btn btn-secondary">Catalog</a>
                    <a href="./admin_addBook.php" class="btn btn-success">Add New Book</a><!--button for adding new book-->
                </div>
            </div>


        </div>
    </div>

    <script>
        // Function to fetch book information based on selected status
        function fetchBooksByStatus(status) {
            fetch('fetch_books.php?status=' + status)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('bookTableBody').innerHTML = data;
                });
        }

        // Function to fetch requested books using AJAX
        function fetchRequests() {
            fetch('fetch_book_request.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('bookTableBody').innerHTML = data;
                });
        }

        // Event listener for dropdown change
        document.getElementById('statusFilter').addEventListener('change', function() {
            let selectedStatus = this.value;
            if (selectedStatus === 'Request') {
                fetchRequests();
            } else {
                fetchBooksByStatus(selectedStatus);
            }
        });

        // Function to change status to Archived
        function changeStatus(accessionCode) {
            fetch('change_book_status.php?accessionCode=' + accessionCode, {
                    method: 'POST'
                })
                .then(response => response.text())
                .then(data => {
                    // Refresh the table
                    let currentStatus = document.getElementById('statusFilter').value;
                    if (currentStatus === 'Requested') {
                        fetchRequests();
                    } else {
                        fetchBooksByStatus(currentStatus);
                    }
                });
        }

        // Call fetchBooksByStatus with default status "Available" when the page loads
        window.addEventListener('load', function() {
            fetchBooksByStatus('Available');
        });
    </script>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all archive buttons
            const archiveButtons = document.querySelectorAll('.archive-btn');

            // Add click event listener to each archive button
            archiveButtons.forEach(button => {
                button.addEventListener('click', function(event) {
                    // Show the confirmation dialog
                    if (!confirm("Do you want to archive this book?")) {
                        event.preventDefault(); // Prevent default form submission if not confirmed
                        return; // Exit function early if not confirmed
                    }

                    // If confirmed, allow form submission
                });
            });
        });
    </script>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>
    <script>
        let date = new Date().toLocaleDateString('en-US', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            weekday: 'long',
        });
        document.getElementById("currentDate").innerText = date;

        setInterval(() => {
            let time = new Date().toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: 'numeric',
                second: 'numeric',
                hour12: 'true',
            })
            document.getElementById("currentTime").innerText = time;

        }, 1000)


        let navItems = document.querySelectorAll(".nav-item"); //adding .active class to navitems 
        navItems.forEach(item => {
            item.addEventListener('click', () => {
                document.querySelector('.active')?.classList.remove('active');
                item.classList.add('active');


            })

        })
    </script>
</body>

</html>