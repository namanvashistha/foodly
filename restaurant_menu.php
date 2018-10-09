<?php
	session_start();
if(!isset($_SESSION['log_email'])){
	header("location:index.php");
}
include 'connection.php';
$restaurant= $_GET['restaurant'];

$q="SELECT * FROM restaurants where email='$restaurant'; ";
$q1=mysqli_query($con,$q);
$rdetails=mysqli_fetch_array($q1);
?>
<!DOCTYPE html>
<html>
<head>
	<title>food - <?php echo $rdetails['name'];?></title>
</head>
<body>
	<a href="home.php"><button>home</button></a>
	<a href="logout.php"><button>logout</button></a>
	<h2><?php echo $rdetails['name'];?></h2>
	<p><a href="tel:<?php echo $rdetails['phone'];?>"><?php echo $rdetails['phone'];?></a></p>
	<p><a href="https://www.<?php echo $rdetails['email'];?>"><?php echo $rdetails['email'];?></a></p>
	<p>Address: <?php echo $rdetails['address'];?></p>
	<p>Description: <?php echo $rdetails['description'];?></p>
	<div>
		
    		<table>
    		<?php
    		$q="SELECT * FROM `$restaurant`; ";
			$q1=mysqli_query($con,$q);
			$rowcount=mysqli_num_rows($q1);
			if ($rowcount>0) {
    		?>	
    		<tr><td><b>name</b></td><td><b>price</b></td><td><b>discount</b></td><td><b>description</b></td><td><b>quantity</b></td></tr></pre>
    		<?php
    			while ($row=mysqli_fetch_array($q1)) {
    				echo "<tr><td>".$row['name']."</td><td>".$row['price']."</td><td>".$row['discount']."</td><td>".$row['desc']."</td><td>
    				<button onclick='remove_item()'>-</button>
    				 <span id='count'>0</span> 
    				 <button onclick='add_item()'>+</button>
    				 </td></tr>";
    			}
    		}
    		else{
    			echo "<b>List of items will be displayed here</b>";
    		}	
    		?>
    		</table>
    	
    </div>
    
    <script type="text/javascript">
    	function add_item(){
    		var quan=document.getElementById("count").innerHTML;
    		if(quan<10)
    			document.getElementById("count").innerHTML=++quan;
    	}
    	function remove_item(){
    		var quan=document.getElementById("count").innerHTML;
    		if(quan>0)
    			document.getElementById("count").innerHTML=--quan;
    	}		
    </script>

</body>
</html>