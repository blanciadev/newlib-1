<?php

session_start();
// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

// Check if the form is submitted
if (isset($_POST['submit'])) {

    $_SESSION['User_ID'];
    $userID = $_SESSION['User_ID'];
    $bookTitle = $_POST['bookTitle'];
    $author = $_POST['author'];
    $publisher = $_POST['publisher'];
    $edition = $_POST['edition'];
    $year = $_POST['year'];
    $quantity = $_POST['quantity'];
    $status = "Pending";
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    $country = $_POST['country'];
    
    // Retrieve selected section and shelf number from hidden input fields
    $selectedSection = $_POST['selectedSection'];
    $selectedShelf = $_POST['selectedShelf'];



    // Handle author input
    if ($_POST['author'] === 'Other') {
        // Use the value from the "New Author" input field
        $author = $_POST['newAuthor'];
    } else {
        // Use the selected author from the dropdown
        $author = $_POST['author'];
    }


    // Validate form data (you may need more robust validation)
    if (empty($bookTitle) || empty($author) || empty($publisher) || empty($quantity)) {
        $errorMessage = "Please fill in all fields.";
        ?>
        <script>
            console.log("Missing fields: <?php echo empty($bookTitle) ? 'Book Title, ' : '' ?><?php echo empty($author) ? 'Author, ' : '' ?><?php echo empty($publisher) ? 'Publisher, ' : '' ?><?php echo empty($quantity) ? 'Quantity' : '' ?>");
        </script>
        <?php
    }
     else {
        // Handle "Other Edition" input
        if ($edition === "Other") {
            // Check if the 'otherEdition' input is set and not empty
            if (isset($_POST['otherEdition']) && !empty($_POST['otherEdition'])) {
                $edition = $_POST['otherEdition']; // Use the input value for edition
            } else {
                $errorMessage = "Please provide a value for Other Edition.";
            }
        }

        // Insert data into the database
        $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308); //database connection

        // Assuming you have a database connection named $conn
        $query = "INSERT INTO tbl_requestbooks (User_ID, Book_Title, Authors_Name, Publisher_Name, price, tb_edition, Year_Published, Quantity, country, tb_status, Section_Code, shelf) 
        VALUES ('$userID', '$bookTitle', '$author', '$publisher', '$price', '$edition', '$year', '$quantity', '$country', '$status', '$selectedSection', '$selectedShelf')";

        $result = mysqli_query($conn, $query);

        if ($result) {
            $successMessage = "Request submitted successfully.";
        } else {
            $errorMessage = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requesting New Book</title>
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
            <?php if (!empty($userData['image_data'])) : ?>
                <!-- Assuming the image_data is in JPEG format, change the MIME type if needed -->
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else : ?>
                <!-- default image -->
                <img src="../images/default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php echo $userData['First_Name'] . "<br/>" . $_SESSION["role"]; ?></span></strong>
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./staff_dashboard.php" class="nav-link link-body-emphasis "> <i class='bx bxs-home'></i>Dashboard </a> </li>
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
                    <a href="./staff_request_list.php"><i class='bx bx-arrow-back'></i></a>
                </div>
                <div class="title">
                    <h2>Request Form</h2>
                </div>
            </div>
        </div>
        <div class="books container">
            <!-- Display success or error message -->
            <div class="container">
                <?php if (!empty($successMessage)) : ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $successMessage; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errorMessage)) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $errorMessage; ?>
                    </div>
                <?php endif; ?>
            </div>

            <form method="POST" action="">

                <input type="hidden" name="userID" value="<?php echo $_SESSION['User_ID']; ?>">

                <div class="mb-3">
                    <label for="bookTitle" class="form-label">Book Title</label>
                    <input type="text" class="form-control" id="bookTitle" name="bookTitle" required>
                </div>

                <?php
                    // Database connection
                    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $query = "SELECT Authors_Name, Authors_ID FROM tbl_authors";
                    $result = mysqli_query($conn, $query);

                    $existingAuthors = [];
                    while ($row = mysqli_fetch_assoc($result)) {
                        $existingAuthors[] = $row['Authors_Name'];
                    }

                    // Add an "Other" option to the existing authors
                    $existingAuthors[] = "Other";
                ?>

                <div class="mb-3">
                    <label for="authorSelect" class="form-label">Author</label>
                    <select class="form-select" id="author" name="author" required>
                        <option value="" disabled selected>Select an author</option>
                        <?php foreach ($existingAuthors as $authorOption) : ?>
                        <option value="<?php echo $authorOption; ?>"><?php echo $authorOption; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3" id="newAuthorInput" style="display: none;">
                <label for="newAuthor" class="form-label">New Author</label>
                    <input type="text" class="form-control" id="newAuthor" name="newAuthor">
                    <label for="country" class="form-label">Country</label>
                    <input type="text" class="form-control" id="country" name="country">
                </div>

                <div class="mb-3">
                    <label for="publisher" class="form-label">Publisher</label>
                    <input type="text" class="form-control" id="publisher" name="publisher" required>
                </div>
                <div class="mb-3">
                    <label for="year" class="form-label">Year Published</label>
                    <input type="text" class="form-control" id="year" name="year" required>
                </div>
                <div class="mb-3">
                    <label for="edition" class="form-label">Edition</label>
                    <select class="form-select" id="edition" name="edition" onchange="toggleOtherEdition()" required>
                        <option value="First Edition">First Edition</option>
                        <option value="Second Edition">Second Edition</option>
                        <option value="Third Edition">Third Edition</option>
                        <option value="Fourth Edition">Fourth Edition</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <div id="otherEditionContainer" class="mb-3" style="display: none;">
                        <label for="otherEdition" class="form-label">Other Edition</label>
                        <input type="text" class="form-control" id="otherEdition" name="otherEdition">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="year" class="form-label">Price</label>
                    <input type="text" class="form-control" id="price" name="price" required>
                </div>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" required>
                </div>

                <?php
                    // Fetch sections from tbl_section
                    $sql_sections = "SELECT Section_Code, Section_Name FROM tbl_section";
                    $result_sections = mysqli_query($conn, $sql_sections);

                    // Check if sections were fetched successfully
                    
                    if ($result_sections && mysqli_num_rows($result_sections) > 0) {
                        echo '<div class="form-group">';
                        echo '<label for="section" class="form-label">Section:</label>';
                        echo '<select id="section" name="section" class="form-select" required>';
                        echo '<option value="">Select Section</option>';
                        
                        // Display options for each section
                        while ($row = mysqli_fetch_assoc($result_sections)) {
                            echo '<option value="' . $row['Section_Code'] . '">' . $row['Section_Name'] . '</option>';
                        }
                        
                        echo '</select>';
                        echo '</div>';
                    } else {
                        echo '<div class="alert alert-warning" role="alert">No sections found</div>';
                    }
                    // Close connection
                    mysqli_close($conn);
                ?>
                <br>
                <div class="mb-3">
                    <label for='shelf'>Shelf Number:</label>
                    <div id="shelfContainer"></div>
                    <input type="hidden" id="selectedSection" name="selectedSection">
                    <input type="hidden" id="selectedShelf" name="selectedShelf">
                </div>
                    <br>
                    <button type="submit" class="btn btn-primary" name="submit">Submit Request</button>
            </form>
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
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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


        $(document).ready(function() {
            // Event listener for section dropdown change
            $('#section').change(function() {
                var sectionCode = $(this).val();
                    
                // AJAX request to fetch shelf numbers
                $.ajax({
                    url: 'queries/shelf.php', // Update the URL to your PHP script
                    method: 'POST',
                    data: { sectionCode: sectionCode },
                    dataType: 'html',
                    success: function(response) {
                        // Update shelf container with fetched shelf numbers
                        $('#shelfContainer').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching shelf numbers:', error);
                    }
                });
            });
            // Event listener for form submission
            $('form').submit(function() {
                // Get selected section and shelf number
                var selectedSection = $('#section').val();
                var selectedShelf = $('#shelf').val(); // Corrected variable name

                // Set hidden input values
                $('#selectedSection').val(selectedSection);
                $('#selectedShelf').val(selectedShelf);
            });

        });

        //JavaScript to toggle input field
        document.getElementById("author").addEventListener("change", function() {
            const newAuthorInput = document.getElementById("newAuthorInput");
            newAuthorInput.style.display = (this.value === "Other") ? "block" : "none";
        });

        function toggleOtherEdition() {
            const editionSelect = document.getElementById('edition');
            const otherEditionContainer = document.getElementById('otherEditionContainer');
            const otherEditionInput = document.getElementById('otherEdition');

            if (editionSelect.value === 'Other') {
                otherEditionContainer.style.display = 'block'; // Show the input field
                otherEditionInput.required = true; // Make the input field required
            } else {
                otherEditionContainer.style.display = 'none'; // Hide the input field
                otherEditionInput.required = false; // Make the input field optional
                otherEditionInput.value = ''; // Clear the input field value
            }
        }
    </script>
</body>
</html>