<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);

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
        echo "<select id='shelf' name='shelf'>";
        echo "<option value=''>Select Shelf </option>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<option value='" . $row['Category'] . "'>" . $row['Category'] . "</option>";
        }
        echo "</select>";
    } else {
        echo "No shelf numbers found for the selected section.";
    }
} else {
    echo "Invalid request.";
}



// Close connection
mysqli_close($conn);
?>
