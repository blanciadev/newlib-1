<?php
include '../auth.php';
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
    <title>VillaReadHub - Books</title>
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
                <!-- Assuming the image_data is in JPEG format, change the MIME type if needed -->
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else: ?>
                <!--default image -->
                <img src="../images/default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php $fname = $userData["First_Name"]; $lname = $userData["Last_Name"]; $userName = $fname." ". $lname;  echo $userName . "<br/>" . $_SESSION["role"]; ?></span></strong></div>
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
                <input type="radio" class="btn-check" name="options-base" id="option5" autocomplete="off" checked>
                <label class="btn btn-primary btnOption" for="option5">Filipiniana</label>

                <input type="radio" class="btn-check" name="options-base" id="option6" autocomplete="off">
                <label class="btn btn-primary btnOption" for="option6">Reference</label>

                <input type="radio" class="btn-check" name="options-base" id="option7" autocomplete="off">
                <label class="btn btn-primary btnOption" for="option7">Circulation</label>

                <input type="radio" class="btn-check" name="options-base" id="option8" autocomplete="off">
                <label class="btn btn-primary btnOption" for="option8">Fiction</label>

                <input type="radio" class="btn-check" name="options-base" id="option9" autocomplete="off">
                <label class="btn btn-primary btnOption" for="option9">Assorted Books</label>
                
                <input type="radio" class="btn-check" name="options-base" id="option10" autocomplete="off">
                <label class="btn btn-primary btnOption" for="option10">Authors</label>

                <input type="radio" class="btn-check" name="options-base" id="option10" autocomplete="off">
                <label class="btn btn-primary btnOption" for="option10">Publishers</label>
            </div>
        </div>
        <hr>
        <div class="catalogCon container">
            <div class="FIL container">
                <h4>Filipiniana</h4>
                <ul>
                    <!--this are dropdowns, if clicked the list of books will be shown(based sa shelf numbers below)-->
                    <li>Logic / Ethics</li>         <!-- 1-2 --> 
                    <li>Social Science</li>         <!-- 3 -->
                    <li>Law</li>                    <!-- 4 -->
                    <li>English Language</li>       <!-- 5-8 -->
                    <li>Filipino</li>               <!-- 9-12 -->
                    <li>Mathematics</li>            <!-- 13-16 -->
                    <li>Science</li>                <!-- 17 -->
                    <li>Music</li>                  <!-- 18-20 -->
                    <li>Philippine Literature</li>  <!-- 21-24 -->
                    <li>Philippine History</li>     <!-- 25-32 -->
                    <li>Geography and History</li>  <!-- 33-44 -->
                </ul>
            </div>
            <div class="REF container">
                <h4>Reference</h4>
                <ul>
                    <li>References</li>             <!-- 45-58 -->
                    <li>Encyclopedia</li>           <!-- 59-66 -->
                    <li>References</li>             <!-- 67-83 -->
                    <li>Language</li>               <!-- 89 -->
                    <li>Dictionaries</li>           <!-- 84-90 -->
                    <li>Encyclopedia</li>           <!-- 91-98-->
                </ul>
            </div>
            <div class="CIR container">
                <h4>Circulation</h4>
                <ul>
                    <li>Psychology</li>            <!-- 99-100 -->
                    <li>Political Science</li>     <!-- 101 -->
                    <li>Law /  Criminology</li>    <!-- 102 -->
                    <li>Education</li>             <!-- 103 -->
                    <li>Language</li>              <!-- 104-105 -->
                    <li>Social Problems and Services</li><!-- 106-107 -->
                    <li>Natural Science and Math</li><!-- 108-109 -->
                    <li>Technology</li>             <!-- 110 -->
                    <li>Medical Science</li>        <!-- 111-112 -->
                    <li>Engineering</li>            <!-- 113-115 -->
                    <li>Home Economics</li>         <!-- 116-117 -->
                    <li>Arts</li>                   <!-- 118-120 -->
                    <li>Agriculture</li>            <!-- 121 -->
                    <li>Economics</li>              <!-- 122-123 -->
                    <li>Accounting</li>             <!-- 124-126 -->
                    <li>English and Literature</li> <!-- 127-128 -->
                    <li>Geography and History</li>  <!-- 129-133 -->
                </ul>
            </div>
            <div class="FIC container">
                <h4>Fiction</h4>
                <ul>
                    <li>Fictions</li>               <!-- 134-139 -->
                </ul>
            </div>
            <div class="ASSRTD container">
                <h4>Assorted Books</h4>
                <ul>
                    <li>Assorted</li>              <!-- 140-161 -->
                </ul>
            </div>
            <div class="AUTHOR container">
                <h4>Authors</h4>
                <ul>
                    <li>...</li>              <!-- based on authors added -->
                </ul>
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