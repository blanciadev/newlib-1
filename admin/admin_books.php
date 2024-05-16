<?php
session_start();
// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql ="UPDATE tbl_books
SET tb_status = 'Unavailable'
WHERE Quantity = 0;
";

if ($conn->query($sql) === TRUE) {
    echo '<script>console.log("Quantity update to 0 ");</script>';
}

$sqlUpdate = "UPDATE tbl_books
        SET tb_status = 'Available'
        WHERE Quantity > 0 AND tb_status != 'Archived' AND tb_status = 'Unavailable'";

if ($conn->query($sqlUpdate) === TRUE) {
    echo '<script>console.log("Update to status to Avaialble");</script>';
}

// Check if the accession code is set in the POST request
if (isset($_POST['archive_book']) && isset($_POST['accessionCode'])) {
    // Handle archiving the book
  

    // Sanitize the accession code to prevent SQL injection
    $accessionCode = mysqli_real_escape_string($conn, $_POST['accessionCode']);

    // Update the status to 'Archived' for the specified accession code
    $sql = "UPDATE tbl_books SET tb_status = 'Archived' WHERE Accession_Code = '$accessionCode'";
    if ($conn->query($sql) === TRUE) {
        echo "Status updated successfully";
    } else {
        echo "Error updating status: " . $conn->error;
    }

    $conn->close();
}



?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books</title>
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
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else : ?>
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
    <div class="board container-fluid"><!--board container-->
        <div class="header1">
            <div class="text">
                <div class="title">
                    <h2>Books</h2>
                </div>
                <div class="form-group">
                    <form id="statusFilterForm" method="GET" action="admin_books.php">
                        <select id="statusFilter" name="status" class="form-select mb-3">
                            <option value="Available" <?php echo $status == 'Available' ? 'selected' : ''; ?>>Available</option>
                            <option value="Archived" <?php echo $status == 'Archived' ? 'selected' : ''; ?>>Archived</option>
                            <option value="Request" <?php echo $status == 'Request' ? 'selected' : ''; ?>>Request</option>
                        </select>
                    </form>
                </div>

            </div>
            <div class="searchbar">
                <form action="">
                    <i class='bx bx-search' id="search-icon"></i>
                    <input type="search" id="searchInput" placeholder="Search..." required>
                    
                </form>
            </div><br>
        </div>
        <div class="table-responsive" id="bookTable">
           
           <?php

$recordsPerPage = 4;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$sqlCount = "SELECT COUNT(*) AS totalRecords FROM tbl_books WHERE tb_status = '$status'";
$resultCount = $conn->query($sqlCount);
$totalRecords = $resultCount->fetch_assoc()['totalRecords'];
$totalPages = ceil($totalRecords / $recordsPerPage);
$offset = ($page - 1) * $recordsPerPage;
$tableHTML = '';

