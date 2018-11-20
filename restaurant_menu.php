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
	
	<h2 class="rname"><?php echo $rdetails['name'];?> is <?php echo $rdetails['status'];?></h2>



	<div>
    		<?php
    		$q="SELECT * FROM menu where restaurant_id='$restaurant'; ";
			$q1=mysqli_query($con,$q);
			$rowcount=mysqli_num_rows($q1);
			if ($rowcount>0) {
    		?>	
    		<table><tr><td><b>Name</b></td><td><b>Price</b></td><td><b>Discount</b></td><td><b>Description</b></td><td><b>Quantity</b></td></tr></pre>
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

    
    <input id="coupon_code" type="text" name="coupon_code" placeholder="Enter coupon code" required><button id="coupon">Apply</button>
    <div>
        <div>
            subtotal = ₹<span id="subtotal">0</span>
        </div>
        <div>
            Savings = ₹<span id="savings">0</span>
        </div>
        <div>
            GST = ₹<span id="gst">0</span>
        </div>
        <div>
            total = ₹<span id="total">0</span>
        </div>
    </div>
    </div>
    
    <input id="<?php echo($rdetails['status']=="Online")? 'totl_con':'totl_dis';?>" type="submit" name="confirm" value="Confirm Order">

    <script src="js/restaurant_menu.js" type="text/javascript" ></script>

<script src="js\restaurant_menu.js"></script>
    <script >
        var items_list="";
        var o = new Object();
        var item = 1;
        var subtotal=0;
        var total=0;
        var otp  = Math.floor((Math.random() * 1000) + 1000);
        var savings=0;
        var gst=0;
        function add_item(cur_id){
            var quan=document.getElementById('buy'+cur_id).innerHTML;
            if(quan<10){
                document.getElementById('buy'+cur_id).innerHTML=++quan;
                var name=document.getElementById('name'+cur_id).innerHTML;
                var price=document.getElementById('price'+cur_id).innerHTML;
                var discount=document.getElementById('discount'+cur_id).innerHTML;
                if(quan==1){
                    item++;
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
                subtotal=subtotal+(1*price);
                gst=gst+(0.05*price);
                total=total+price*0.01*(100-discount)+(0.05*price);
                savings=savings+price*0.01*discount;
                document.getElementById('subtotal').innerHTML=1*subtotal;
                document.getElementById('gst').innerHTML=1*gst;
                document.getElementById('total').innerHTML=1*total;
                document.getElementById('savings').innerHTML=1*savings;
                o[cur_id] = quan;
            }
        }

        function remove_item(cur_id){
            var quan=document.getElementById('buy'+cur_id).innerHTML;
            if(quan>0){
                document.getElementById('buy'+cur_id).innerHTML=--quan;
                var name=document.getElementById('name'+cur_id).innerHTML;
                var price=document.getElementById('price'+cur_id).innerHTML;
                var discount=document.getElementById('discount'+cur_id).innerHTML;
                if(quan==0){
                    var objTo = document.getElementById('fin_items'+cur_id).remove(); 
                }
                else{
                    document.getElementById('fin_quan'+cur_id).innerHTML=quan;
                    document.getElementById('fin_price'+cur_id).innerHTML=quan*price;
                    document.getElementById('fin_fin_price'+cur_id).innerHTML=(quan*price*0.01*(100-discount));
                }
                subtotal=subtotal-(1*price);
                    gst=gst-(0.05*price);
                    total=total-price*0.01*(100-discount)-(0.05*price);
                    savings=savings-price*0.01*discount;
                    document.getElementById('subtotal').innerHTML=subtotal;
                    document.getElementById('gst').innerHTML=gst;
                    document.getElementById('total').innerHTML=total;
                    document.getElementById('savings').innerHTML=savings;
                o[cur_id] = quan;
            }
        }
        
        
        $(document).ready(function(){
        $('#totl_con').click(function(){
            for(var i in o){
            if (o[i]>0)
                items_list=items_list+i+" "+o[i]+" ";
            }
            console.log(items_list);
            var delivery_address = $('#delivery_address').val();
            var total = $('#total').html();
            items_list=$.trim(items_list);
            delivery_address=$.trim(delivery_address);
            if(items_list !=''){
                $.ajax({
                    url:"send_order.php",
                    method:"POST",
                    data:{items:items_list,total:total,address:delivery_address,otp:otp},
                    dataType:"text",
                    success:function(data){
                        window.location = "order_status.php";
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