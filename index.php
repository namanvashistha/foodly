<?php
include 'connection.php';
	$ipaddress = '';
	if($_SERVER['REMOTE_ADDR'])
		$ipaddress = $_SERVER['REMOTE_ADDR'];
   	else if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
	$q="INSERT INTO `stats` (`ip_address`, `coordinates`,`city`) VALUES ('$ipaddress','','');";
    mysqli_query($con,$q);
$error_msg="";
if(isset($_POST['login']) || isset($_POST['signup'])){
	session_start();

	if(isset($_POST['login'])){
		$log_email =$_POST['log_email'];
		$log_pass  =$_POST['log_pass'];
		$q="SELECT name,password from users where email='$log_email'; ";
		$q1=mysqli_query($con,$q);
		$row=mysqli_fetch_array($q1);
		if($row['password'] == $log_pass){
			$_SESSION['log_email'] =$log_email;
			$_SESSION['log_name'] =$row['name'];
			$_SESSION['log_client'] ="user";
			$q_ip="INSERT INTO `stats` (`ip_address`, `coordinates`,`city`,`client`,`status`) VALUES ('$ipaddress','','','$log_email','login');";
    		mysqli_query($con,$q_ip);
			header("location:home.php");
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
		$q2="SELECT email from users where email='$sign_email' ";
		$row=mysqli_query($con,$q2);
		$rowcount=mysqli_num_rows($row);
		if($rowcount>0){
			$error_msg= "email already exists";
		}
		else{
			$q="INSERT INTO `users` (`name`, `password`, `email`, `phone`, `address`) VALUES ('$sign_name', '$sign_pass', '$sign_email', '$sign_phone', '$sign_address');";
			$q1=mysqli_query($con,$q);
			if($q1){
				$_SESSION['log_email'] =$sign_email;
				$_SESSION['log_name'] =$sign_name;
				$_SESSION['log_client'] ="user";
				$q_ip="INSERT INTO `stats` (`ip_address`, `coordinates`,`city`,`client`,`status`) VALUES ('$ipaddress','','','$log_email','signup');";
    			mysqli_query($con,$q_ip);
				header("location:home.php");	
			}
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>foodly</title>
	<link rel="shortcut icon" href="images/logo.png" type="image/png">
	<link rel="stylesheet" type="text/css" href="css/index.css">
</head>

<body background="images/res.jpg">
	<div class="topnav">
  		<img src="images/header_logo.jpeg" height= "45px" width = "110px" align="left">
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
				use test login:<br>username: admin<br>password: admin<br>
      			<label for="log_email"><b>Username</b></label>
      			<input type="text" placeholder="Enter Username" name="log_email" required>

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
				<div id="sign_error_msg" class="error_msg"><?php if($error_msg=="email already exists") echo $error_msg; ?></div>
			    <button type="submit" name="signup" value="Sign Up">Sign Up</button>
    		</div>
		</form>
	</div>
	<br><br><br><br><br><br><br><br><b><h1 style="color: black;text-align: center; ">FOODLY</h1></b>
	<strong><center><h3 style="color: black;">Fast,fresh and instant
 food a click away from your pick</h3></center></strong>
 <center><div class="dist">
 	<a href="https://github.com/namanvashistha/foodly" style="   background-color: black;
 	color: #FFDF00; 
    border: none;
    padding: 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 2px;
    ">
    Fork us on github
    </a>
    <a href="mailto:support@foodly.ml" style="   background-color:black ;

    border: none;
    color: #FFDF00; 
    padding: 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 8px;">
Mail Us
</a>
</div></center>
	<div class="navbar">
  		<div class="for">For Business</div>
  		<a href="restaurant_sign.php">Restaurant</a>
  		<a href="rider_sign.php">Rider</a>
  		<a href="support_sign.php">Chat Support Executive</a>
  		<div class="copy">&copy; foodly</div>
  		<a style="float:right;" href="">About Us</a>
  		<a style="float:right;" href="">FAQs</a>
  		<a style="float:right;" href="food_donation.php">FOOD DONATION</a>
  	</div>
  	<script src="js/index.js"></script>
</body>
</html>