<?php
session_start();
if(!isset($_SESSION['restaurant_log_email'])){
	header("location:main.php");
}
$con=mysqli_connect("localhost","root","","food");
$restaurant_log_email= $_SESSION['restaurant_log_email'];
if(isset($_POST['update'])){
	$item_name=$_POST['item_name'];
	$item_price=$_POST['item_price'];
	$item_discount=$_POST['item_discount'];
	$item_desc=$_POST['item_desc'];
	for ($i=0;$i<sizeof($item_name);$i++){
		$q="SELECT name from `$restaurant_log_email` where name='$item_name[$i]' ";
		$q1=mysqli_query($con,$q);
		$rowcount=mysqli_num_rows($q1);
		if(empty($item_name[$i]) || empty($item_price[$i]) || empty($item_discount[$i]) || empty($item_desc[$i]) || $rowcount>0) continue;
		$q="INSERT INTO `$restaurant_log_email` (`name`,`price`,`discount`,`desc`) VALUES ('$item_name[$i]', '$item_price[$i]','$item_discount[$i]','$item_desc[$i]');";
		$q1=mysqli_query($con,$q);
    	
	}
		
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Restaurant Sign Up</title>
</head>
<body>
	<h3><?php echo $_SESSION['restaurant_log_name'];?></h3>
	
	<form method="post" >
       <div id="item_fileds">
           <div>
            <div class='label'>Item 1:</div>
            <div class="content">
                <span>item name:<input type="text" name="item_name[]" /></span>
                <span>Price: <input type="text" name="item_price[]" /></span>
                <span>Discount: <input type="text" name="item_discount[]" required /></span>
                <span>Description: <input type="text" name="item_desc[]" /></span>
            </div>
           </div>
        </div>
        <input type="button" id="more_fields" onclick="add_fields();" value="+"/><br>
       	<input type="submit" name="update" value="Update">
    </form>

    <div>
    	<table>
    	<?php
    	$q="SELECT * FROM `$restaurant_log_email`; ";
		$q1=mysqli_query($con,$q);
		$rowcount=mysqli_num_rows($q1);
		if ($rowcount>0) {
    	?>	
    	<tr><td><b>name</b></td><td><b>price</b></td><td><b>discount</b></td><td><b>description</b></td></tr></pre>
    	<?php
    			while ($row=mysqli_fetch_array($q1)) {
    				echo "<tr><td>".$row['name']."</td><td>".$row['price']."</td><td>".$row['discount']."</td><td>".$row['desc']."</td></tr>";
    			}
    		}
    		else{
    			echo "<b>List of items will be displayed here</b>";
    		}	
    	?>
    	</table>
    </div>
    <script type="text/javascript">    
    	var item = 1;
		function add_fields() {
   			item++;
   			var objTo = document.getElementById('item_fileds');
   			var divtest = document.createElement("div");
   			divtest.innerHTML = '<div class="label">Item ' + item +':</div><div class="content"><span>item name:<input type="text" name="item_name[]"/></span> <span>Price: <input type="text" name="item_price[]" /><span> Discount: <input type="text" name="item_discount[]" /></span></span> <span>Description: <input type="text" name="item_desc[]" /></span></div>';
    		objTo.appendChild(divtest);
		}
    </script>
</body>
</html>