<?php
session_start();

// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

// Establish database connection
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);

// Check connection
if (!$conn) {
    die(json_encode(array("error" => "Connection failed: " . mysqli_connect_error())));
}

// Check if the target is received
if (isset($_POST['target'])) {
    // Sanitize the received target to prevent SQL injection
    $target = mysqli_real_escape_string($conn, $_POST['target']);

    // Prepare SQL query based on the target
    $sql = "";
    if ($target === "Authors") {
        $sql = "SELECT Authors_Name, Authors_ID, Nationality FROM tbl_authors";
    } elseif ($target === "Publishers") {
        $sql = "SELECT DISTINCT Publisher_Name FROM tbl_books";
    }

        // Execute the SQL query
        $result = mysqli_query($conn, $sql);

        // Check if query execution was successful
        if ($result) {
        // Output fetched data as HTML
        while ($row = mysqli_fetch_assoc($result)) {
            // Echo the data based on the target
            if ($target === "Authors") {
                $author = $row['Authors_Name'];
                $author_id = $row['Authors_ID']; // Retrieve the Authors_ID
                $nationality = $row['Nationality'];
                echo "<div class='accordion-item'>
                        <h2 class='accordion-header' id='heading$author_id'>
                            <button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#collapse$author_id' aria-expanded='false' aria-controls='collapse$author_id'>
                                <strong>Author's Name:</strong> $author
                            </button>
                        </h2>
                        <div id='collapse$author_id' class='accordion-collapse collapse' aria-labelledby='heading$author_id'>
                            <div class='accordion-body'>
                                <p><strong>Nationality:</strong> $nationality</p>
                                <p><strong>Books:</strong></p>
                                <ul class='list-group books-list'>";
                // Fetch and display the author's books dynamically
                $book_query = "SELECT Book_Title FROM tbl_books WHERE Authors_ID = '$author_id'";
                $books_result = mysqli_query($conn, $book_query);
                if ($books_result && mysqli_num_rows($books_result) > 0) {
                    while ($book_row = mysqli_fetch_assoc($books_result)) {
                        $book_title = $book_row['Book_Title'];
                        echo "<li class='list-group-item'>$book_title</li>";
                    }
                } else {
                    echo "<li class='list-group-item'>No books found for this author.</li>";
                }
                echo "</ul>
                            </div>
                        </div>
                    </div>";
                } elseif ($target === "Publishers") {
                    $publisher = $row['Publisher_Name'];
                    echo "<div class='accordion-item'>
                            <h2 class='accordion-header' id='heading$publisher'>
                                <button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#collapse$publisher' aria-expanded='false' aria-controls='collapse$publisher'>
                                    <strong>Publisher Name:</strong> $publisher
                                </button>
                            </h2>
                            <div id='collapse$publisher' class='accordion-collapse collapse' aria-labelledby='heading$publisher'>
                                <div class='accordion-body'>
                                    <p><strong>Publisher Name:</strong> $publisher</p>
                                    <p><strong>Books:</strong></p>
                                    <ul class='list-group'>";
                    // Fetch and display the books by the publisher dynamically
                    $book_query = "SELECT Book_Title FROM tbl_books WHERE Publisher_Name = '$publisher'";
                    $books_result = mysqli_query($conn, $book_query);
                    if ($books_result && mysqli_num_rows($books_result) > 0) {
                        while ($book_row = mysqli_fetch_assoc($books_result)) {
                            $book_title = $book_row['Book_Title'];
                            echo "<li class='list-group-item'>$book_title</li>";
                        }
                    } else {
                        echo "<li class='list-group-item'>No books found for this publisher.</li>";
                    }
                    echo "</ul>
                                </div>
                            </div>
                        </div>";
                }
            }
    }
     else {
        // If there's an error in executing the query, return an error message
        echo json_encode(array("error" => "Error: " . mysqli_error($conn)));
    }
} else {
    // If the target is not received, return an error message
    echo json_encode(array("error" => "Target not received."));
}

// Close database connection
mysqli_close($conn);
?>

<script>
    // Add event listeners to accordion buttons
    var accordions = document.querySelectorAll('.accordion');
    accordions.forEach(function(accordion) {
        accordion.addEventListener('click', function() {
            this.classList.toggle('active');
            var panel = this.nextElementSibling;
            var booksList = panel.querySelector('.books-list');
            if (panel.style.display === 'block') {
                panel.style.display = 'none';
                booksList.style.display = 'none'; // Hide books list
            } else {
                panel.style.display = 'block';
                booksList.style.display = 'block'; // Show books list
            }
        });
    });
</script>
