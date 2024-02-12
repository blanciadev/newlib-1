<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VillaReadHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="./styles.css" rel="stylesheet">
    <link rel="icon" href="./images/lib-icon.png "> 
</head>
<body>
    <div class="main-wrap container-fluid">
        <div class="main-con row ">
            <div class="img-sec col-7">
                <img src="https://villanuevamisor.gov.ph/wp-content/uploads/2022/11/Villanueva-Municipal-Government-Association_LGU_Region-10-1024x692.jpg" alt="Library">
            </div>
            <div class="form-sec col-5">
                <div class="title">
                    <h1><strong>Villa<span>Read</span>Hub</strong></h1>
                <img src="./images/lib-icon.png" alt="lib-icon"/>
                </div>

                <div class="error-con">
                <?php
                    // Check if an error message is passed in the URL
                    if (isset($_GET['error'])) {
                        $error = $_GET['error'];
                        echo "<p class='error-message'>$error</p>";
                    }
                    ?>
                </div>
                <div class="form-con">
                    <form action="auth.php" method="POST">  
                    <label for="uname">User ID</label>
                    <i class='bx bxs-user' ></i><br/> 
                    <input type="text" name="User_ID" id="User_ID" required>
                    <br/><br/>
                    
                    <div class="psw-container">
                    <label for="psw">Password</label>
                    <i class='bx bxs-lock-alt' ></i><br/>
                    <input type="password" name="Password" id="password" required>
                    <i class='bx bx-show' id="show-password"></i>
                    <br/>
                    </div>

                    <a href="./forgot-password/forgot_password.php" class="forgot-pass">Forgot Password?</a><br/><br/>
                    <button class="button" name="login" type="submit" >Login</button>
                    
                    </form>
                </div>
                
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>
 <script>
      
   let showPassword = document.querySelector("#show-password");
    const passwordField = document.querySelector("#password"); 
    
    showPassword.addEventListener("click", function() { 
        if(this.classList.contains("bx-show")){ 
    this.classList.remove("bx-show")
    this.classList.add("bx-hide") 
   }else{
    this.classList.remove("bx-hide")
    this.classList.add("bx-show")  } 
        const type =passwordField.getAttribute("type") === "password" ? "text" : "password";
        passwordField.setAttribute("type",type)
   })
 

 </script>
 
</body>
</html> 