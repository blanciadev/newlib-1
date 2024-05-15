<?php
    session_start();

    

    // Check if the User_ID session variable is not set or empty
    if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
        // Redirect to index.php
        header("Location: ../index.php");
        exit(); // Ensure script execution stops after redirection
    }

    
    



    // Initialize $result variable
    $result = null;
    $bookDetails = [];

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        
        // Sanitize input to prevent SQL injection
        $conn =  mysqli_connect("localhost", "root", "root", "db_library_2", 3308); //database connection

        // Initialize an empty array to store book details
        $bookDetails = [];

        // Retrieve all Accession Codes from the form
        $accessionCodes = $_POST['Accession_Code'];
        // Retrieve Book Title from the form
        $Book_Title = $_POST['Book_Title'];

        // Ensure $accessionCodes is treated as an array
        if (!is_array($accessionCodes)) {
            $accessionCodes = [$accessionCodes];
        }

        // Loop through each Accession Code
        foreach ($accessionCodes as $Accession_Code) {
            // Check if Accession Code is provided
            if (!empty($Accession_Code) || $Accession_Code === '0') {
                // Query to retrieve book details based on Accession Code
                $sql = "SELECT
                            tbl_books.*, 
                            tbl_authors.Authors_Name
                        FROM
                            tbl_books
                        INNER JOIN
                            tbl_authors
                        ON 
                            tbl_books.Authors_ID = tbl_authors.Authors_ID
                        WHERE
                            Accession_Code = ?";
            } elseif (!empty($Book_Title)) {
                // Query to retrieve book details based on Book Title
                $sql = "SELECT
                            tbl_books.*, 
                            tbl_authors.Authors_Name
                        FROM
                            tbl_books
                        INNER JOIN
                            tbl_authors
                        ON 
                            tbl_books.Authors_ID = tbl_authors.Authors_ID
                        WHERE
                            Book_Title LIKE ?";
            } else {
                // Display an alert if neither Accession Code nor Book Title is provided
                // echo '<script>alert("Please enter either Accession Code or Book Title.");</script>';
                echo '<script>
                // Call showToast with "success" message type after successful insertion
                showToast("error", "Please enter either Accession Code or Book Title.");
                </script>';
        
    
                continue; // Skip to the next iteration of the loop
            }

            // Prepare the statement
            $stmt = $conn->prepare($sql);

            // Bind parameters and execute the query
            if ($stmt) {
                if (!empty($Accession_Code) || $Accession_Code === '0') {
                    $stmt->bind_param("s", $Accession_Code);
                } else {
                    $Book_Title = "%" . $Book_Title . "%";
                    $stmt->bind_param("s", $Book_Title);
                }
                
            $stmt->execute();

            // Get the result set
            $result = $stmt->get_result();

            // Check if the query returned any rows
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $bookDetails[] = $row;
                }
            } else {
                // Display an alert for invalid Accession Code or Book Title
                // echo '<script>alert("Invalid Accession Code or Book Title: ' . $Accession_Code . ' - ' . $Book_Title . '");</script>';
                echo '<script>
                // // Call showToast with "success" message type after successful insertion
                // showToast("error", "Invalid Accession Code or Book Title");
                // </script>';
            }
            
            // Close the statement
            $stmt->close();
            }
        }
        // Close the database connection
        $conn->close();

        // Store bookDetails array in session
        $_SESSION['bookDetails'] = $bookDetails;

        // Output book details in JSON format
        echo json_encode($bookDetails);
        exit(); // Stop further execution
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Book</title>
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
        <div class="user-header d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
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
            <?php if (!empty($userData['image_data'])) : ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else : ?>
                <img src="default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php echo $userData['First_Name'] . "<br/>" . $_SESSION["role"]; ?></span></strong>
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <!-- navitem container -->
            <li class="nav-item"> <a href="./staff_dashboard.php" class="nav-link link-body-emphasis "> <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./staff_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item active"> <a href="./staff_transaction_dash.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transaction</a> </li>
            <li class="nav-item"> <a href="./staff_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./staff_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <hr>
            <li class="nav-item"> <a href="./staff_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
    </div>
    <div class="board1 container"><!--board container-->
        <div class="header1">
            <div class="text">
                <div class="back-btn">
                    <a href="./staff_borrow_dash.php"><i class='bx bx-arrow-back'></i></a>
                </div>
                <div class="title">
                    <h2>Search Book by Accession Code</h2>
                </div>
                <!-- Add the book cart icon and badge -->
                <div class="book-cart">
                    <i class='bx bx-cart-alt'></i>
                    <span class="badge bg-secondary" id="bookCartBadge">0</span>
                </div>
            </div>
        </div>

        <div class='books container'>
            
        <form id="dataform" method="POST">
    <label for="Accession_Code">Accession Code:</label>
    <input type="text" id="Accession_Code" name="Accession_Code[]" placeholder="Enter Accession Code">

    <label for="Book_Title">Book Title:</label>
    <input type="text" id="Book_Title" name="Book_Title" placeholder="Enter Book Title">

    <div class="container">
        <h3>Search Results</h3>
        <div class="bookSearchResult container" id="bookDetailsContainer">
            <!-- Display book details will be added dynamically -->
        </div>
    </div>
    
    <a id="checkoutBtn" class="btn btn-primary" style="display: none;">Checkout</a>
