<?php
  
    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $User_ID = $_POST["User_ID"];
    $Password = $_POST["Password"];
    $error = " ";  
     
    
    $conn =  mysqli_connect("localhost","root","","db_library"); //database connection
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }  else {

        $stmt = $conn->prepare("select * from tbl_employee where User_ID = ?");// lookup data from employees
        $stmt-> bind_param("i", $User_ID);
        $stmt-> execute();
        $stmt_result = $stmt->get_result();

            if($stmt_result->num_rows == 1) { // 2 usertypes
                $row = $stmt_result->fetch_assoc();
                if($row["User_ID"] == $User_ID && $row["Password"] == $Password){ 
                    
                    if($row["Role"] == "Admin"){ 
                        $fname = $row["First_Name"];
                        $lname = $row["Last_Name"];
                        $_SESSION["admin_name"] = $fname ." " .$lname; 
                        $_SESSION["role"] = $row["Role"];
                        setcookie('user', $User_ID, time()+60*60*24*120);
                        header('location:admin/admin_dashboard.php');
                        
                    }elseif($row["Role"] == "Staff"){ 
                        $fname = $row["First_Name"];
                        $lname = $row["Last_Name"];
                        $_SESSION["login"] = true;
                        $_SESSION["staff_name"] = $fname ." " .$lname; 
                        $_SESSION["role"] = $row["Role"]; 
                        setcookie('user', $User_ID, time()+60*60*24*120);
                        header('location:staff/staff_dashboard.php'); 
                    }
                }else{
                     
                    $error = "Invalid Username or Password! Please Try Again";
                    header("Location: index.php?error=" . urlencode($error));
                    exit();  
                }
                  
                
            }else{
                     
                    $error = " Invalid Account! Please Try Again";
                    header("Location: index.php?error=" . urlencode($error));
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
 