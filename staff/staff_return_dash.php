<?php

session_start();

// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

// Database connection
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307); 
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Borrowers</title>
    <script src="../node_modules/html5-qrcode/html5-qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js" integrity="sha512-k/KAe4Yff9EUdYI5/IAHlwUswqeipP+Cp5qnrsUjTPCgl51La2/JhyyjNciztD7mWNKLSXci48m7cctATKfLlQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <style>
    main {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    #reader {
        width: 600px;
    }
    #result {
        text-align: center;
        font-size: 1.5rem;
    }
    table{
        padding: 10px;
    }
</style>

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
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon"/>
        </a><!--header container--> 
        <div class="user-header  d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
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
            <li class="nav-item "> <a href="./staff_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item active"> <a href="./staff_transaction_dash.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transaction</a> </li>
             <li class="nav-item"> <a href="./staff_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
             <li class="nav-item"> <a href="./staff_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>  <hr>
            <li class="nav-item"> <a href="./staff_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="" data-bs-toggle="modal" data-bs-target="#logOut" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul> 
    </div>
    <div class="board container"><!--board container-->
        <div class="header1">
                <div class="text">
                    <div class="back-btn">
                        <a href="./staff_transaction_dash.php"><i class='bx bx-arrow-back'></i></a>
                    </div>
                    <div class="title">
                        <h2>List of Borrowers</h2>
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
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Borrower's Name</th>
                        <th>Status</th>
                        <th>Contact Number</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $conn =  mysqli_connect("localhost","root","root","db_library_2", 3307); 
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // SQL query
                        $sql = "SELECT
                        MIN(b.User_ID) AS User_ID,
                        MIN(b.Accession_Code) AS Accession_Code,
                        MIN(bk.Book_Title) AS Book_Title,
                        MIN(bd.Quantity) AS Quantity,
                        MIN(b.Date_Borrowed) AS Date_Borrowed,
                        MIN(b.Due_Date) AS Due_Date,
                        MIN(bd.tb_status) AS tb_status,
                        MIN(bd.Borrower_ID) AS Borrower_ID,
                        MIN(br.First_Name) AS First_Name,
                        MIN(br.Middle_Name) AS Middle_Name,
                        MIN(br.Last_Name) AS Last_Name
                    FROM
                        tbl_borrowdetails AS bd
                        INNER JOIN tbl_borrow AS b ON bd.Borrower_ID = b.Borrower_ID
                        INNER JOIN tbl_books AS bk ON b.Accession_Code = bk.Accession_Code
                        INNER JOIN tbl_borrower AS br ON b.Borrower_ID = br.Borrower_ID
                    GROUP BY
                        bd.Borrower_ID;
                    ";

                        $result = $conn->query($sql);

                        // Output data of each row
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["Borrower_ID"] . "</td>";
                            echo "<td>" . $row["First_Name"] ." ". $row["Middle_Name"] ." ". $row["Last_Name"] . "</td>";
                            echo "<td>" . $row["tb_status"] . "</td>";
                            echo "<td> </td>";// insert borrower contact number please

                            echo "<td>";
                            // Conditionally render the button based on the status
                            if ($row["tb_status"] === 'Pending') {
                                echo "<button type='button' class='btn btn-primary btn-sm update-btn' onclick='updateAndSetSession(" . $row["Borrower_ID"] . ")'>View Books</button>";
                            } else {
                                echo "<button type='button' class='btn btn-secondary btn-sm' disabled>All Books Returned</button>";
                            }
                            echo "<div class='update-message'></div>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody></table>";
                        // Close connection
                        $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
        <button class="btn btn-success showToast">Show Toast</button>
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
    
    <div class="toastNotif" class="hide">
        <div class="toast-content">
            <i class='bx bx-check check'></i>

            <div class="message">
                <span class="text text-1">Success</span><!-- this message can be changed to "Success" and "Error"-->
                <span class="text text-2"></span> <!-- specify based on the if-else statements -->
            </div>
        </div>
        <i class='bx bx-x close'></i>
        <div class="progress"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>
    <script> 
        //Toast Notification 
        const btn = document.querySelector(".showToast"),
            toast = document.querySelector(".toastNotif"),
            close = document.querySelector(".close"),
            progress = document.querySelector(".progress");

        btn.addEventListener("click", () => { // showing toast
            console.log("showing toast")
            toast.classList.add("showing");
            progress.classList.add("showing");
            setTimeout(() => {
                toast.classList.remove("showing");
                progress.classList.remove("showing");
                console.log("hide toast after 5s")
            }, 5000);
        });

        close.addEventListener("click", () => { // closing toast
            toast.classList.remove("showing");
            progress.classList.remove("showing");
        });
        
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

         // Function to handle the update action and redirect to staff_return_transaction.php
        function updateAndSetSession(borrowId) {
            console.log("Borrow ID:", borrowId); // Debugging console log
            // Redirect to staff_return_transaction.php with the borrowId parameter
            window.location.href = "staff_return.php?borrowId=" + borrowId;
        }
    </script>

</body>
</html>
