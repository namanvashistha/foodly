<?php
session_start();
if(!isset($_SESSION['log_email'])){
	header("location:main.php");
}
$con=mysqli_connect("localhost","root","","food");
$q="SELECT email FROM `restaurants`; ";
	

?>
<!DOCTYPE html>
<html>
<head>
	<title>home page</title>
</head>
<body>

</body>
</html>