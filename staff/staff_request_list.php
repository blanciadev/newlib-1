<?php
session_start();

// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

// Toast Notification HTML
echo '<div class="toastNotif hide">
<div class="toast-content">
    <i class="bx bx-check check"></i>
    <div class="message">
        <span class="text text-1">Success</span>
        <span class="text text-2"></span>
    </div>
</div>
<i class="bx bx-x close"></i>
<div class="progress"></div>
</div>';

echo '<script>
function showToast(messageType, message) {
    var toast = document.querySelector(".toastNotif");
    var progress = document.querySelector(".progress");

    toast.querySelector(".text-1").textContent = messageType;
    toast.querySelector(".text-2").textContent = message;

    if (toast && progress) {
        toast.classList.add("showing");
        progress.classList.add("showing");
        setTimeout(() => {
            toast.classList.remove("showing");
            progress.classList.remove("showing");
        }, 5000);
    } else {
        console.error("Toast elements not found");
    }
}

function closeToast() {
    var toast = document.querySelector(".toastNotif");
    var progress = document.querySelector(".progress");
    toast.classList.remove("showing");
    progress.classList.remove("showing");
}
</script>';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cancelRequestID'])) {
        $requestID = $_POST['cancelRequestID'];

        $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $deleteSql = "DELETE FROM tbl_requestbooks WHERE Request_ID = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("i", $requestID);

        if ($stmt->execute()) {
            echo '<script>showToast("success", "Request Cancelled Successfully");</script>';
        } else {
            echo '<script>showToast("error", "Failed to Cancel Request");</script>';
        }

        $stmt->close();
        $conn->close();
    } else {
        $requestID = $_POST['requestID'];
        $userID = $_POST['userID'];
        $bookTitle = $_POST['bookTitle'];
        $authorsName = $_POST['authorsName'];
        $publisherName = $_POST['publisherName'];
        $price = $_POST['price'];
        $edition = $_POST['edition'];
        $yearPublished = $_POST['yearPublished'];
        $quantity = $_POST['quantity'];

        $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $updateSql = "UPDATE tbl_requestbooks 
                      SET Book_Title=?, Authors_Name=?, Publisher_Name=?, price=?, tb_edition=?, Year_Published=?, Quantity=?
                      WHERE Request_ID=?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("sssdssii", $bookTitle, $authorsName, $publisherName, $price, $edition, $yearPublished, $quantity, $requestID);

        if ($stmt->execute()) {
            echo '<script>showToast("success", "Data Updated Successfully");</script>';
        } else {
            echo '<script>showToast("error", "Failed to Update Data");</script>';
        }

        $stmt->close();
        $conn->close();
    }
}

