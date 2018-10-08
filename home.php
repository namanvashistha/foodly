<?php
session_start();
if(!isset($_SESSION['log_email'])){
	header("location:index.php");
}
$con=mysqli_connect("localhost","root","","food");
$q="SELECT name,email FROM `restaurants`; ";
$q1=mysqli_query($con,$q);
$q1=mysqli_fetch_array();

?>
<!DOCTYPE html>
<html>
<head>
	<title>home page</title>
</head>
<body>

</body>
</html>