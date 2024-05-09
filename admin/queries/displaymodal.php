<?php
session_start(); // Start the session if not already started

// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

// Check if the request is sent using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['borrowerID'])) {
    $borrowerID = $_POST['borrowerID'];
    $_SESSION['bID'] = $borrowerID;
    // Database connection
    $conn_display_all = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
    if ($conn_display_all->connect_error) {
        die("Connection failed: " . $conn_display_all->connect_error);
    }

    // SQL query to select the record of the specified borrowerID
    $sql_display = "SELECT * FROM tbl_borrower WHERE Borrower_ID = ?";
    
    // Prepare and bind the parameter
    $stmt = $conn_display_all->prepare($sql_display);
    $stmt->bind_param("s", $borrowerID);

    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a record is found
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
?>
        <!-- Input fields for borrower data -->
        <div class="mb-3">
            <label for="firstName" class="form-label">First Name</label>
            <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlspecialchars($row['First_Name']); ?>">
        </div>
        <!-- Middle Name -->
        <div class="mb-3">
            <label for="middleName" class="form-label">Middle Name</label>
            <input type="text" class="form-control" id="middleName" name="middleName" value="<?php echo htmlspecialchars($row['Middle_Name']); ?>">
        </div>
        <!-- Last Name -->
        <div class="mb-3">
            <label for="lastName" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars($row['Last_Name']); ?>">
        </div>
        <!-- Contact Number -->
        <div class="mb-3">
            <label for="contactNumber" class="form-label">Contact Number</label>
            <input type="text" class="form-control" id="contactNumber" name="contactNumber" value="<?php echo htmlspecialchars($row['Contact_Number']); ?>">
        </div>
        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($row['Email']); ?>">
        </div>
        <!-- Affiliation -->
        <div class="mb-3">
            <label for="affiliation" class="form-label">Affiliation</label>
            <input type="text" class="form-control" id="affiliation" name="affiliation" value="<?php echo htmlspecialchars($row['affiliation']); ?>">
        </div>
<?php
    } else {
        echo "<p>No records found.</p>";
    }
    // Close display connection
    $stmt->close();
    $conn_display_all->close();
} else {
    // Invalid request method or missing borrowerID
    echo "Invalid request or missing borrower ID.";
}
?>
