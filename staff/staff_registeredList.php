<?php

session_start();

// Initialize a flag to track the validity of the Borrower ID
$isBorrowerIdValid = false;
$errorMessage = "";

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
    <title>List of Registered Borrowers</title>
    <script src="../node_modules/html5-qrcode/html5-qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js" integrity="sha512-k/KAe4Yff9EUdYI5/IAHlwUswqeipP+Cp5qnrsUjTPCgl51La2/JhyyjNciztD7mWNKLSXci48m7cctATKfLlQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="./staff.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png ">
    <style>
        .img-responsive {
            max-width: 20%;
            /* This will make sure the image does not exceed the width of its container */
            height: auto;
            /* This will maintain the aspect ratio of the image */
        }
    </style>

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
                <!-- Assuming the image_data is in JPEG format, change the MIME type if needed -->
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else : ?>
                <!--default image -->
                <img src="../images/default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php echo $userData['First_Name'] . "<br/>" . $_SESSION["role"]; ?></span></strong>
        </div>

        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./staff_dashboard.php" class="nav-link link-body-emphasis "> <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./staff_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./staff_transaction_dash.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transaction</a> </li>
            <li class="nav-item active"> <a href="./staff_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./staff_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <hr>
            <li class="nav-item"> <a href="./staff_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="" data-bs-toggle="modal" data-bs-target="#logOut" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
    </div>
    <div class="board container"><!--board container-->
        <div class="header1">
            <div class="text">
                <div class="back-btn">
                    <a href="./staff_log.php"><i class='bx bx-arrow-back'></i></a>
                </div>
                <div class="title">
                    <h2>Registered List</h2>
                </div>
            </div>
            <div class="searchbar">
                <form action="">
                    <input type="search" id="searchInput" placeholder="Search..." required>
                    <i class='bx bx-search' id="search-icon"></i>
                </form>
            </div>
        </div>
        <div class="books container">
            <table class="table table-hover table-m">
                <thead class="bg-light sticky-top">
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Contact Number</th>
                        <th>Email</th>
                        <th>School/Affiliation</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Database connection
                    $conn_display_all = mysqli_connect("localhost", "root", "root", "db_library_2", 3307);
                    if ($conn_display_all->connect_error) {
                        die("Connection failed: " . $conn_display_all->connect_error);
                    }

                    // SQL query to select all records from tbl_borrower and tbl_log
                    $sql_display_all = "SELECT * FROM tbl_borrower";
                    $result_display_all = $conn_display_all->query($sql_display_all);

                    // Close display connection
                    $conn_display_all->close();

                    if ($result_display_all && $result_display_all->num_rows > 0) {
                        while ($row = $result_display_all->fetch_assoc()) {
                            echo '<tr>';
                            echo "<td>" . $row['Borrower_ID'] . "</td>";
                            echo "<td>" . $row['First_Name'] . " " . $row['Middle_Name'] . " " . $row['Last_Name'] . "</td>";
                            echo "<td>" . $row['Contact_Number'] . "</td>";
                            echo "<td>" . $row['Email'] . "</td>";
                            echo "<td>" . $row['affiliation'] . "</td>";
                            echo "<td><button type='button' class='btn borrower-edit-btn' data-bs-toggle='modal' data-bs-target='#BorrowerModal' data-borrower-id='" . $row['Borrower_ID'] . "'>Edit</button></td>";
                            echo "<input type='hidden' id='borrowerID' name='borrowerID' value='" . $row['Borrower_ID'] . "'>";

                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No records found.</td></tr>";
                    }
                    ?>
                </tbody>

            </table>
        </div>
        <div class="btn-con">
            <a href="staff_registerUser.php" class="btn">Register Borrower</a>
        </div>
    </div>

    <div id="BorrowerModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Borrower Details</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- QR code image here-->
                </div>
                <!-- Modal -->
                <div class="modal fade" id="BorrowerModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Edit Borrower Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Content will be dynamically populated here -->
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <!-- Button with data attribute for borrower ID -->
                                <button type="button" class="btn btn-primary" onclick="saveData()" data-borrower-id="<?php echo $row['Borrower_ID']; ?>">Save changes</button>

                                <!-- Hidden input field to store borrower ID -->
                                <input type="hidden" id="borrowerID" name="borrowerID" value="<?php echo $row['Borrower_ID']; ?>">


                            </div>
                        </div>
                    </div>
                </div>



                <div class="modal-footer d-flex flex-row justify-content-center">
                    <a href="#"><button type="button" class="btn" onclick="sendEmail()">Send to Email</button></a>
                    <a href="queries/print_details.php"><button id="print" type="button" class="btn">Print</button></a>
                    <a href="#"><button id="saveButton" type="button" class="btn">Save</button></a>


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



        function BorrowerModal(id) {
            console.log(id);
            $('#BorrowerModal').modal({
                keyboard: true,
                backdrop: "static"
            });
        }


        document.addEventListener("DOMContentLoaded", function() {
            const borrowerEditButtons = document.querySelectorAll(".borrower-edit-btn");
            borrowerEditButtons.forEach(button => {
                button.addEventListener("click", function() {
                    // Get the Borrower_ID from the data attribute
                    const borrowerID = this.getAttribute("data-borrower-id");


                    // Send AJAX request to update.php to fetch and populate data
                    fetch('queries/displaymodal.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'borrowerID=' + encodeURIComponent(borrowerID),
                        })
                        .then(response => response.text())
                        .then(data => {
                            // Update modal content with fetched data
                            document.querySelector('.modal-body').innerHTML = data;
                            // Show the modal
                            $('#BorrowerModal').modal({
                                keyboard: true,
                                backdrop: "static"
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                });
            });
        });

        let globalBorrowerID;

        document.addEventListener("DOMContentLoaded", function() {
            const borrowerEditButtons = document.querySelectorAll(".borrower-edit-btn");
            borrowerEditButtons.forEach(button => {
                button.addEventListener("click", function() {
                    // Get the Borrower_ID from the data attribute
                      globalBorrowerID = this.getAttribute("data-borrower-id");
                      console.log("Borrower_ID To send:", globalBorrowerID);
                    // You can now use borrowerID as needed, such as sending it to the server via AJAX
                });
            });
        });










        // Add event listener to the Save button
        document.addEventListener("DOMContentLoaded", function() {
            const saveButton = document.getElementById("saveButton");
            saveButton.addEventListener("click", function() {
                saveData(); // Call the saveData() function when the button is clicked
            });
        });

        // Function to handle the Save button click event
        function saveData(borrowerID) {
            // Retrieve the updated values from the form fields
            const firstName = document.getElementById("firstName").value;
            const middleName = document.getElementById("middleName").value;
            const lastName = document.getElementById("lastName").value;
            const contactNumber = document.getElementById("contactNumber").value;
            const email = document.getElementById("email").value;
            const affiliation = document.getElementById("affiliation").value;

            // Debugging: Log the retrieved values
            console.log("Updated data:", borrowerID);
            console.log("First Name:", firstName);
            console.log("Middle Name:", middleName);
            console.log("Last Name:", lastName);
            console.log("Contact Number:", contactNumber);
            console.log("Email:", email);
            console.log("Affiliation:", affiliation);
            console.log("Borrower ID:", borrowerID); // Log the borrower ID

            // Send an AJAX request to update.php
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "queries/update.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        // Request was successful
                        console.log("Response from server:", xhr.responseText);
                        // You can perform further actions here if needed
                        alert("Record updated successfully."); // Display alert dialog
                    } else {
                        // Error handling
                        console.error("Error:", xhr.statusText);
                    }
                }
            };
            // Send the updated values and borrower ID as data
            xhr.send("borrowerID=" + encodeURIComponent(borrowerID) + "&firstName=" + encodeURIComponent(firstName) + "&middleName=" + encodeURIComponent(middleName) + "&lastName=" + encodeURIComponent(lastName) + "&contactNumber=" + encodeURIComponent(contactNumber) + "&email=" + encodeURIComponent(email) + "&affiliation=" + encodeURIComponent(affiliation));
        }

        function sendEmail() {
    // Retrieve the borrowerID from the global variable
    const borrowerID = globalBorrowerID;

    // Send an AJAX request to a PHP script with the borrowerID directly in the URL
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "send_email.php?borrower_id=" + encodeURIComponent(borrowerID), true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                // Request was successful
                console.log("Email sent successfully. Borrower ID: " + borrowerID);
                // You can perform further actions here if needed
            } else {
                // Error handling
                console.error("Error:", xhr.statusText);
            }
        }
    };
    // Send an empty body as the data since the borrowerID is included in the URL
    xhr.send();
}



        function error(err) {
            console.error(err);
            // Prints any errors to the console
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