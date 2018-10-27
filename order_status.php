<?php
	session_start();
	if(!isset($_SESSION['log_email'])){
		header("location:index.php");
	}
	include 'connection.php';
	if(isset($_POST['submit'])){
	$order_from=$_POST['restaurant'];
	$order_by=$_SESSION['log_email'];
	$items=$_POST['items'];
	$total=$_POST['total'];
	$address=$_POST['address'];
	$q="INSERT INTO `orders` (`order_from` , `order_by`, `items` ,`total`, `address`) VALUES
		('$order_from','$order_by','$items','$total','$address');";
	mysqli_query($con,$q);
	header('location:order_status.php');
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>order status</title>
	<link rel="shortcut icon" href="logo.png" type="image/png">
</head>
<body>
	
	
</body>
</html>