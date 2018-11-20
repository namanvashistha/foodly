<?php
session_start();
if(!isset($_SESSION['rider_log_email'])){
	header("location:index.php");
}
include 'connection.php';
$rider_log_email= $_SESSION['rider_log_email'];
$error="";
	if(isset($_POST['line'])){
        $line=$_POST['line'];
        $line=($line == 'Go Online') ? 'Online':'Offline';
        $q="UPDATE riders SET status='$line' where email='$rider_log_email' ;";
        mysqli_query($con,$q);
        header('location:rider_home.php');
    }

 	if(isset($_POST['accept'])){
        $act_id=$_POST['order_id'];
        $q="UPDATE orders SET rider_status='accepted' where order_id='$act_id' ;";
        mysqli_query($con,$q); 
        header('location:rider_home.php');
    }
    if(isset($_POST['decline'])){
        $act_id=$_POST['order_id'];
        $q_rdr="SELECT email from riders where status='Online' and streak IN(SELECT MIN(streak) FROM riders WHERE status='Online');";
		$q1_rdr=mysqli_query($con,$q_rdr);
		$row_rdr=mysqli_fetch_array($q1_rdr);
		$rider=$row_rdr['email'];
		$q_rdr="UPDATE orders SET rider_status='pending' , rider='$rider' where order_id='$act_id' ;";
		mysqli_query($con,$q_rdr);
		header('location:rider_home.php');
    }
    if(isset($_POST['on_the_way'])){
        $act_id=$_POST['order_id'];
        $q="UPDATE orders SET rider_status='On the way' , status='On the way' where order_id='$act_id' ;";
        mysqli_query($con,$q);
        header('location:rider_home.php'); 
    }
    if(isset($_POST['delivered'])){
        $act_id=$_POST['order_id'];
        $otp=$_POST['otp'];
        $q="SELECT otp from orders where order_id='$act_id' ;";
        $q1=mysqli_query($con,$q);
        $row=mysqli_fetch_array($q1);
        if($otp==$row['otp']){ 
            $q="UPDATE orders SET rider_status='delivered' , status='delivered' where order_id='$act_id' ;";
            mysqli_query($con,$q); 
            header('location:rider_home.php');
        }
        else{
            $error="wrong otp";
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
	<title>rider home</title>
	<link rel="shortcut icon" href="images\logo.png" type="image/png">
    <link rel="stylesheet" type="text/css" href="css\rider_home.css">
</head>
<body style="font-family: Helvetica;">
   <div class="topnav">
        <img src="images/header_logo.jpeg" height= "45px" width = "110px" align="left"></div>


	<h3><?php echo $_SESSION['rider_log_name'];?></h3>
	
    <?php
        $q="SELECT wallet,status from riders where email='$rider_log_email';";
        $q1=mysqli_query($con,$q);
        $row=mysqli_fetch_array($q1);
        echo "You are currently ";
        echo ($row['status'] == 'Online') ? 'Online':'Offline';
        ?>
        <form method="post">
            <input type="submit" name="line" value="<?php echo ($row['status'] == 'Online') ? 'Go Offline':'Go Online'; ?>" >
        </form> 
       <?php
       	echo "Your pending payment is ₹".$row['wallet'];
        $q="SELECT * from orders where rider='$rider_log_email';";
        $q1=mysqli_query($con,$q);
    ?>
    <br><BR><br><div class="active_orders">Active Orders</div>
    <div>
        <?php
        while ($row=mysqli_fetch_array($q1)){
           if($row['status']!="delivered" && $row['rider_status']!="declined"){
            ?>
                 <div class="distance">
                    <div class="ordercard">
                        <div class="ordercardinsidetext">
                    Order ID:<?php echo $row['order_id']; ?>
                    <br>Customer Email:<?php echo $row['order_by']; ?>
                    <br>Restaurant Email:<?php $order_from=$row['order_from'];
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
                    
                    <br>Rider Email:<?php echo $row['rider']; ?>
                    <br>Instance:<?php echo $row['instance']; ?>
                    <br>Status:<?php echo $row['rider_status']; ?>
                    <br><form method="post">
                        <input type="text" name="order_id" value="<?php echo $row['order_id']; ?>" hidden>
                        <?php
                        if ($row['status']=="accepted" || $row['status']=="On the way" ){
                        	if ($row['rider_status']=="pending"){ ?>
                        	<input type="submit" name="accept" value="accept"><?php } ?>
                        	<input type="submit" name="decline" value="decline"><br><?php  
                        	if ($row['rider_status']=="accepted") { ?>
                        	<input type="submit" name="on_the_way" value="Mark as On the way"><?php } 
                        	if($row['rider_status']=="On the way") { ?>
                            <input type="text" name="otp" placeholder="Enter OTP" ><br>
                            <?php if($error=="wrong otp") echo "wrong OTP"; ?>
                        	<input type="submit" name="delivered" value="Mark as Delivered"><?php } 
                        } ?>
                </div></div></div>
                <br>   
        <?php }
        }
        ?>
    </div>
    <br><div class="active_orders">Past Orders</div>
    
        <?php
        $q="SELECT * from orders where rider='$rider_log_email';";
        $q1=mysqli_query($con,$q);
        while ($row=mysqli_fetch_array($q1)){
            if($row['status']=="delivered"){?>
                 <div class="distance">
                    <div class="ordercard">
                        <div class="ordercardinsidetext">
                    Order ID:<?php echo $row['order_id']; ?>
                    <br>Customer Email:<?php echo $row['order_by']; ?>
                    <br>Restaurant Email:<?php $order_from=$row['order_from'];
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
                    
                    <br>Rider Email:<?php echo $row['rider']; ?>
                    <br>Instance:<?php echo $row['instance']; ?>
                    <br>Status:<?php echo $row['rider_status']; ?>
                </div></div></div>
                <br>    
        <?php }
        }
        ?>
    </div>

<br><br><br><br>

<div class="navbar">
       
        <a href="logout.php">Log out</a>
        <a href="#">Active orders</a>
        <a href="#">Past orders</a>
        <div class="copy">&copy; foodly</div>
        </div>
</html>