<?php
include '../auth.php';

if(!isset($_SESSION["staff_name"])) {
    header("location: ../auth.php");
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
        <div class="user-header mt-4 d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
            <img src="https://github.com/mdo.png" alt="" width="50" height="50" class="rounded-circle me-2">
            <strong><span><?php echo $_SESSION["staff_name"] ."<br/>"; echo $_SESSION["role"]; ?></span> </strong> 
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./staff_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item active"> <a href="./staff_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./staff_borrow.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Borrow</a> </li>
            <li class="nav-item"> <a href="./staff_return.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Return</a> </li>
            <li class="nav-item"> <a href="./staff_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./staff_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <hr>
            <li class="nav-item"> <a href="./staff_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
         
    </div>

    <div class="board container"><!--board container-->
    <div class="bookcon">
                    <div class="b-icon"><i class='bx bxs-book'></i></div>
                    <form action="" method="get">
                    <div class="searchbox">
                        <input type="text" name="search" placeholder="Search" value="<?php if(isset($_GET['search'])){
                            echo $_GET['search'];
                        } ?>">
                        <button type="submit"><i class='bx bx-search'></i></button>
                    </div>
                    </form>
                    <div class="booktable">
                        <table>
                            <thead>
                                <tr>
                                    <th>Accession Code</th>
                                    <th>Book Title</th>
                                    <th>Author</th>
                                    <th>Publisher</th>
                                    <th>Section</th>
                                    <th>Shelf Number</th>
                                    <th>Edition</th>
                                    <th>Year Published</th>
                                    <th>ISBN</th>
                                    <th>Available</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM books";

                                if (isset($_GET['search'])) {
                                    $filtervalue = $_GET['search'];
                                    $sql = "SELECT * FROM books WHERE CONCAT(Accession_Code, Book_Title, Authors_ID, Publisher_ID, Section_Code, Shelf_Number, Year_Published, ISBN) LIKE '%$filtervalue%'";
                                     
                                } 
 
                                $result = $conn->query($sql); 

                                if(mysqli_num_rows($result) == 0){
                                    echo "<tr>
                                            <td colspan='10' style='text-align: center;'>No Matches Found</td>
                                          </tr> 
                                    ";

                                }
                                
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                                    <td>" . $row["Accession_Code"] . "</td>
                                                    <td>" . $row["Book_Title"] . "</td>
                                                    <td>" . $row["Authors_ID"] . "</td>
                                                    <td>" . $row["Publisher_ID"] . "</td>
                                                    <td>" . $row["Section_Code"] . "</td>
                                                    <td>" . $row["Shelf_Number"] . "</td>
                                                    <td>" . $row["Edition"] . "</td>
                                                    <td>" . $row["Year_Published"] . "</td>
                                                    <td>" . $row["ISBN"] . "</td> 
                                                    <td>" . $row["Quantity"] . "</td>
                                                </tr>";
                                }

                               
                                ?>

                            </tbody>
                        </table>
                    </div> 
                    <div class="reqBooks">
                        <a href="./request_book.php" class="req-btn">Request Book</a> 
                    </div>
                </div>


    </div>
        

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