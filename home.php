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
	<link rel="shortcut icon" href="logo.png" type="image/png">
</head>

<body style="font-family: Helvetica;">
<ul>
<li><img src="header_logo.jpeg" align="left" width="100" height="52"></li>
</ul>
<center><h4>Restaurants</h4></center>
	
	<a href="logout.php"><button>logout</button></a>

	<div class="recommendation">
		<?php
			while($row=mysqli_fetch_array($q1)){ ?>
				<a href="restaurant_menu.php?restaurant=<?php echo $row['email']; ?>">
				<div class="list">	
					<?php echo $row['name']." ".$row['status'];  ?>
				</div></a>
		<?php } ?>
	</div>

</body>
</html>