if ($status === 'Request') {
    $sql = "SELECT tbl_requestbooks.* FROM tbl_requestbooks ORDER BY CASE WHEN 
    tb_status = 'Pending' THEN 0 ELSE 1 END, Request_ID LIMIT $offset, $recordsPerPage";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $tableHTML = '<table class="table table-hover">
            <thead class="bg-light sticky-top">
                <tr>
                    <th style="width: 10%;">Accession Code</th><th style="width: 15%;">Book Title</th>
                    <th style="width: 10%;">Authors</th> <th style="width: 10%;">Publisher</th>
                    <th style="width: 10%;">Section</th><th style="width: 5%;">Shelf #</th>
                    <th style="width: 5%;">Edition</th> <th style="width: 5%;">Year Published</th><th style="width: 10%;">ISBN</th>
                    <th style="width: 10%;">Bibliography</th> <th style="width: 5%;">Quantity</th>
                    <th style="width: 5%;">Price</th><th style="width: 5%;">Status</th><th style="width: 5%;">Action</th>
                </tr>
            </thead>
            <tbody>';
        while ($row = $result->fetch_assoc()) {
            $tableHTML .= '<tr>
                <td></td>
                <td>' . $row["Book_Title"] . '</td>
                <td>' . $row["Authors_Name"] . '</td>
                <td>' . $row["Publisher_Name"] . '</td>
                <td></td>
                <td></td>
                <td>' . $row["tb_edition"] . '</td>
                <td>' . $row["Year_Published"] . '</td>
                <td></td>
                <td></td>
                <td>' . $row["Quantity"] . '</td>
                <td>' . $row["price"] . '</td>
                <td>' . $row["tb_status"] . '</td>
                <td>';
                if ($row["tb_status"] === "Approved") {
                    $tableHTML .= "<button type='button' class='btn btn-secondary' disabled>Process</button>";
                } else {
                    $tableHTML .= "<a href='process_data_book.php?id=" . $row["Request_ID"] . "' class='btn btn-primary'>Process</a>";
                }

                $tableHTML .= '</td>
                            </tr>';
            }

            $tableHTML .= "</tbody></table>";
            
        } else {
            $tableHTML = "No books found.";
        }

    
    } else {
                $sql = "SELECT
        tbl_books.Accession_Code, 
        tbl_books.Book_Title, 
        tbl_books.Authors_ID, 
        tbl_books.Publisher_Name, 
        tbl_books.Section_Code, 
        tbl_books.shelf, 
        tbl_books.tb_edition, 
        tbl_books.Year_Published, 
        tbl_books.ISBN, 
        tbl_books.Bibliography, 
        tbl_books.Quantity, 
        tbl_books.tb_status, 
        tbl_books.Price, 
        tbl_section.Section_uid, 
        tbl_section.Section_Name, 
        tbl_section.Section_Code, 
        tbl_authors.Authors_Name
    FROM
        tbl_books
    INNER JOIN
        tbl_section ON tbl_books.Section_Code = tbl_section.Section_uid
    INNER JOIN
        tbl_authors ON tbl_books.Authors_ID = tbl_authors.Authors_ID
    WHERE
        tbl_books.tb_status = '$status'
    LIMIT $offset, $recordsPerPage";
                $result = $conn->query($sql);
                echo '<table class="table table-hover">
        <thead class="bg-light sticky-top">
            <tr>
                <th style="width: 10%;">Accession Code</th><th style="width: 15%;">Book Title</th>
                <th style="width: 10%;">Authors</th><th style="width: 10%;">Publisher</th>
                <th style="width: 10%;">Section</th> <th style="width: 5%;">Shelf #</th>
                <th style="width: 5%;">Edition</th> <th style="width: 5%;">Year Published</th>
                <th style="width: 10%;">ISBN</th><th style="width: 10%;">Bibliography</th>
                <th style="width: 5%;">Quantity</th> <th style="width: 5%;">Price</th>
                <th style="width: 5%;">Status</th> <th style="width: 5%;">Action</th>
        </tr>
    </thead>
    <tbody>';

                while ($row = $result->fetch_assoc()) {
                    $tableHTML .= '<tr>
        <td>' . $row['Accession_Code'] . '</td>
        <td>' . $row['Book_Title'] . '</td>
        <td>' . $row['Authors_Name'] . '</td>
        <td>' . $row['Publisher_Name'] . '</td>
        <td>' . $row['Section_Name'] . '</td>
        <td>' . $row['shelf'] . '</td>
        <td>' . $row['tb_edition'] . '</td>
        <td>' . $row['Year_Published'] . '</td>
        <td>' . $row['ISBN'] . '</td>
        <td>' . $row['Bibliography'] . '</td>
        <td>' . $row['Quantity'] . '</td>
        <td>' . $row['Price'] . '</td>
        <td>' . $row['tb_status'] . '</td>
        <td>';

                    if ($row['tb_status'] == 'Available') {
                        $tableHTML .= '<button type="button" class="btn btn-primary btn-sm archive-btn" data-bs-toggle="modal" data-bs-target="#archiveModal" data-accession-code="' . $row['Accession_Code'] . '">Archive</button>';
                    }
                    $tableHTML .= '</td>
                    </tr>';
                }
            }

            $tableHTML .= '</tbody>
    </table>';

            echo $tableHTML;

                    // Add pagination links
                    echo '<div class="d-flex justify-content-center">
            <ul class="pagination">';
                    for ($i = 1; $i <= $totalPages; $i++) {
                        echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '"><a class="page-link" href="?status=' . $status . '&page=' . $i . '">' . $i . '</a></li>';
                    }
                    echo '</ul>
        </div>';
            ?>
        </div>
        

        <div class="btn-con">
            <a href="./admin_bookCatalog.php" class="btn btn-secondary">Catalog</a>
            <a href="./admin_addBook.php" class="btn btn-success">Add New Book</a>
        </div>
    </div> 



    <!-- Bootstrap Bundle with Popper.js (for Bootstrap 5) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


    <div id="archiveModal" class="modal fade" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="archiveModalLabel">Archive Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="modal-body">
                        <p><strong>Book Title:</strong><input type="text" id="bookTitle" class="form-control" readonly><strong>Author</strong> <input type="text" id="authors" class="form-control" readonly></p>
                        <p><strong>Publisher:</strong> <input type="text" id="publisher" class="form-control" readonly></p>
                        <p><strong>Section:</strong> <input type="text" id="section" class="form-control" readonly></p>
                        <p><strong>Shelf:</strong> <input type="text" id="shelf" class="form-control" readonly></p>
                        <p><strong>Edition:</strong> <input type="text" id="edition" class="form-control" readonly></p>
                        <p><strong>Year Published:</strong> <input type="text" id="yearPublished" class="form-control" readonly></p>
                        <p><strong>ISBN:</strong> <input type="text" id="isbn" class="form-control" readonly></p>
                        <p><strong>Bibliography:</strong> <input type="text" id="bibliography" class="form-control" readonly></p>

                        <p><strong>Quantity:</strong> <input type="text" id="quantity" class="form-control" readonly></p>
                        <p><strong>Price:</strong> <input type="text" id="price" class="form-control" readonly></p>
                        <p><strong>Status:</strong> <input type="text" id="status" class="form-control" readonly></p>

                    </div>
                </div>
                <div class="modal-footer">
                    <form id="archiveForm" method="POST" action="">

                        <p><strong>Updated Quantity:</strong> <input type="text" id="qty" placeholder="Update Quantity" oninput="updateHiddenQuantity()"></p>

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                        <input type="hidden" id="hiddenQuantity" name="quantity">
                        <input type="hidden" id="archiveAccessionCode" name="accessionCode">
                        <input type="hidden" id="action" name="action" value="">

                        <br>
                        <button type="button" class="btn btn-primary" onclick="saveChanges()">Save Changes</button>
                        <button type="button" class="btn btn-primary" onclick="setActionAndSubmit('archive')">Archive</button>
                    </form>




                </div>
            </div>
        </div>
    </div>


    </div>


    <script>
        function setAction(action) {
            document.getElementById('action').value = action;
            console.log('Action set to:', action);
        }


        // Function to update hidden input with quantity value
        function updateHiddenQuantity() {
            const quantityInput = document.getElementById('qty');
            const hiddenQuantityInput = document.getElementById('hiddenQuantity');
            hiddenQuantityInput.value = quantityInput.value;
            console.log('Hidden quantity updated to:', hiddenQuantityInput.value);
        }

        // Function to handle Save Changes button click
        function saveChanges() {
            // Set the action to save_changes
            setAction('save_changes');
            // Update hidden input with quantity value
            updateHiddenQuantity();
            // Submit the form
            document.getElementById('archiveForm').submit();
        }

        // Function to handle Archive button click and submit the form
        function setActionAndSubmit(action) {
            setAction(action);
            document.getElementById('archiveForm').submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const quantityInput = document.getElementById('quantity');

            // Add event listener for input on the quantity field
            quantityInput.addEventListener('input', function() {
                // Remove non-numeric characters from input
                this.value = this.value.replace(/\D/g, '');
                console.log('Quantity input value:', this.value);
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const archiveButtons = document.querySelectorAll('.archive-btn');

            archiveButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const accessionCode = this.getAttribute('data-accession-code');
                    document.getElementById('archiveAccessionCode').value = accessionCode;
                    fetchBookDetails(accessionCode);
                });
            });

            // Function to fetch book details and populate modal
            function fetchBookDetails(accessionCode) {
                fetch('queries/fetch_book_request.php?accessionCode=' + accessionCode)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Populate modal fields with retrieved data
                            document.getElementById('bookTitle').value = data.data.Book_Title;
                            document.getElementById('authors').value = data.data.Authors_Name;
                            document.getElementById('publisher').value = data.data.Publisher_Name;
                            document.getElementById('section').value = data.data.Section_Name;
                            document.getElementById('shelf').value = data.data.shelf;
                            document.getElementById('edition').value = data.data.tb_edition;
                            document.getElementById('yearPublished').value = data.data.Year_Published;
                            document.getElementById('isbn').value = data.data.ISBN;
                            document.getElementById('bibliography').value = data.data.Bibliography;
                            document.getElementById('quantity').value = data.data.Quantity;
                            document.getElementById('price').value = data.data.Price;
                            document.getElementById('status').value = data.data.tb_status;

                            // Show the modal
                            const archiveModal = new bootstrap.Modal(document.getElementById('archiveModal'));
                            archiveModal.show();
                        } else {
                            // Display error message if book not found
                            alert(data.message);
                        }
                    })
                    .catch(error => console.error('Error fetching book details:', error));
            }
        });


        // Add event listener to the select element for both functionalities
        document.getElementById('statusFilter').addEventListener('change', function() {
            var status = this.value; // Get the selected value
            // Show loading spinner

            // Update form action with the selected status
            document.getElementById('statusFilterForm').action = 'admin_books.php?status=' + encodeURIComponent(status);
            // Submit the form
            document.getElementById('statusFilterForm').submit();

            // Call function to update books
            updateBooks(status);
        });

        // Function to update books using AJAX
        function updateBooks(status) {
            // Send AJAX request to update books
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'admin_books.php?status=' + encodeURIComponent(status), true);
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 400) {
                    // Success
                    // Update the books display with the response from the server
                    var tableResponsive = document.querySelector('.table-responsive');
                    if (tableResponsive) {
                        tableResponsive.innerHTML = xhr.responseText;
                    } else {
                        console.error('.table-responsive element not found');
                    }
                } else {
                    // Error
                    console.error('Request failed');
                }
                // Hide loading spinner after request completes

            };
            xhr.onerror = function() {
                // Connection error
                console.error('Connection error');
                // Hide loading spinner on error

            };
            xhr.send();
        }
    </script>




</body>

</html>