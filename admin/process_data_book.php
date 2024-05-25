<?php
session_start();

// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

// Check if the ID parameter is set in the URL
if (isset($_GET['id'])) {
    // Get the ID value from the URL
    $id = $_GET['id'];

    $_SESSION['reqID'] = $id;
    // Database connection
    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    // SQL query to retrieve available books
    $sql = "SELECT tbl_requestbooks.* FROM tbl_requestbooks WHERE Request_ID ='$id'";

    $result = $conn->query($sql);

    echo '<script>console.log("ID RETRIEVED '.$id.'");</script>';
} else {
    // If the ID parameter is not set in the URL
    echo "No ID parameter found in the URL.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VillaReadHub - Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
        <div class="user-header mr-3 d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
            <img src="https://github.com/mdo.png" alt="" width="50" height="50" class="rounded-circle me-2">
            <p>(ADMIN)</p>
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./admin_dashboard.php" class="nav-link link-body-emphasis "> <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item active active"> <a href="./admin_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
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

    <h2>Book Request Information</h2>

    <div class="board container">
        <form id="myForm" method="POST" action="">
            <div class="mb-3">
                <label for="accessionCode" class="form-label">Custom Accession Code</label>
                <input type="text" class="form-control" id="accessionCode" name="accessionCode">
            </div>

            <?php
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<p><strong>Book Title:</strong> " . $row["Book_Title"] . "</p>";
                echo "<p><strong>Authors Name:</strong> " . $row["Authors_Name"] . "</p>";
                echo "<p><strong>Publisher Name:</strong> " . $row["Publisher_Name"] . "</p>";
                echo "<p><strong>Edition:</strong> " . $row["tb_edition"] . "</p>";
                echo "<p><strong>Year Published:</strong> " . $row["Year_Published"] . "</p>";
                echo "<p><strong>Country:</strong> " . $row["country"] . "</p>";
                echo "<p><strong>Quantity:</strong> " . $row["Quantity"] . "</p>";
                echo "<p><strong>Price:</strong> " . $row["price"] . "</p>";
                echo "<p><strong>Status:</strong> " . $row["tb_status"] . "</p>";

                echo "<input type='hidden' id='Authors_ID' name='Authors_ID' value='" . $row["Authors_Name"] . "'>";
                echo "<input type='hidden' name='Book_Title' value='" . $row["Book_Title"] . "'>";
                echo "<input type='hidden' name='Publisher_Name' value='" . $row["Publisher_Name"] . "'>";
                echo "<input type='hidden' name='tb_edition' value='" . $row["tb_edition"] . "'>";
                echo "<input type='hidden' name='Year_Published' value='" . $row["Year_Published"] . "'>";
                echo "<input type='hidden' name='country' value='" . $row["country"] . "'>";
                echo "<input type='hidden' name='Quantity' value='" . $row["Quantity"] . "'>";
                echo "<input type='hidden' name='price' value='" . $row["price"] . "'>";
                echo "<input type='hidden' name='section' value='" . $row["Section_Code"] . "'>";
                echo "<input type='hidden' name='shelf' value='" . $row["shelf"] . "'>";
            }
            ?>

    <div class="mb-3">
                    <label for="type" class="form-label">Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="Procured" selected>Supplier Procured</option>
                        <option value="Donated">Donated</option>
                    </select>
                </div>

            <div class="mb-3">
                <label for="add_name" class="form-label">Name</label>
                <input type="text" class="form-control" id="add_name" name="add_name" required>
            </div>

            <div class="mb-3">
                <label for="add_address" class="form-label">Address</label>
                <input type="text" class="form-control" id="add_address" name="add_address" required>
            </div>

            <div class="mb-3">
                <label for="add_email" class="form-label">Email</label>
                <input type="text" class="form-control" id="add_email" name="add_email" required>
            </div>

            <div class="mb-3">
                <label for="add_contact" class="form-label">Contact Number</label>
                <input type="text" class="form-control" id="add_contact" name="add_contact" required>
            </div>
            
            <button type="submit" class="btn btn-primary" id="submit" name="submit">Procure Book</button>
            <a href="admin_books.php" class="btn btn-primary">Cancel</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        // Assuming you have a form with id 'myForm' and an AJAX endpoint 'process_data_ajax.php'
        var form = document.getElementById('myForm');

        form.addEventListener('submit', function(event) {
            // Prevent the default form submission behavior
            event.preventDefault();

            // Gather the form data
            var formData = new FormData(form);

            // Add the 'submit' parameter to the form data
            formData.append('submit', true);

            // Create and configure the AJAX request
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'process_data_ajax.php', true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest'); // Add this line to identify AJAX requests

            // Define the callback function to handle the AJAX response
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        // Successful AJAX request
                        if (xhr.responseText.includes('Update Quantity Success') || xhr.responseText.includes('Insert New Book Success') || xhr.responseText.includes('Update Success')) {
                            // Display alert for successful operation
                            alert('Operation successful!');
                            window.location.href = "admin_books.php";
                        } else {
                            // Display alert for other messages or errors
                            alert(xhr.responseText);
                        }
                    } else {
                        // Error handling for failed AJAX request
                        console.error('AJAX request failed:', xhr.status, xhr.statusText);
                    }
                }
            };

            // Send the AJAX request with form data
            xhr.send(formData);
        });
    </script>
</body>
</html>
