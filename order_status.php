<?php
	session_start();
	if(!isset($_SESSION['log_email'])){
		header("location:index.php");
	}
	$log_email=$_SESSION['log_email'];
	include 'connection.php';


?>
<!DOCTYPE html>
<html>
<head>
	<title>order status</title>
 <link rel="shortcut icon" href="images\logo.png" type="image/png">
    <link rel="stylesheet" type="text/css" href="css\order_status.css">
</head>
<body style="font-family: Helvetica;">
	 <div class="topnav">
        <img src="images/header_logo.jpeg" height= "45px" width = "150px" align="left"></div>


	<?php
        $q="select * from orders where order_by='$log_email';";
        $q1=mysqli_query($con,$q);
    ?>
    <div>
        <?php
        while ($row=mysqli_fetch_array($q1)){
           if($row['status']!="delivered" && $row['status']!="declined"){
            ?>
                <div class="distance">
                <div class="ordercard">
                <div class="ordercardinsidetext">

                    Order ID-<?php echo $row['order_id']; ?>
                    <br>Restaurant:<?php $order_from=$row['order_from'];
                    	echo $order_from; ?>
                    <br>Items:<br><?php 
					$item_list  = preg_split("/ /", $row['items']);
					for($i=0;$i<sizeof($item_list);$i=$i+2){
						$q_itm="SELECT name FROM menu where sno='$item_list[$i]' and restaurant_id='$order_from' ;";
						$q1_itm=mysqli_query($con,$q_itm);
						$row_itm=mysqli_fetch_array($q1_itm);
						echo "<div>&nbsp;&nbsp;".$row_itm['name']." &times; ".$item_list[$i+1]."</div>";
			   		}
                    ?>
                    Total:<?php echo $row['total']; ?>
                    <br>Address:<?php echo $row['address']; ?>
                    <br>Rider:<?php echo $row['rider']; ?>
                    <br>Instance:<?php echo $row['instance']; ?>
                    <br>Status:<?php echo $row['status']; ?>

                <br> 

  </div>
</div> </div>  
        <?php }
        }
        ?>
    


     <div class="navbar">
       
        <a href="confirm_order.php">Confirm Order</a>
        <a href="logout.php">Log out</a>
        
        <div class="copy">&copy; foodly</div>
        </div>
</body>
</html>