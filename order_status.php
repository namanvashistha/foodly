<?php
	session_start();
	if(!isset($_SESSION['log_email'])){
		header("location:index.php");
	}
	$log_email=$_SESSION['log_email'];
	include 'connection.php';
	if(isset($_POST['submit'])){
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
	header('location:order_status.php');
	}
	$q_rdr="UPDATE orders set rider_status='pending' rider =(SELECT MAX(email) from riders where status='Online' and streak IN(SELECT MIN(streak) FROM riders WHERE status='Online' ) ) where rider_status='not allotted' ; ";
	mysqli_query($con,$q_rdr);
?>
<!DOCTYPE html>
<html>
<head>
	<title>order status</title>
	<link rel="shortcut icon" href="logo.png" type="image/png">
</head>
<body style="font-family: Helvetica;">
	<?php
        $q="select * from orders where order_by='$log_email';";
        $q1=mysqli_query($con,$q);
    ?>
    <div>
        <?php
        while ($row=mysqli_fetch_array($q1)){
           if($row['status']!="delivered" && $row['status']!="declined"){
            ?>
                <div>
                    order id:<?php echo $row['order_id']; ?>
                    <br>ordered from:<?php $order_from=$row['order_from'];
                    	echo $order_from; ?>
                    <br>items:<br><?php 
					$item_list  = preg_split("/ /", $row['items']);
					for($i=0;$i<sizeof($item_list);$i=$i+2){
						$q_itm="SELECT name FROM menu where sno='$item_list[$i]' and restaurant_id='$order_from' ;";
						$q1_itm=mysqli_query($con,$q_itm);
						$row_itm=mysqli_fetch_array($q1_itm);
						echo "<div>&nbsp;&nbsp;".$row_itm['name']." X ".$item_list[$i+1]."</div>";
			   		}
                    ?>
                    total:<?php echo $row['total']; ?>
                    <br>address:<?php echo $row['address']; ?>
                    <br>rider:<?php echo $row['rider']; ?>
                    <br>instance:<?php echo $row['instance']; ?>
                    <br>status:<?php echo $row['status']; ?>
                </div>
                <br>    
        <?php }
        }
        ?>
    </div>
</body>
</html>