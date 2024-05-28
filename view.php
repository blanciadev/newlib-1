<?php
    session_start();

    

    


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
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
 
    <link rel="icon" href="../images/lib-icon.png">
    <style>
        
        .add-book-btn {
            display: none;
        }
        .book-card {
    width: 300px; /* Set the desired width */
    border: 1px solid #ccc;
    padding: 16px;
    margin-bottom: 16px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    background-color: #f8f9fa; /* Add background color here */
}

        .card-title {
            font-size: 1.25rem;
            margin-bottom: 8px;
        }
        .card-text {
            margin-bottom: 8px;
        }
        .book-container {
            margin-top: 16px;
        }
        .hidden-button {
    display: none !important;
}

    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="header">
            <div class="text">
                <div class="title">
                    <h2>Client Dashboard</h2>
                    <p>Welcome! <strong>Client!</strong></p>
                </div>
            </div>
            <div class="datetime">
                <p id="currentTime" style="font-size:1rem;font-weight: 700; margin:0%;"></p>
                <p id="currentDate" style="font-size: 10pt;margin:0%;"></p>
            </div>
        </div>

        <div class="container-fluid">
            <div class="header1">
                <div class="text">
                    <div class="back-btn">
                        <a href="./index.php"><i class='bx bx-arrow-back'></i></a>
                    </div>
                    <div class="title">
                        <h2>Search Book</h2>
                    </div>
                </div>
            </div>
            <div class='books container'>
                <form id="dataform" method="POST"> 
                    <label for="Accession_Code">Accession Code:</label>
                    <input type="text" id="Accession_Code" name="Accession_Code[]" class="form-control mb-3" placeholder="Enter Accession Code">

                    <label for="Book_Title">Book Title:</label>
                    <input type="text" id="Book_Title" name="Book_Title" class="form-control mb-3" placeholder="Enter Book Title">
                    <div class="container book-container" id="bookDetailsContainer">
                        <!-- Display book details will be added dynamically -->
                    </div>
                    <a id="checkoutBtn" class="btn btn-primary">Checkout</a> 
                </form>
            </div>    
        </div>
        <a href="./index.php" class="nav-link link-body-emphasis"><i class='bx bxs-lock'></i>Return To Login</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Define the bookDetails array
            let bookDetails = [];

            // Get the book cart badge element
            const bookCartBadge = document.getElementById('bookCartBadge');

            // Hide the "Checkout" button initially
            const checkoutBtn = document.getElementById('checkoutBtn');
            checkoutBtn.style.display = 'none';

            // Add event listener to search input fields
            const accessionCodeInput = document.getElementById('Accession_Code');
            const bookTitleInput = document.getElementById('Book_Title');

            // Add event listener to both input fields
            [accessionCodeInput, bookTitleInput].forEach(input => {
                input.addEventListener('input', function() {
                    // Get the form data
                    const formData = new FormData(document.getElementById('dataform'));
                    console.log('Form data:', formData);

                    // Send an AJAX request
                    fetch('view.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Response:', data);
                        // Display book details dynamically
                        const bookDetailsContainer = document.getElementById('bookDetailsContainer');
                        bookDetailsContainer.innerHTML = '';
                        data.forEach(book => {
                            const bookDiv = document.createElement('div');
                            bookDiv.classList.add('book');
                            // Assuming bookDiv is the target element where book details are displayed
                            bookDiv.innerHTML = `
                                <div class="book-card">
                                    <h3 class="card-title">Book Details</h3>
                                    <p class="card-text"><strong>Accession Code:</strong> ${book['Accession_Code']}</p>
                                    <p class="card-text"><strong>Title:</strong> ${book['Book_Title']}</p>
                                    <p class="card-text"><strong>Author:</strong> ${book['Authors_Name']}</p>
                                    <p class="card-text"><strong>Edition:</strong> ${book['tb_edition']}</p>
                                    <p class="card-text"><strong>Availability:</strong> ${book['Quantity']}</p>
                                    <button type="button" class="btn btn-secondary add-book-btn hidden-button" data-accession="${book['Accession_Code']}">Get Book</button>
                                </div>
                            `;

                            const quantity = book['Quantity'];

                            // Add event listener to "Add Book" button
                            const addBookBtn = bookDiv.querySelector('.add-book-btn');
                            if (quantity == 0) {
                                addBookBtn.disabled = true; // Disable button if quantity is 0
                            } else {
                                addBookBtn.style.display = 'block';
                                addBookBtn.addEventListener('click', function() {
                                    // Add or remove the book from the bookDetails array
                                    const accessionCode = this.getAttribute('data-accession');
                                    const index = bookDetails.indexOf(accessionCode);
                                    if (index === -1) {
                                        bookDetails.push(accessionCode);
                                    } else {
                                        bookDetails.splice(index, 1);
                                    }

                                    // Update the book cart badge count
                                    bookCartBadge.textContent = bookDetails.length;
                                    console.log('Book details:', bookDetails);

                                    // Show or hide the "Checkout" button based on the bookDetails array length
                                    checkoutBtn.style.display = bookDetails.length > 0 ? 'block' : 'none';
                                });
                            }
                            bookDetailsContainer.appendChild(bookDiv);
                        });
                    })
                    .catch(error => console.error('Error:', error));
                });
            });
        });
    </script>
</body>
</html>
