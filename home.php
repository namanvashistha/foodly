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

<style>
body{
	margin: 0px;

}
ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
    background-color: black;
}

li {
    float: left;
}

li a {
    display: block;
    color: white;
    text-align: center;
    text-decoration: none;
}

li a:hover:not(.active) {
    background-color: #111;
}

.active {
    background-color: golden
    text-align: center;

.arrow-down{

    width: 0; 
  height: 0; 
  border-left: 5px solid transparent;
  border-right: 5px solid transparent;
  
  border-top: 5px solid black;

}

</style>

</head>

<body>
<ul>
<li><img src="header_logo.jpeg" align="left" width="100" height="52"></li>
  
</ul>


<center><h4>Restaurants</h4></center>
	

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