echo '<script>
document.querySelector(".close").addEventListener("click", closeToast);
</script>';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="./staff.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png">
</head>
<body>
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary">
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <h2>Villa<span>Read</span>Hub</h2> 
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon"/>
        </a><!--header container--> 
        <div class="user-header d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
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
            <?php if (!empty($userData['image_data'])): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else: ?>
                <img src="../images/default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php echo $userData['First_Name'] . "<br/>" . $_SESSION["role"]; ?></span></strong>
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item"> <a href="./staff_dashboard.php" class="nav-link link-body-emphasis"><i class='bx bxs-home'></i>Dashboard</a> </li>
            <li class="nav-item active"> <a href="./staff_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./staff_transaction_dash.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transaction</a> </li>
            <li class="nav-item"> <a href="./staff_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./staff_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <hr>
            <li class="nav-item"> <a href="./staff_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="" data-bs-toggle="modal" data-bs-target="#logOut" class="nav-link link-body-emphasis"><i class='bx bxs-log-out'></i>Log Out</a> </li>
        </ul>
    </div>
    
    <div class="board container-fluid"><!--board container-->
        <div class="header1">
                <div class="text">
                    <div class="back-btn">
                        <a href="./staff_books.php"><i class='bx bx-arrow-back'></i></a>
                    </div>
                    <div class="title">
                        <h2>Request List</h2>
                    </div>
                </div>
                <div class="searchbar">
                    <form action="">
                        <input type="search" id="searchInput" placeholder="Search..." required>
                        <i class='bx bx-search' id="search-icon"></i>
                    </form>
                </div>
        </div>
        <div class="books container-fluid"> 
                <table class="table table-hover table-sm" style="table-layout: auto;"> 
                    <thead class="bg-light sticky-top">
                        <tr>
                            <th>#</th>
                            <th>Staff</th>                 
                            <th>Book Title</th>
                            <th>Author</th>
                            <th>Publisher</th>
                            <th>Price</th>
                            <th>Edition</th>
                            <th>Year Published</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php
                            $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
                            if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                            }

                            $sql = "SELECT Request_ID, User_ID, Book_Title, Authors_Name, Publisher_Name, price, tb_edition, Year_Published, Quantity, tb_status FROM tbl_requestbooks  ORDER BY Request_ID DESC ";
                            $result = $conn->query($sql);

                            while ($row = $result->fetch_assoc()) {
                                echo "<tr data-id='".$row["Request_ID"]."'>
                                        <td>".$row["Request_ID"]."</td>
                                        <td>".$row["User_ID"]."</td>
                                        <td class='book-title'>".$row["Book_Title"]."</td>
                                        <td class='authors-name'>".$row["Authors_Name"]."</td>
                                        <td class='publisher-name'>".$row["Publisher_Name"]."</td>
                                        <td class='price'>".$row["price"]."</td>
                                        <td class='edition'>".$row["tb_edition"]."</td>
                                        <td class='year-published'>".$row["Year_Published"]."</td>
                                        <td class='quantity'>".$row["Quantity"]."</td>
                                        <td class='status'>".$row["tb_status"]."</td>";

                                if ($row["User_ID"] == $_SESSION["User_ID"] && $row["tb_status"] != "Approved" && $row["tb_status"] != "Cancelled") {
                                    echo "<td>
                                            <form action='".$_SERVER['PHP_SELF']."' method='POST'>
                                                <input type='hidden' name='requestID' value='".$row["Request_ID"]."'>
                                                <button type='button' class='btn btn-primary view-details-btn' data-row='" . json_encode($row) . "'><i class='bx bxs-edit'></i></button>
                                            </form>
                                        </td>";
                                } else {
                                    echo "<td></td>"; // Empty cell if condition is not met
                                }
                                echo "</tr>";
                            }

                            $conn->close();
                        ?>

                    </tbody>
                </table> 
        </div>
        <div class="btn-con">
            <a href="./staff_request_form.php" class="btn">Request New Book</a>
        </div>
    </div>

    <!-- Edit Request Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="detailsForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailsModalLabel">Request Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalBody">
                        <!-- Editable fields will be loaded here using JavaScript -->
                    </div>
                    <div class="modal-footer d-flex flex-row justify-content-center">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-danger cancel-request">Cancel Request</button>
                    </div>
                </form>
            </div>
        </div>
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
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.view-details-btn').forEach(button => {
                button.addEventListener('click', function() {
                    var rowData = JSON.parse(this.getAttribute('data-row'));
                    viewDetails(rowData);
                });
            });
    
            function viewDetails(request) {
                var modalBody = document.getElementById("modalBody");
                modalBody.innerHTML = `
                <div class="mb-3">
                
                    <input type="hidden" id="requestID" name="requestID" value="${request.Request_ID}">
                </div>
                <div class="mb-3">
                
                    <input type="hidden" id="userID" name="userID" value="${request.User_ID}">
                </div>
                <div class="mb-3">
                    <label for="bookTitle" class="form-label"><strong>Book Title:</strong></label>
                    <input type="text" class="form-control" id="bookTitle" name="bookTitle" value="${request.Book_Title}">
                </div>
                <div class="mb-3">
                    <label for="authorsName" class="form-label"><strong>Author:</strong></label>
                    <input type="text" class="form-control" id="authorsName" name="authorsName" value="${request.Authors_Name}"readonly>
                </div>
                <div class="mb-3">
                    <label for="publisherName" class="form-label"><strong>Publisher:</strong></label>
                    <input type="text" class="form-control" id="publisherName" name="publisherName" value="${request.Publisher_Name}"readonly>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label"><strong>Price:</strong></label>
                    <input type="text" class="form-control" id="price" name="price" value="${request.price}">
                </div>
                <div class="mb-3">
                    <label for="edition" class="form-label"><strong>Edition:</strong></label>
                    <input type="text" class="form-control" id="edition" name="edition" value="${request.tb_edition}"readonly>
                </div>
                <div class="mb-3">
                    <label for="yearPublished" class="form-label"><strong>Year Published:</strong></label>
                    <input type="text" class="form-control" id="yearPublished" name="yearPublished" value="${request.Year_Published}">
                </div>
                <div class="mb-3">
                    <label for="quantity" class="form-label"><strong>Quantity:</strong></label>
                    <input type="text" class="form-control" id="quantity" name="quantity" value="${request.Quantity}">
                </div>
                `;
                var detailsModal = new bootstrap.Modal(document.getElementById("detailsModal"));
                detailsModal.show();
            }

            var detailsForm = document.getElementById("detailsForm");

            if (detailsForm) {
                detailsForm.addEventListener("submit", function(event) {
                    event.preventDefault(); // Prevent the default form submission
                    var formData = new FormData(this);
                    $.ajax({
                        url: 'staff_request_list.php',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            var detailsModal = bootstrap.Modal.getInstance(document.getElementById("detailsModal"));
                            detailsModal.hide();
                            var requestID = formData.get('requestID');
                            var row = document.querySelector(`tr[data-id="${requestID}"]`);
                            if (row) {
                                row.querySelector('.book-title').textContent = formData.get('bookTitle');
                                row.querySelector('.authors-name').textContent = formData.get('authorsName');
                                row.querySelector('.publisher-name').textContent = formData.get('publisherName');
                                row.querySelector('.price').textContent = formData.get('price');
                                row.querySelector('.edition').textContent = formData.get('edition');
                                row.querySelector('.year-published').textContent = formData.get('yearPublished');
                                row.querySelector('.quantity').textContent = formData.get('quantity');
                                showToast("success", "Data Updated Successfully");
                            } else {
                                console.error("Table row not found");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }
                    });
                });

                document.querySelector('.cancel-request').addEventListener('click', function(event) {
                    var requestID = document.getElementById('requestID').value;
                    if (confirm('Are you sure you want to cancel this request?')) {
                        $.ajax({
                            url: 'staff_request_list.php',
                            type: 'POST',
                            data: { cancelRequestID: requestID },
                            success: function(response) {
                                var detailsModal = bootstrap.Modal.getInstance(document.getElementById("detailsModal"));
                                detailsModal.hide();
                                var row = document.querySelector(`tr[data-id="${requestID}"]`);
                                if (row) {
                                    row.remove();
                                    showToast("success", "Request Cancelled Successfully");
                                } else {
                                    console.error("Table row not found");
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error:', error);
                            }
                        });
                    }
                });
            } else {
                console.error("Details form element not found");
            }
        });
    </script> 
</body>
</html>