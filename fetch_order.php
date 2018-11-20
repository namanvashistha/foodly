<?php
	session_start();
	if(!isset($_SESSION['log_email'])){
		header("location:index.php");
	}
	$log_email=$_SESSION['log_email'];
	include 'connection.php';

        $q="SELECT * from orders where order_by='$log_email' ORDER BY order_id desc;";
        $q1=mysqli_query($con,$q);
        while ($row=mysqli_fetch_array($q1)){
           if($row['status']!="delivered" && $row['status']!="declined"){
            ?>
                <div class="distance">
                    <div class="ordercard">
                        <div class="ordercardinsidetext">
                            Order ID: <?php echo $row['order_id']; ?>
                            <br>Restaurant: <?php $order_from=$row['order_from'];
                    	       echo $order_from; ?>
                            <br>Items: <br><?php 
					       $item_list  = preg_split("/ /", $row['items']);
					       for($i=0;$i<sizeof($item_list);$i=$i+2){
						      $q_itm="SELECT name FROM menu where sno='$item_list[$i]' and restaurant_id='$order_from' ;";
						      $q1_itm=mysqli_query($con,$q_itm);
						      $row_itm=mysqli_fetch_array($q1_itm);
						      echo "<div>&nbsp;&nbsp;".$row_itm['name']." &times; ".$item_list[$i+1]."</div>";
			   		        }
                            ?>
                            Total: <?php echo floor($row['total']); ?>
                            <br>Address: <?php echo $row['address']; ?>
                            <br>Rider: <?php echo $row['rider']; ?>
                            <br>Instance: <?php echo $row['instance']; ?>
                            <br>Status: <?php echo $row['status']; ?>
                            <br>OTP: <?php if($row['status']=="On the way") echo $row['otp'];
                                            else echo "will be available soon"; ?><br> 
                        </div>
                    </div>
                </div>  
        <?php }
        }
        ?>