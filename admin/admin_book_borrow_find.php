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
    $conn =  mysqli_connect("localhost", "root", "root", "db_library_2", 3307); //database connection

      // Initialize an empty array to store book details
      $bookDetails = [];
    // Retrieve all Accession Codes from the form
    $accessionCodes = $_POST['Accession_Code'];

    // Ensure $accessionCodes is treated as an array
    if (!is_array($accessionCodes)) {
        $accessionCodes = [$accessionCodes];
    }

    // Loop through each Accession Code
    foreach ($accessionCodes as $Accession_Code) {
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
                    Accession_Code = '$Accession_Code'";

        $result = $conn->query($sql);

        // Check if the query returned any rows
        if ($result && $result->num_rows > 0) {
            $bookDetails[] = $result->fetch_assoc();
            echo json_encode($bookDetails);

        } else {
            // Display an alert for invalid Accession Code
            echo '<script>alert("Invalid Accession Code");</script>';
        }
    }

    // Close the database connection
    $conn->close();
      // Store bookDetails array in session
      $_SESSION['bookDetails'] = $bookDetails;

    // Output book details in JSON format
    
    exit(); // Stop further execution
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
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <h2>Villa<span>Read</span>Hub</h2>
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon" />
        </a><!--header container-->
        <div class="user-header d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
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
                <!-- Change the path to your actual default image -->
                <img src="default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php echo $userData['First_Name'] . "<br/>" . $_SESSION["role"]; ?></span></strong>
        </div><!-- navitem container -->
        <ul class="nav nav-pills flex-column mb-auto">
            <!-- navitem container -->
            <li class="nav-item"> <a href="./staff_dashboard.php" class="nav-link link-body-emphasis "> <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./staff_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./staff_transaction_dash.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transaction</a> </li>
            <li class="nav-item"> <a href="./staff_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./staff_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <hr>
            <li class="nav-item"> <a href="./staff_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
    </div>

    <div class="board container">
        <div class='books-container'>
            <h1>Search Book by Accession Code</h1>

            <form id="dataform" method="POST">
                <label for="Accession_Code">Accession Code:</label>
                <!-- Allow multiple Accession Codes -->
                <input type="text" id="Accession_Code" name="Accession_Code[]" placeholder="Enter Accession Code" required onkeydown="return (event.keyCode !== 13);">

                <!-- <button type="button" class="btn btn-primary" id="book_borrow">Retrieve Book</button> -->
                <br><br>
                <a id="checkoutBtn" class="btn btn-primary">Checkout</a>
            <br>
            </form>

            <div class="board container">
                <div class='books-container' id="bookDetailsContainer">
                    <h1>Book Details</h1>
                    <!-- Display book details will be added dynamically -->
                    
                </div>
            </div>
        </div>
    </div>

    <script>

document.addEventListener('DOMContentLoaded', function() {
    // Define the bookDetails array
    let bookDetails = [];

    // Hide the "Checkout" button initially
    const checkoutBtn = document.getElementById('checkoutBtn');
    checkoutBtn.style.display = 'none';

    // Add event listener to input field for instant search
    document.getElementById('Accession_Code').addEventListener('input', function() {
        // Get the value of the input field
        const accessionCode = this.value.trim();
        if (accessionCode !== '') {
            // Send an AJAX request if input is not empty
            fetch('admin_book_borrow_find.php', {
                method: 'POST',
                body: new FormData(document.getElementById('dataform'))
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response:', data); // Log the response data
                // Check if the response indicates invalid Accession Code
                if (data && data.invalid_accession_code) {
                    // Display an alert for invalid Accession Code
                    alert('Invalid Accession Code');
                } else {
                    // Display book details dynamically
                    const bookDetailsContainer = document.getElementById('bookDetailsContainer');
                    bookDetailsContainer.innerHTML = '';
                    data.forEach(book => {
                        const bookDiv = document.createElement('div');
                        bookDiv.classList.add('book');
                        bookDiv.innerHTML = `
                            <p><strong>Accession Code:</strong> ${book['Accession_Code']}</p>
                            <p><strong>Title:</strong> ${book['Book_Title']}</p>
                            <p><strong>Author:</strong> ${book['Authors_Name']}</p>
                            <p><strong>Availability:</strong> ${book['Quantity']}</p>
                            <button type="button" class="btn btn-secondary add-book-btn" data-accession="${book['Accession_Code']}">Add Book</button>
                            <hr>
                        `;

                        const quantity = book['Quantity'];

                            // Add event listener to "Add Book" button
                            const addBookBtn = bookDiv.querySelector('.add-book-btn');
                            if (quantity ==0 ) {
                            addBookBtn.disabled = true; // Disable button if quantity is 0 or 10
                        } else {
                           addBookBtn.addEventListener('click', function() {
                                    // Add the book to the bookDetails array if it's not already there
                                    const accessionCode = this.getAttribute('data-accession');
                                    if (!bookDetails.includes(accessionCode)) {
                                        bookDetails.push(accessionCode);
                                        console.log(bookDetails); // Show the "Checkout" button when bookDetails is populated
                                        checkoutBtn.style.display = 'block';
                                    } else {
                                        console.log('Book already added.');
                                    }
                                });
                        }

                        bookDetailsContainer.appendChild(bookDiv);
                    });
                }
            })
            .catch(error => console.error('Error:', error)); // Log any errors
        } else {
            // Clear the book details container if input is empty
            document.getElementById('bookDetailsContainer').innerHTML = '';
        }
    });

    // Add event listener to "Checkout" button
    checkoutBtn.addEventListener('click', function() {
        // Construct the URL with the bookDetails array values
        const url = 'admin_book_borrow_process.php?bookDetails=' + JSON.stringify(bookDetails);
        // Redirect to the checkout page with the bookDetails in the URL
        window.location.href = url;
    });
});


</script>


</body>

</html>
