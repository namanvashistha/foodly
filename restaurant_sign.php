<?php

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
            header("location:restaurant_home.php");
        }
        else{
            echo "incorrect email or password";
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
            echo "already exist";
        }
        else{
            $q1="INSERT INTO `restaurants` (`name`, `password`, `email`, `phone`, `address`,`description`) VALUES ('$sign_name', '$sign_pass', '$sign_email', '$sign_phone', '$sign_address','$sign_desc');";
            $q3=mysqli_query($con,$q1);
            if($q3){
                $_SESSION['restaurant_log_email'] =$sign_email;
                $_SESSION['restaurant_log_name'] =$sign_name;
                header("location:restaurant_home.php");    
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Main Page</title>
    <link rel="shortcut icon" href="logo.png" type="image/png">
<style type="text/css">
    body{
  margin : 0;
  font-family: Arial, Helvetica, sans-serif;
  background-color:   #D3D3D3 ;
}
.topnav {
  overflow: hidden;
  background-color: black;
}
.img {
}

.topnav a {
  float: left;
  color: #FFDF00;
  text-align: center;
  text-decoration: none;
  font-size: 17px;
  padding: 14px 16px;
}

.topnav a:hover {
  background-color: #FFDF00;
  color: black;
}

.topnav a.active {
  background-color: #4CAF50;
  color: white;
}

.or {
  float: left;
  color: #696969;
  text-align: center;
  text-decoration: none;
  font-size: 17px;
  float: right;
  padding: 14px 16px;

}

input[type=text], input[type=password] {
    width: 100%;
    height: 100%;
    padding: 2px 2px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

button {
    background-color: #404040;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    cursor: pointer;
    width: 100%;
    color: #FFDF00;
}

button:hover {
    opacity: 0.8;
}

.cancelbtn {
    width: auto;
    padding: 10px 18px;
    background-color: #404040;
    color:  ;
}

.imgcontainer {
    text-align: center;
    margin: 24px 0 12px 0;
    position: relative;
}

img.avatar {
    width: 40%;
    border-radius: 50%;
}

.container {
    padding: 16px;
}

span.log_pass {
    text-decoration: none;
    float: right;
    padding-top: 16px;
}

.modal {
    display: none; 
    position: fixed; 
    z-index: 1; 
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%; 
    overflow: auto;
    background-color: rgb(0,0,0);
    background-color: rgba(0,0,0,0.4);
    padding-top: 60px;
}


.modal-content {
    background-color: #fefefe;
    margin: 5% auto 15% auto; 
    border: 1px solid #888;
    width: 40%;
}

.buttons{
  color: #FFDF00;
}

.close {
    position: absolute;
    right: 25px;
    top: 0;
    color: #000;
    font-size: 35px;
    font-weight: bold;
}

.close:hover,.close:focus {
    color: red;
    cursor: pointer;
}

.animate {
    -webkit-animation: animatezoom 0.6s;
    animation: animatezoom 0.6s
}

@-webkit-keyframes animatezoom {
    from {-webkit-transform: scale(0)} 
    to {-webkit-transform: scale(1)}
}
    
@keyframes animatezoom {
    from {transform: scale(0)} 
    to {transform: scale(1)}
}

@media screen and (max-width: 300px) {
    span.log_pass{
        text-decoration: none;
       display: block;
       float: none;
    }
    .cancelbtn {
       width: 100%;
    }
}

.navbar {
  overflow: hidden;
  background-color: black;
  position: fixed;
  bottom: 0;
  width: 100%;
}

.navbar a {
  float: left;
  display: block;
  color:  #FFDF00;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  font-size: 17px;
}

.navbar a:hover {
  background:  #FFDF00;
  color: black;
}

.navbar a.active {
  background-color: #4CAF50;
  color: white;
}

.main {
  padding: 16px;
  margin-bottom: 30px;
}

.for{
  float: left;
  display: block;
  color:  #FFDF00;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  font-size: 17px;
}
.copy{
  float: right;
  display: block;
  color:  #FFDF00;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  font-size: 17px;
}

.restaurant{
    float: left;
  display: block;
  color:  #FFDF00;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  font-size: 17px;
}
</style>
</head>
<body >
   
    <div class="topnav">
        <img src="header_logo.jpeg" height= "45px" width = "150px" align="left">
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

                <button type="submit" name="signup" value="Sign Up">Sign Up</button>
            </div>
        </form>
    </div>

    <div class="navbar">
       
        <a href="index.php">User</a>
        <a href="rider_sign.php">Rider</a>
        <a href="support_sign.php">Chat Support Executive</a>
        <div class="copy">&copy; foodly</div>
        
</body>
</html>