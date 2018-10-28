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
<head>
	<title>home page</title>
	<link rel="shortcut icon" href="logo.png" type="image/png">
</head>
<body style="font-family: Helvetica;">
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