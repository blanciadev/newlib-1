<?php 
    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $User_ID = $_POST["User_ID"];
        $Password = $_POST["Password"];
        $error = " ";  
         
        $conn =  mysqli_connect("localhost","root","root","db_library_2", 3308); //database connection
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }  else { 
            $stmt = $conn->prepare("select * from tbl_employee where User_ID = ?");// lookup data from employees
            $stmt-> bind_param("i", $User_ID);
            $stmt-> execute();
            $stmt_result = $stmt->get_result();

            if($stmt_result->num_rows == 1) { // 2 usertypes
                $row = $stmt_result->fetch_assoc();
                if($row["User_ID"] == $User_ID && $row["tb_password"] == $Password){ 
                    if ($row["status"] == "Deactivated") {
                        $error = "Your account has been deactivated. Please contact the administrator.";
                        header("Location: index.php?error=" . urlencode($error));
                        exit();
                    } else { 
                        $_SESSION["User_ID"] = $User_ID;
                        if($row["tb_role"] == "Admin"){ 
                            $_SESSION["User_ID"] = $User_ID;
                            $fname = $row["First_Name"];
                            $lname = $row["Last_Name"];
                            $_SESSION["User_ID"] = $User_ID;
                            $_SESSION["admin_name"] = $fname ." " .$lname; 
                        
                            $_SESSION["role"] = $row["tb_role"];
                            header('location:admin/admin_dashboard.php');
                            
                        } elseif($row["tb_role"] == "Staff"){ 
                            $fname = $row["First_Name"];
                            $lname = $row["Last_Name"];
                        
                            $_SESSION["login"] = true;
                            $_SESSION["User_ID"] = $User_ID;
                            $_SESSION["staff_name"] = $fname ." " .$lname; 
                            $_SESSION["role"] = $row["tb_role"]; 
                            header('location: staff/staff_dashboard.php?User_ID=' . $_SESSION['User_ID']);
                        }
                    }
                } else { 
                    $error = "Invalid Username or Password! Please Try Again";
                    header("Location: index.php?error=" . urlencode($error));
                    exit();  
                }  
            } else{   
                    $error = "Invalid Account! Please Try Again";
                    header("Location: index.php?error=" );
                    exit();  
            } 
        } 
    }else{
        if(isset($_SESSION["login"]) == false){
            header('Location: ./index.php');
            exit();
        } 
    }
?> 