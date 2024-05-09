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
        echo "<div id='shelfAccordion'>";
        $index = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $index++;
            echo "<div class='accordion-item'>";
            echo "<h2 class='accordion-header' id='heading{$index}'>";
            echo "<button class='accordion-button' type='button' data-bs-toggle='collapse' data-bs-target='#collapse{$index}' aria-expanded='true' aria-controls='collapse{$index}'>";
            echo $row['Category'];
            echo "</button>";
            echo "</h2>";
            echo "<div id='collapse{$index}' class='accordion-collapse collapse' aria-labelledby='heading{$index}' data-bs-parent='#shelfAccordion'>";
            echo "<div class='accordion-body'>";
            echo "<a href='#' class='shelf-button' data-shelf='" . $row['Category'] . "'></a>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "No shelf numbers found for the selected section.";
    }
    
    
} else {
    echo "Invalid request.";
}



// Close connection
mysqli_close($conn);
?>
