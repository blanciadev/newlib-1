<?php
    include '../auth.php';
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
        echo '<script>console.log("Quantity update of 0 Record to Unavailable ");</script>';
    }

    $sqlUpdate = "UPDATE tbl_books
            SET tb_status = 'Available'
            WHERE Quantity > 0 AND tb_status != 'Archived' AND tb_status = 'Unavailable'";

    if ($conn->query($sqlUpdate) === TRUE) {
        echo '<script>console.log("Update to status to Avaialble");</script>';
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
    <link href="./staff.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png ">
</head>
<body>
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" ><!--sidenav container-->
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
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
                }
            ?>
            <?php if (!empty($userData['image_data'])): ?> 
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else: ?> 
                <img src="../images/default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php echo $userData['First_Name'] . "<br/>" . $_SESSION["role"]; ?></span></strong>
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./staff_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item active"> <a href="./staff_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./staff_transaction_dash.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transaction</a> </li>
            <li class="nav-item"> <a href="./staff_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./staff_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <hr>
            <li class="nav-item"> <a href="./staff_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="" data-bs-toggle="modal" data-bs-target="#logOut" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
    </div>
    <div class="board container"><!--board container-->
        <div class="header1">
            <div class="text">
                <div class="title">
                    <h2>Books</h2>
                </div>
            </div>
            <div class="searchbar">
                <form action="">
                    <input type="search" id="searchInput"  placeholder="Search..." required>
                    <i class='bx bx-search' id="search-icon"></i>
                </form>
            </div>
        </div>
        <div class="books container">
            <table class="table table-hover table-sm">
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
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        // Establish database connection
                        $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // Number of records per page
                        $recordsPerPage = 10;

                        // Determine current page number
                        $page = isset($_GET['page']) ? $_GET['page'] : 1;

                        // Calculate the offset
                        $offset = ($page - 1) * $recordsPerPage;

                        // SQL query to count total records
                        $countSql = "SELECT COUNT(*) AS total FROM tbl_books";
                        $countResult = $conn->query($countSql);
                        $totalRecords = $countResult->fetch_assoc()['total'];

                        // SQL query with pagination
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
                                WHERE tb_status = 'Available'
                                LIMIT $offset, $recordsPerPage";

                        $result = $conn->query($sql);

                        while ($row = $result->fetch_assoc()) {
                            echo "<tr><td>" . $row["Accession_Code"] . "</td>
                                <td>" . $row["Book_Title"] . "</td>
                                <td>" . $row["Authors_Name"] . "</td>
                                <td>" . $row["Publisher_Name"] . "</td>
                                <td>" . $row["Section_Code"] . "</td>
                                <td>" . $row["shelf"] . "</td>
                                <td>" . $row["tb_edition"] . "</td>
                                <td>" . $row["Year_Published"] . "</td>
                                <td>" . $row["Quantity"] . "</td>
                                <td>" . $row["Price"] . "</td>
                                <td>" . $row["tb_status"] . "</td></tr>";
                        }

                        // Close connection
                        $conn->close();
                    ?>
                </tbody>
            </table>
            
            <ul class="pagination justify-content-center"><!-- Pagination links -->
                <?php
                    // Calculate total number of pages
                    $totalPages = ceil($totalRecords / $recordsPerPage);

                    // Previous page link
                    if ($page > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?page=' . ($page - 1) . '">Previous</a></li>';
                    }

                    // Page numbers
                    for ($i = 1; $i <= $totalPages; $i++) {
                        echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                    }

                    // Next page link
                    if ($page < $totalPages) {
                        echo '<li class="page-item"><a class="page-link" href="?page=' . ($page + 1) . '">Next</a></li>';
                    }
                ?>
            </ul>
        </div>
        <div class="btn-con">
            <a href="./staff_request_list.php" class="btn">Request List</a>
            <a href="./staff_bookCatalog.php" class="btn">Catalog</a>
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
    <script> 
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


    