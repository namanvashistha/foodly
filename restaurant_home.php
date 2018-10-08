<?php
session_start();
if(!isset($_SESSION['restaurant_log_email'])){
	header("location:restaurant_sign.php");
}
if(isset($_POST['update'])){
	$con=mysqli_connect("localhost","root","","food");
	$item_name=$_POST['item_name'];
	$item_price=$_POST['item_price'];
	$item_discount=$_POST['item_discount'];
	$item_desc=$_POST['item_desc'];
	$restaurant_log_email= $_SESSION['restaurant_log_email'];
	for ($i=0;$i<sizeof($item_name);$i++){
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