</form>

<div class="toastNotif hide">
<div class="toast-content">
    <i class="bx bx-check check"></i>
    <div class="message">
        <span class="text text-1">Success</span>
        <!-- this message can be changed to "Success" and "Error"-->
        <span class="text text-2"></span>
        <!-- specify based on the if-else statements -->
    </div>
</div>
<i class="bx bx-x close"></i>
<div class="progress"></div>
</div>

<script>

    
// Define showToast() function
function showToast(messageType, message) {
    console.log("Toast Called");
    var toast = document.querySelector(".toastNotif");
    var progress = document.querySelector(".progress");
    
    // Set the message type and text
    toast.querySelector(".text-1").textContent = messageType;
    toast.querySelector(".text-2").textContent = message;
    
    if (toast && progress) {
        toast.classList.add("showing");
        progress.classList.add("showing");
        setTimeout(() => {
            toast.classList.remove("showing");
            progress.classList.remove("showing");
        }, 5000);
    } else {
        console.error("Toast elements not found");
    }
}

function closeToast() {
    var toast = document.querySelector(".toastNotif");
    var progress = document.querySelector(".progress");
    toast.classList.remove("showing");
    progress.classList.remove("showing");
}



    document.addEventListener('DOMContentLoaded', function() {
        let bookDetails = [];
        const bookCartBadge = document.getElementById('bookCartBadge');
        const checkoutBtn = document.getElementById('checkoutBtn');

        const accessionCodeInput = document.getElementById('Accession_Code');
        const bookTitleInput = document.getElementById('Book_Title');

        [accessionCodeInput, bookTitleInput].forEach(input => {
            input.addEventListener('input', function() {
                console.log('Input event triggered'); // Debugging statement

                const formData = new FormData(document.getElementById('dataform'));

                fetch('staff_book_borrow_find.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text(); // Change to text() to read response as text
                })
                .then(data => {
                    console.log('Data received from server:', data); // Debugging statement

                    try {
                        const jsonData = JSON.parse(data); // Attempt to parse response as JSON
                        console.log('Parsed JSON data:', jsonData); // Debugging statement

                        const bookDetailsContainer = document.getElementById('bookDetailsContainer');
                        bookDetailsContainer.innerHTML = '';

                        jsonData.forEach(book => {
                            console.log('Processing book:', book); // Debugging statement

                            const bookDiv = document.createElement('div');
                            bookDiv.classList.add('book');
                            bookDiv.innerHTML = `
                                <br>
                                <h3>Book Details</h3>
                                <br>
                                <p><strong>Accession Code:</strong> ${book['Accession_Code']}</p>
                                <p><strong>Title:</strong> ${book['Book_Title']}</p>
                                <p><strong>Author:</strong> ${book['Authors_Name']}</p>
                                <p><strong>Edition:</strong> ${book['tb_edition']}</p>
                                <p><strong>Availability:</strong> ${book['Quantity']}</p>
                                <button type="button" class="btn btn-secondary add-book-btn" data-accession="${book['Accession_Code']}">Get Book</button>
                                <hr>
                            `;
                            const quantity = book['Quantity'];

                            const addBookBtn = bookDiv.querySelector('.add-book-btn');
                            if (quantity === 0) {
                                addBookBtn.disabled = true;
                            } else {
                                addBookBtn.addEventListener('click', function() {
                                    console.log('Add to cart button clicked'); // Debugging statement

                                    const accessionCode = this.getAttribute('data-accession');
                                    const index = bookDetails.indexOf(accessionCode);
                                    if (index === -1) {
                                        bookDetails.push(accessionCode);
                                    } else {
                                        bookDetails.splice(index, 1);
                                    }
                                    console.log('Updated bookDetails array:', bookDetails); // Debugging statement
                                    bookCartBadge.textContent = bookDetails.length;
                                    checkoutBtn.style.display = bookDetails.length > 0 ? 'block' : 'none';
                                });
                            }
                            bookDetailsContainer.appendChild(bookDiv);
                        });
                    } catch (error) {
                        console.error('Error parsing JSON:', error);
                        // Handle error (e.g., display an error message to the user)
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Handle error (e.g., display an error message to the user)
                });
            });
        });

        // Add event listener to "Checkout" button
        checkoutBtn.addEventListener('click', function() {
            console.log('Checkout button clicked'); // Debugging statement
            // Construct the URL with the bookDetails array values
            const url = 'staff_book_borrow_process.php?bookDetails=' + JSON.stringify(bookDetails);
            console.log('Checkout URL:', url); // Debugging statement
            // Redirect to the checkout page with the bookDetails in the URL
            window.location.href = url;
        });
    });
</script>


</body>

</html>