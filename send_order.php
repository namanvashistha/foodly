<?php
	include 'connection.php';
	session_start();
	$order_from=$_POST['restaurant'];
	$order_by=$_SESSION['log_email'];
	$items=$_POST['items'];
	$total=$_POST['total'];
	$address=$_POST['address'];
	$q_rdr="SELECT email from riders where status='Online' and streak IN(SELECT MIN(streak) FROM riders WHERE status='Online' ); ";
	$q1_rdr=mysqli_query($con,$q_rdr);
	$row_rdr=mysqli_fetch_array($q1_rdr);
	$rider=$row_rdr['email'];
	if($rider=="") 
		$q="INSERT INTO `orders` (`order_from` , `order_by`,`rider`,`items` ,`total`, `address`,`rider_status`) VALUES
		('$order_from','$order_by','Not Alloted (Refresh Page)','$items','$total','$address','not allotted');";
	else
		$q="INSERT INTO `orders` (`order_from` , `order_by`,`rider`,`items` ,`total`, `address`) VALUES
		('$order_from','$order_by','$rider','$items','$total','$address');";
	mysqli_query($con,$q);
	
	
	$q_rdr="UPDATE orders set rider_status='pending' rider =(SELECT MAX(email) from riders where status='Online' and streak IN(SELECT MIN(streak) FROM riders WHERE status='Online' ) ) where rider_status='not allotted' ; ";
	mysqli_query($con,$q_rdr);

?>