<?php
$error_msg="";
if(isset($_POST['login']) || isset($_POST['signup'])){
    include 'connection.php';
    session_start();

    if(isset($_POST['login'])){
        $log_email =$_POST['log_email'];
        $log_pass  =$_POST['log_pass'];
        $q="SELECT name,password from restaurants where email='$log_email'; ";
        $q1=mysqli_query($con,$q);
        $row=mysqli_fetch_array($q1);
        if($row['password'] == $log_pass){
            $_SESSION['restaurant_log_name'] =$row['name'];
            $_SESSION['restaurant_log_email'] =$log_email;
            $_SESSION['log_client'] ="restaurant";
            header("location:restaurant_home.php");
        }
        else{
            $error_msg="incorrect email or password";
        }
    }
    else if(isset($_POST['signup'])){
        $sign_name    =$_POST['sign_name'];
        $sign_pass    =$_POST['sign_pass'];
        $sign_email   =$_POST['sign_email'];
        $sign_phone   =$_POST['sign_phone'];
        $sign_address =$_POST['sign_address'];
        $sign_desc    =$_POST['sign_desc'];
        $q2="SELECT email from restaurants where email='$sign_email' ";
        $row=mysqli_query($con,$q2);
        $rowcount=mysqli_num_rows($row);
        if($rowcount>0){
            $error_msg= "email already exists";
        }
        else{
            $q1="INSERT INTO `restaurants` (`name`, `password`, `email`, `phone`, `address`,`description`) VALUES ('$sign_name', '$sign_pass', '$sign_email', '$sign_phone', '$sign_address','$sign_desc');";
            $q3=mysqli_query($con,$q1);
            if($q3){
                $_SESSION['restaurant_log_email'] =$sign_email;
                $_SESSION['restaurant_log_name'] =$sign_name;
                $_SESSION['log_client'] ="restaurant";
                header("location:restaurant_home.php");    
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="shortcut icon" href="images/logo.png" type="image/png">
    <link rel="stylesheet" type="text/css" href="css/restaurant_sign.css">
    <title>Restaurant Main Page</title>

</head>
<body >
   
    <div class="topnav">
        <img src="images/header_logo.jpeg" height= "45px" width = "110px" align="left">
          <div class="restaurant">for restaurants</div>
        <a style="float:right;" onclick="document.getElementById('id02').style.display='block'" style="width:auto;" >Sign up</a>
        <div class="or">or</div>
        <a style="float:right;" onclick="document.getElementById('id01').style.display='block'" style="width:auto;">Login</a>
    </div>

    <div id="id01" class="modal">
        <form class="modal-content animate" method="POST" >
            <div class="imgcontainer">
                <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">&times;</span>
            </div>
            <div class="container">
                <h3><center>Restaurant</center></h3>
                <label for="log_email"><b>Email</b></label>
                <input type="text" placeholder="Enter Email" name="log_email" required>

                <label for="log_pass"><b>Password</b></label>
                <input type="password" placeholder="Enter Password" name="log_pass" required>
        <div id="log_error_msg" class="error_msg"><?php if($error_msg=="incorrect email or password") echo $error_msg; ?></div>
                <button type="submit" name="login" value="login">Login</button>
            </div>
        </form>
    </div>

    <div id="id02" class="modal">
        <form class="modal-content animate" method="POST" >
            <div class="imgcontainer">
                <span onclick="document.getElementById('id02').style.display='none'" class="close" title="Close Modal">&times;</span>
            </div>

            <div class="container">
                 <h3><center>Restaurant</center></h3>
                <label for="sign_name"><b>Name</b></label>
                <input type="text" placeholder="Enter Name" name="sign_name" required>


                
                <label for="sign_email"><b>Email</b></label>
                <input type="text" placeholder="Enter Email" name="sign_email" required>

                
                <label for="sign_pass"><b>Password</b></label>
                <input type="password" placeholder="Enter Password" name="sign_pass" required>

                <label for="sign_phone"><b>Phone</b></label>
                <input type="text" placeholder="Enter Phone" name="sign_phone" required>

                <label for="sign_address"><b>Address</b></label>
                <input type="text" placeholder="Enter Address" name="sign_address" required>
            

                <label for="sign_desc"><b>Description</b></label>
                <input type="text" placeholder="Enter Description" name="sign_desc" required>
                    <div id="sign_error_msg" class="error_msg"><?php if($error_msg=="email already exists") echo $error_msg; ?></div>
                <button type="submit" name="signup" value="Sign Up">Sign Up</button>
            </div>
        </form>
    </div>
<center><video height="300" width="300" autoplay="" loop=""><source src="https://d3i4yxtzktqr9n.cloudfront.net/web-eats/static/videos/header_animation-c22df1758f.mp4" type="video/mp4"></video></center>

    <div class="navbar">
       
        <a href="index.php">User</a>
        <a href="rider_sign.php">Rider</a>
        <a href="support_sign.php">Chat Support Executive</a>
        <div class="copy">&copy; foodly</div>
        </div>
        <script src="js/restaurant_sign.js"></script>
</body>
</html>