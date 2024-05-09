
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

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch shelf numbers based on selected section code
if (isset($_POST['sectionCode'])) {
    $sectionCode = $_POST['sectionCode'];
    $sql = "SELECT Category FROM tbl_shelf WHERE shelf_code = '$sectionCode'";
    $result = mysqli_query($conn, $sql);

   
    if ($result && mysqli_num_rows($result) > 0) {
        // Display shelf numbers as options
        echo '<div class="form-group">';
       
        echo '<select id="shelf" name="shelf" class="form-select">';
        echo '<option value="">Select Shelf</option>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['Category'] . '">' . $row['Category'] . '</option>';
        }
        echo '</select>';
        echo '</div>';
    } else {
        echo '<div class="alert alert-warning" role="alert">No shelf numbers found for the selected section.</div>';
    }
    
    
} else {
    echo "Invalid request.";
}



// Close connection
mysqli_close($conn);
?>
