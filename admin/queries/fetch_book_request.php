<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3307);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to retrieve requested books
$sql = "SELECT tbl_requestbooks.* FROM tbl_requestbooks";
$result = $conn->query($sql);

// Prepare response data
$response = "";

if ($result->num_rows > 0) {
   
  

    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        $response .= "<tr>
        <td></td>

            <td>".$row["Book_Title"]."</td>
            <td>".$row["Authors_Name"]."</td>
            <td>".$row["Publisher_Name"]."</td>
            <td></td>
            <td></td>
            <td>".$row["tb_edition"]."</td>
            <td>".$row["Year_Published"]."</td>
            <td></td>
            <td></td>
            <td>".$row["Quantity"]."</td>
            <td>".$row["price"]."</td>
            <td>".$row["tb_status"]."</td>
            <td>";
        
        // Add appropriate action button based on status
        if ($row["tb_status"] === "Approved") {
            $response .= "<button type='button' class='btn btn-secondary' disabled>Process</button>";
        } else {
            $response .= "<a href='process_data_book.php?id=".$row["Request_ID"]."' class='btn btn-primary'>Process</a>";
        }
        
        $response .= "</td>
        </tr>";
    }

    $response .= "</tbody></table>";
} else {
    $response = "No requested books found.";
}

// Close the database connection
$conn->close();

// Return the response
echo $response;
?>

