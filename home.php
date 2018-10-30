<?php
session_start();
if(!isset($_SESSION['log_email'])){
	header("location:index.php");
}
include 'connection.php';
$q="SELECT * FROM `restaurants`; ";
$q1=mysqli_query($con,$q);

?>
<!DOCTYPE html>
<html>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<head>
	<link rel="stylesheet" type="text/css" href="css/home.css">
	<title>home page</title>
	<link rel="shortcut icon" href="images/logo.png" type="image/png">
</head>

<body>
<ul>
<li><img src="images\header_logo.jpeg" align="left" width="100" height="52"></li>
</ul>
<center><h2>Restaurants</h2></center>
	
	
    <div class="card">
  <img src="FETCH THE IMAGE WITH PHP" alt="res_photo" style="width:100%">
  <div class="container">
    <h4><b>RESTAURANT NAME</b></h4> 
    <p>Status</p> 
  </div>

		<?php
			while($row=mysqli_fetch_array($q1)){ ?>
				<a href="restaurant_menu.php?restaurant=<?php echo $row['email']; ?>">
				<div class="list">	
				<?php echo $row['name']." ".$row['status'];  ?>
				</div></a>
		<?php } ?>
	</div>


 <div class="navbar">
       
        <a href="#">Past Orders</a>
      
        <a href="logout.php">Log Out</a>
        <div class="copy">&copy; foodly</div>
        </div>
</body>
</html>
