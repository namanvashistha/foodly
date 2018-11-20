<?php
	include 'connection.php';
	session_start();
	$order_from=$_SESSION['cur_restaurant'];
	$order_by=$_SESSION['log_email'];
	$items=$_POST['items'];
	$total=$_POST['total'];
	$address=$_POST['address'];
	$otp=$_POST['otp'];
	$q_rdr="SELECT email from riders where status='Online' and streak IN(SELECT MIN(streak) FROM riders WHERE status='Online' ); ";
	$q1_rdr=mysqli_query($con,$q_rdr);
	$row_rdr=mysqli_fetch_array($q1_rdr);
	$rider=$row_rdr['email'];
	if($rider=="") 
		$q="INSERT INTO `orders` (`order_from` , `order_by`,`rider`,`items` ,`total`, `address`,`rider_status`,`otp`) VALUES
		('$order_from','$order_by','Not Alloted (Refresh Page)','$items','$total','$address','not allotted','$otp');";
	else
		$q="INSERT INTO `orders` (`order_from` , `order_by`,`rider`,`items` ,`total`, `address`,`otp`) VALUES
		('$order_from','$order_by','$rider','$items','$total','$address','$otp');";
	mysqli_query($con,$q);
	
	
	$q_rdr="UPDATE orders set rider_status='pending' rider =(SELECT MAX(email) from riders where status='Online' and streak IN(SELECT MIN(streak) FROM riders WHERE status='Online' ) ) where rider_status='not allotted' ; ";
	mysqli_query($con,$q_rdr);

/*

        $q="SELECT * from orders where order_by='$log_email' ORDER BY order_id desc;";
        $q1=mysqli_query($con,$q);
    
        while ($row=mysqli_fetch_array($q1)){
           	echo $row['order_id'];
           	$order_from=$row['order_from'];
           	echo $order_from;
			$item_list  = preg_split("/ /", $row['items']);
			for($i=0;$i<sizeof($item_list);$i=$i+2){
			$q_itm="SELECT name FROM menu where sno='$item_list[$i]' and restaurant_id='$order_from' ;";
			$q1_itm=mysqli_query($con,$q_itm);
			$row_itm=mysqli_fetch_array($q1_itm);
			//updating in recommend table
			echo "<div>&nbsp;&nbsp;".$row_itm['name']." &times; ".$item_list[$i+1]."</div>";
        }*/
?>