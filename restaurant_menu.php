<?php
	session_start();
if(!isset($_SESSION['log_email'])){
	header("location:index.php");
}
include 'connection.php';
$restaurant= $_GET['restaurant'];
$_SESSION['cur_restaurant']=$restaurant;
$q="SELECT * FROM restaurants where email='$restaurant'; ";
$q1=mysqli_query($con,$q);
$rdetails=mysqli_fetch_array($q1);
?>
<!DOCTYPE html>
<html>
<head>
	<title>foodly | <?php echo $rdetails['name'];?></title>
 <link rel="shortcut icon" href="images\logo.png" type="image/png">
    <link rel="stylesheet" type="text/css" href="css\restaurant_menu.css">
    <script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>
</head>
<body style="font-family: Roboto,Arial,sans-serif;">
    <div class="topnav">
        <img src="images/header_logo.jpeg" height= "45px" width = "150px" align="left"></div>

	
	<h2><?php echo $rdetails['name'];?></h2>
    <p><?php echo $rdetails['status'];?></p>
	<p><a href="tel:<?php echo $rdetails['phone'];?>"><?php echo $rdetails['phone'];?></a></p>
	<p><a target="_blank" href="mailto:<?php echo $rdetails['email'];?>"><?php echo $rdetails['email'];?></a></p>
	<p>Address: <?php echo $rdetails['address'];?></p>
	<p>Description: <?php echo $rdetails['description'];?></p>



	<div>
    		<?php
    		$q="SELECT * FROM menu where restaurant_id='$restaurant'; ";
			$q1=mysqli_query($con,$q);
			$rowcount=mysqli_num_rows($q1);
			if ($rowcount>0) {
    		?>	
    		<table><tr><td><b>name</b></td><td><b>price</b></td><td><b>discount</b></td><td><b>description</b></td><td><b>quantity</b></td></tr></pre>
    		<?php
    			while ($row=mysqli_fetch_array($q1)) {
    				$n=$row['sno'];
    				echo "<tr><td><span class='name' id='name".$n."'>".$row['name']."</span></td><td><span class='price' id='price".$n."'>".$row['price']."</span></td><td><span class='discount' id='discount".$n."'>".$row['discount']."</span></td><td><span class='description' id='description".$n."'>".$row['description']."</span></td><td>
    				<button class='addition' onclick='remove_item(".$n.");'>-</button>
    				 <span class='buy' id='buy".$n."'>0</span> 
    				 <button class='addition' onclick='add_item(".$n.")'>+</button>
    				 </td></tr>";
    			} ?>
    		</table>
            <?php 
    		}
    		else{
    			echo "<b>List of items will be displayed here</b>";
    		}	
    		?>
    	
    </div>

    <div id="totl">
    
    <div id="item_fileds">
        
    </div>

    <?php
    /*$gst=0.05*$subtotal;
    $savings=$subtotal-$total;
    $total+=$gst;
    echo "<br>Subtotal = ₹".$subtotal."<br>"."GST = ₹".$gst."<br>";
    echo "Savings = ₹".$savings."</br>";
    echo "<b>Total = ₹".$total."</b>";*/
    ?>
    <form action="order_status.php" method="post">
        <input type="text" name="restaurant" value="<?php echo $restaurant; ?>" hidden>
        <input type="text" name="no_items" value="<?php echo $no_items; ?>" hidden>
        <input type="text" name="items" value="<?php echo $items; ?>" hidden>
        <input type="text" name="total" value="<?php echo $total; ?>" hidden><br>
        <input type="text" name="address" placeholder="Enter delivery address" required><br>
    </form>
    <div>
        <div>
            subtotal
        </div>
        <div>
            gst
        </div>
        <div>
            savings
        </div>
        <div>
            total
        </div>
    </div>
    </div>
    
    <input id="totl_con" type="submit" name="confirm" value="Confirm Order">




    <script >
        var item = 1;
        var items_list="";
        function add_item(cur_id){
            var quan=document.getElementById('buy'+cur_id).innerHTML;
            if(quan<10){
                document.getElementById('buy'+cur_id).innerHTML=++quan;
                var name=document.getElementById('name'+cur_id).innerHTML;
                var price=document.getElementById('price'+cur_id).innerHTML;
                var discount=document.getElementById('discount'+cur_id).innerHTML;
                if(quan==1){
                    item++;
                    items_list=items_list+cur_id+" "+1+" ";
                    var objTo = document.getElementById('item_fileds');
                    var divtest = document.createElement("div");
                    divtest.innerHTML = '<div id=fin_items'+cur_id+'><span>'+name+'</span>: <span >'+price+'</span> &times; <span id=fin_quan'+cur_id+'>1</span>=<span><strike id=fin_price'+cur_id+'>'+price+'</strike> </span><span id=fin_fin_price'+cur_id+'>'+(price*0.01*(100-discount))+'</span></div>';
                    objTo.appendChild(divtest);   
                }
                else{
                    document.getElementById('fin_quan'+cur_id).innerHTML=quan;
                    document.getElementById('fin_price'+cur_id).innerHTML=quan*price;
                    document.getElementById('fin_fin_price'+cur_id).innerHTML=(quan*price*0.01*(100-discount));
                }
            }
        }

          var res = items_list.match(/\d+/g);
        function remove_item(cur_id){
            var quan=document.getElementById('buy'+cur_id).innerHTML;
            if(quan>0){
                document.getElementById('buy'+cur_id).innerHTML=--quan;
                var name=document.getElementById('name'+cur_id).innerHTML;
                var price=document.getElementById('price'+cur_id).innerHTML;
                var discount=document.getElementById('discount'+cur_id).innerHTML;
                if(quan==0){
                    item++;
                    var objTo = document.getElementById('fin_items'+cur_id).remove(); 
                }
                else{
                    document.getElementById('fin_quan'+cur_id).innerHTML=quan;
                    document.getElementById('fin_price'+cur_id).innerHTML=quan*price;
                    document.getElementById('fin_fin_price'+cur_id).innerHTML=(quan*price*0.01*(100-discount));
                }
            }
            console.log(items_list);
        }
        
        
    </script>

    <script>
        $(document).ready(function(){
        $('#totl_con').click(function(){
            var send_msg = $('#send_msg').val();
            if($.trim(send_msg) !=''){
                $.ajax({
                    url:"send-msg.php",
                    method:"POST",
                    data:{msg:send_msg,client:"user"},
                    dataType:"text",
                    success:function(data){
                        $('#send_msg').val("");
                }
            });
            }
        });
    });
    </script>
    <div class="navbar">
       
        <a href="home.php">Home</a>
        <a href="logout.php">Log out</a>
        <a href="#">Past orders</a>
        <div class="copy">&copy; foodly</div>
        </div>

</body>
</html>