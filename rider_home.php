<?php
session_start();
if(!isset($_SESSION['rider_log_email'])){
	header("location:index.php");
}
include 'connection.php';
$rider_log_email= $_SESSION['rider_log_email'];

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
        $q="UPDATE orders SET rider_status='delivered' , status='delivered' where order_id='$act_id' ;";
        mysqli_query($con,$q); 
        header('location:rider_home.php');
    }
?>
<!DOCTYPE html>
<html>
<head>
	<title>rider home</title>
	<link rel="shortcut icon" href="logo.png" type="image/png">
</head>
<body style="font-family: Helvetica;">
	<h3><?php echo $_SESSION['rider_log_name'];?></h3>
	<a href="logout.php"><button>logout</button></a><br>
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
    <br>active orders<br><br>
    <div>
        <?php
        while ($row=mysqli_fetch_array($q1)){
           if($row['status']!="delivered" && $row['rider_status']!="declined"){
            ?>
                <div>
                    order id:<?php echo $row['order_id']; ?>
                    <br>ordered by:<?php echo $row['order_by']; ?>
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
                    <br>status:<?php echo $row['rider_status']; ?>
                    <br><form method="post">
                        <input type="text" name="order_id" value="<?php echo $row['order_id']; ?>" hidden>
                        <?php
                        if ($row['status']=="accepted" || $row['status']=="On the way" ){
                        	if ($row['rider_status']=="pending"){ ?>
                        	<input type="submit" name="accept" value="accept"><?php } ?>
                        	<input type="submit" name="decline" value="decline"><?php  
                        	if ($row['rider_status']=="accepted") { ?>
                        	<input type="submit" name="on_the_way" value="Mark as On the way"><?php } 
                        	if($row['rider_status']=="On the way") { ?>
                        	<input type="submit" name="delivered" value="Mark as Delivered"><?php } 
                        } ?>
                </div>
                <br>   
        <?php }
        }
        ?>
    </div>
    <br>past orders
    <div>
        <?php
        $q="SELECT * from orders where rider='$rider_log_email';";
        $q1=mysqli_query($con,$q);
        while ($row=mysqli_fetch_array($q1)){
            if($row['status']=="delivered"){?>
                <div>
                    order id:<?php echo $row['order_id']; ?>
                    <br>ordered by:<?php echo $row['order_by']; ?>
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
                    <br>status:<?php echo $row['rider_status']; ?>
                </div>
                <br>    
        <?php }
        }
        ?>
    </div>

</body>
</html>