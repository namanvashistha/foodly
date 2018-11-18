<?php
session_start();
if(!isset($_SESSION['restaurant_log_email'])){
	header("location:index.php");
}
include 'connection.php';
$restaurant_log_email= $_SESSION['restaurant_log_email'];
if(isset($_POST['update'])){
	$item_name=$_POST['item_name'];
	$item_price=$_POST['item_price'];
	$item_discount=$_POST['item_discount'];
	$item_desc=$_POST['item_desc'];
	for($i=0;$i<sizeof($item_name);$i++){
		$q="SELECT name from menu where name='$item_name[$i]' and restaurant_id='$restaurant_log_email' ";
		$q1=mysqli_query($con,$q);
		$rowcount=mysqli_num_rows($q1);
		if(empty($item_name[$i]) || empty($item_price[$i]) || empty($item_discount[$i]) || empty($item_desc[$i]) || $rowcount>0) 
            continue;
		$q="INSERT INTO menu (`restaurant_id`,`name`,`price`,`discount`,`description`) VALUES ('$restaurant_log_email','$item_name[$i]', '$item_price[$i]','$item_discount[$i]','$item_desc[$i]');";
		$q1=mysqli_query($con,$q);
    	header('location:restaurant_home.php');
	}	
}
    if(isset($_POST['delete'])){
        $del_name=$_POST['del_name'];
        $q="DELETE FROM menu where restaurant_id='$restaurant_log_email' and name='$del_name' ;";
        mysqli_query($con,$q);
        header('location:restaurant_home.php');
    }
    if(isset($_POST['line'])){
        $line=$_POST['line'];
        $line=($line == 'Go Online') ? 'Online':'Offline';
        $q="UPDATE restaurants SET status='$line' where email='$restaurant_log_email' ;";
        mysqli_query($con,$q);
        header('location:restaurant_home.php');
    }
    if(isset($_POST['accept'])){
        $act_id=$_POST['order_id'];
        $q="UPDATE orders SET status='accepted' where order_id='$act_id' ;";
        mysqli_query($con,$q); 
        header('location:restaurant_home.php');
    }
    if(isset($_POST['decline'])){
        $act_id=$_POST['order_id'];
        $q="UPDATE orders SET status='declined' ,rider_status='declined' where order_id='$act_id' ;";
        mysqli_query($con,$q);
        header('location:restaurant_home.php');
    }
?>
<!DOCTYPE html>
<html>
<head>
	<title>Restaurant Sign Up</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/png">
    <link rel="stylesheet" type="text/css" href="css/restaurant_home.css">
<script src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>
</head>
<body >
<ul class="links_head">
<li><img src="images\header_logo.jpeg" align="left" width="100" height="52"></li>
 <div class="dropdown">
    <button style= "float:right;"   class="dropbtn" onclick="myFunction()"><?php echo $_SESSION['log_name']; ?>
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content" id="myDropdown">
    <a target="_blank" href="mailto:<?php echo $rdetails['email'];?>">Mail</a>
    <a href="tel:<?php echo $rdetails['phone'];?>">Call</a>
    <a href="index.php">Logout</a></div></div>
</ul>

	<h3><?php echo $_SESSION['restaurant_log_name'];?></h3>
    <?php
        $q="select status,wallet from restaurants where email='$restaurant_log_email';";
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
        $q="select * from orders where order_from='$restaurant_log_email';";
        $q1=mysqli_query($con,$q);
    ?>
    <br>active orders<br><br>
    <div>
        <?php
        while ($row=mysqli_fetch_array($q1)){
           if($row['status']!="delivered" && $row['status']!="declined"){
            ?>
                <div>
                    order id:<?php echo $row['order_id']; ?>
                    <br>ordered by:<?php echo $row['order_by']; ?>
                    <br>items:<br><?php 
                    $item_list  = preg_split("/ /", $row['items']);
                    for($i=0;$i<sizeof($item_list);$i=$i+2){
                        $q_itm="SELECT name FROM menu where sno='$item_list[$i]' and restaurant_id='$restaurant_log_email'; ";
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
                    <br><form method="post">
                        <input type="text" name="order_id" value="<?php echo $row['order_id']; ?>" hidden>
                        <?php if ($row['status']=="placed") { ?>
                        <input type="submit" name="accept" value="accept"><?php } ?>
                        <?php if ($row['status']!="declined") { ?>
                        <input type="submit" name="decline" value="decline"><?php } ?>
                    </form>
                </div>
                <br>    
        <?php }
        }
        ?>
    </div>
    <br>past orders
    <div>
        <?php
        $q="select * from orders where order_from='$restaurant_log_email';";
        $q1=mysqli_query($con,$q);
        while ($row=mysqli_fetch_array($q1)){
            if($row['status']=="delivered" || $row['status']=="declined"){ ?>
                <div>
                    order id:<?php echo $row['order_id']; ?>
                    <br>ordered by:<?php echo $row['order_by']; ?>
                    <br>items:<br><?php 
                    $item_list  = preg_split("/ /", $row['items']);
                    for($i=0;$i<sizeof($item_list);$i=$i+2){
                        $q_itm="SELECT name FROM menu where sno='$item_list[$i]' and restaurant_id='$restaurant_log_email' ";
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
	<form method="post" >
       <div id="item_fileds">
           <div>
            <div class='label'>Item 1:</div>
            <div class="content">
                <span>item name:<input type="text" name="item_name[]" /></span>
                <span>Price: <input type="text" name="item_price[]" /></span>
                <span>Discount: <input type="text" name="item_discount[]" required maxlength="3"/></span>
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
    	$q="SELECT * FROM menu where restaurant_id='$restaurant_log_email'; ";
		$q1=mysqli_query($con,$q);
		$rowcount=mysqli_num_rows($q1);
		if ($rowcount>0) {
    	?>	
    	<tr><td><b>name</b></td><td><b>price</b></td><td><b>discount</b></td><td><b>description</b></td></tr></pre>
    	<?php
    			while ($row=mysqli_fetch_array($q1)) {
    				echo "<tr><td>".$row['name']."</td><td>".$row['price']."</td><td>".$row['discount']."</td><td>".$row['description']."</td><td>";?>
                    <form method="post">
                    <input type="text" name="del_name" value="<?php echo $row['name'] ;?>" hidden>
                    <input type="submit" name="delete" value="delete">
                    </form></td></tr>
    		<?php }
    		}
    		else{
    			echo "<b>List of items will be displayed here</b>";
    		}	
    	?>
    	</table>
    </div>
 

        <div class="navbar">
            <a href="logout.php">Logout</a>
        <a href="support_sign.php">Chat Support Executive</a>
        <div class="copy">&copy; foodly</div>
        </div>
        <script src="js/restaurant_home.js"></script>
</body>
</html>