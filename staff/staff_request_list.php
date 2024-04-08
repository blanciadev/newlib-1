<?php
session_start();

// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

if (isset($_POST['cancelButton']) && $_POST['cancelButton'] == 1) {
    $requestID = $_POST['requestID']; // Get the request ID from the form

    // Perform the database update operation
    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Update the status to "Canceled" for the specific request ID
    $updateSql = "UPDATE tbl_requestbooks SET tb_status = 'Cancelled' WHERE Request_ID = $requestID";
    if ($conn->query($updateSql) === TRUE) {
        echo "<script>alert('Record updated successfully'); window.location.href = 'staff_request_list.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
    
    

    $conn->close(); // Close the database connection
    exit(); // Stop further execution
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VillaReadHub - Request List</title>
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
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else: ?>
                <!--default image -->
                <img src="../images/default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
       <strong><span><?php echo $userData['First_Name'] . "<br/>" . $_SESSION["role"]; ?></span></strong></div> 
    
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./staff_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item active"> <a href="./staff_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./staff_transaction_dash.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transaction</a> </li>
            <li class="nav-item"> <a href="./staff_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./staff_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>   <hr>
            <li class="nav-item"> <a href="./staff_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="" data-bs-toggle="modal" data-bs-target="#logOut"  class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
         
    </div>
    
    <div class="board container"><!--board container-->
    <div class="header1">
            <div class="text">
                <div class="back-btn">
                    <a href="./staff_books.php"><i class='bx bx-arrow-back'></i></a>
                </div>
                <div class="title">
                    <h2>Request List</h2>
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
                <th>Request ID</th>
                <th>Employee ID</th>                 
                <th>Book Title</th>
                <th>Authors ID</th>
                <th>Publisher ID</th>
                <th>Price</th>
                <th>Edition</th>
                <th>Year Published</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
                <?php
        $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // SQL query
        $sql = "SELECT Request_ID, User_ID, Book_Title, Authors_Name, Publisher_Name, price, tb_edition, Year_Published, Quantity, tb_status FROM tbl_requestbooks";
        $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>".$row["Request_ID"]."</td>
                        <td>".$row["User_ID"]."</td>
                        <td>".$row["Book_Title"]."</td>
                        <td>".$row["Authors_Name"]."</td>
                        <td>".$row["Publisher_Name"]."</td>
                        <td>".$row["price"]."</td>
                        <td>".$row["tb_edition"]."</td>
                        <td>".$row["Year_Published"]."</td>
                        <td>".$row["Quantity"]."</td>
                        <td>".$row["tb_status"]."</td>";

                // Check if the user ID matches the session user ID and the status is not "Approved"
                if ($_SESSION["User_ID"] == $row["User_ID"] && $row["tb_status"] != "Approved") {
                    if ($row["tb_status"] != "Cancelled") {
                        echo "<td>
                            <form id='cancelForm' method='POST' action=''>
                                <input type=\"hidden\" name=\"cancelButton\" value=\"1\"> <!-- This hidden input indicates the form was submitted -->
                                <input type='hidden' name='requestID' value='" . $row["Request_ID"] . "'>
                                <input type='submit' value='Cancel'>
                            </form>
                        </td>";
                    } 
                } else {
                    echo "<td></td>"; // Empty cell if the condition is not met
                }
                

                echo "</tr>";
            }
            echo "</table>";



        // Close connection
        $conn->close();
        ?>

            </tbody>
    </table>
    </div>
    <div class="btn-con">
        <button class="btn" id="requestButton">Request New Book</button>
        <!--<a href="./staff_books.php"><button class="btn">Back</button></a>-->
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


    <script>
        document.getElementById("requestButton").addEventListener("click", function() {
            window.location.href = "staff_request_form.php";
        });
    </script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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