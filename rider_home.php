<?php
session_start();
if(!isset($_SESSION['rider_log_email'])){
	header("location:index.php");
}
include 'connection.php';
$rider_log_email= $_SESSION['rider_log_email'];

if(isset($_POST['line'])){
        $line=$_POST['line'];
        $q="UPDATE riders SET status='$line' where email='$rider_log_email' ;";
        mysqli_query($con,$q);
    }


?>
<!DOCTYPE html>
<html>
<head>
	<title>rider home</title>
	<link rel="shortcut icon" href="logo.png" type="image/png">
</head>
<body>
	<h3><?php echo $_SESSION['rider_log_name'];?></h3>
	<a href="logout.php"><button>logout</button></a><br>
    <?php
        $q="select status from riders where email='$rider_log_email';";
        $q1=mysqli_query($con,$q);
        $row=mysqli_fetch_array($q1);
        echo "You are currently ";
        echo ($row['status'] == 'Go Online') ? 'Online':'Offline';
        ?>
        <form method="post">
            <input type="submit" name="line" value="<?php echo ($row['status'] == 'Go Online') ? 'Go Offline':'Go Online'; ?>" >
        </form> 
       <?php
        $q="select * from orders where order_from='$rider_log_email';";
        $q1=mysqli_query($con,$q);
    ?>
    <br>active orders<br><br>
    <div>
        <?php
        while ($row=mysqli_fetch_array($q1)){
           if($row['status']!="delivered"){
            ?>
                <div>
                    order id:<?php echo $row['order_id']; ?>
                    <br>ordered by:<?php echo $row['order_by']; ?>
                    <br>items:<?php echo $row['rider']; ?>
                    <br>total:<?php echo $row['total']; ?>
                    <br>address:<?php echo $row['address']; ?>
                    <br>rider:<?php echo $row['rider']; ?>
                    <br>status:<?php echo $row['status']; ?>
                    <br>instance:<?php echo $row['instance']; ?>
                </div>
                <br>    
        <?php }
        }
        ?>
    </div>
    <br>past orders
    <div>
        <?php
        while ($row=mysqli_fetch_array($q1)){
            if($row['status']=="delivered"){?>
                <div>
                    order id:
                    ordered by:
                    items:
                    total:
                    instance:
                    address:
                    status:
                </div>    
        <?php }
        }
        ?>
    </div>

</body>
</html>