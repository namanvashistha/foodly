<?php
	session_start();
if(!isset($_SESSION['log_email'])){
	header("location:index.php");
	exit;
}
include 'connection.php';
$restaurant= $_GET['restaurant'];
$_SESSION['cur_restaurant']=$restaurant;
$q="SELECT * FROM restaurants where email='$restaurant'; ";
$q1=mysqli_query($con,$q);
$rdetails=mysqli_fetch_array($q1);
$isOnline = ($rdetails['status']=="Online");
$mq = mysqli_query($con,"SELECT * FROM menu where restaurant_id='$restaurant';");
$menu = array();
while($m = mysqli_fetch_array($mq)){ $menu[] = $m; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Foodly | <?php echo htmlspecialchars($rdetails['name']);?></title>
	<link rel="shortcut icon" href="images/logo.png" type="image/png">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="stylesheet" href="css/theme.css?v=<?php echo @filemtime('css/theme.css'); ?>">
	<link rel="stylesheet" href="css/app.css?v=<?php echo @filemtime('css/app.css'); ?>">
	<link rel="stylesheet" href="css/restaurant_menu.css?v=<?php echo @filemtime('css/restaurant_menu.css'); ?>">
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
</head>
<body>

	<!-- ===== top bar ===== -->
	<header class="topbar">
		<div class="wrap">
			<a class="wordmark" href="home.php">foodly<span class="dot">.</span></a>
			<nav class="topbar-nav">
				<a href="home.php">Home</a>
				<a href="order_status.php">Active orders</a>
				<div class="dropdown">
					<button class="dropbtn" onclick="myFunction()">
						<span class="avatar"><?php echo strtoupper(substr($_SESSION['log_name'],0,1)); ?></span>
						<span class="who"><?php echo htmlspecialchars($_SESSION['log_name']); ?></span>
						<span class="caret">&#9662;</span>
					</button>
					<div class="dropdown-content" id="myDropdown">
						<a href="home.php">Home</a>
						<a href="order_status.php">Past orders</a>
						<a href="logout.php">Log out</a>
					</div>
				</div>
			</nav>
		</div>
	</header>

	<!-- ===== restaurant banner ===== -->
	<section class="menu-hero" style="background-image:url('images/8.jpg');">
		<div class="wrap menu-hero-inner">
			<a class="auth-back" href="home.php" style="color:var(--bg);opacity:.9;">&larr; All restaurants</a>
			<h1><?php echo htmlspecialchars($rdetails['name']);?></h1>
			<div class="menu-hero-meta">
				<span class="statebadge <?php echo $isOnline ? 'on':''; ?>"><i class="ping"></i><?php echo $isOnline ? 'Open now' : 'Closed'; ?></span>
				<span class="dotsep">&middot;</span>
				<span><?php echo htmlspecialchars($rdetails['address']);?></span>
			</div>
			<p class="menu-hero-desc"><?php echo htmlspecialchars($rdetails['description']);?></p>
		</div>
	</section>

	<div class="page wrap menu-layout">

		<!-- ===== menu list ===== -->
		<main>
			<h2 class="menu-section-title">Menu</h2>
			<?php if(count($menu)>0){ ?>
				<div class="dish-list">
					<?php foreach($menu as $row){ $n=$row['sno']; $hasDisc = ((int)$row['discount'])>0; ?>
					<article class="dish-item">
						<div class="dish-info">
							<h3 class="dish-name"><span class="name" id="name<?php echo $n;?>"><?php echo htmlspecialchars($row['name']);?></span></h3>
							<p class="dish-desc description" id="description<?php echo $n;?>"><?php echo htmlspecialchars($row['description']);?></p>
							<div class="dish-price">
								&#8377;<span class="price" id="price<?php echo $n;?>"><?php echo htmlspecialchars($row['price']);?></span>
								<span class="discount-tag" <?php echo $hasDisc?'':'hidden';?>><span class="discount" id="discount<?php echo $n;?>"><?php echo htmlspecialchars($row['discount']);?></span>% off</span>
								<?php if(!$hasDisc){ ?><span class="discount" id="discount<?php echo $n;?>" hidden><?php echo htmlspecialchars($row['discount']);?></span><?php } ?>
							</div>
						</div>
						<div class="qty">
							<button type="button" class="qty-btn" onclick="remove_item(<?php echo $n;?>)">&minus;</button>
							<span class="buy" id="buy<?php echo $n;?>">0</span>
							<button type="button" class="qty-btn" onclick="add_item(<?php echo $n;?>)">+</button>
						</div>
					</article>
					<?php } ?>
				</div>
			<?php } else { ?>
				<div class="empty"><div class="empty-ic">&#127869;</div><h3>No items yet</h3><p>This kitchen has not added any dishes.</p></div>
			<?php } ?>
		</main>

		<!-- ===== order summary ===== -->
		<aside class="cart">
			<div class="cart-card">
				<h2 class="cart-title">Your order</h2>
				<div id="item_fileds" class="cart-items"></div>
				<div class="cart-empty" id="cart_empty">Add dishes to start your order.</div>

				<div class="coupon">
					<input id="coupon_code" class="input" type="text" name="coupon_code" placeholder="Coupon code">
					<button id="coupon" class="btn-soft" type="button">Apply</button>
				</div>

				<div class="cart-lines">
					<div><span>Subtotal</span><span>&#8377;<span id="subtotal">0</span></span></div>
					<div><span>Savings</span><span class="save">&minus;&#8377;<span id="savings">0</span></span></div>
					<div><span>GST</span><span>&#8377;<span id="gst">0</span></span></div>
					<div class="cart-total"><span>Total</span><span>&#8377;<span id="total">0</span></span></div>
				</div>

				<div class="field" style="margin-top:1rem;">
					<label for="delivery_address">Delivery address</label>
					<input id="delivery_address" class="input" type="text" placeholder="Where should we deliver?" value="<?php echo isset($_SESSION['log_email'])?'':''; ?>">
				</div>

				<?php if($isOnline){ ?>
					<button id="totl_con" class="btn btn-primary" type="submit" name="confirm" value="Confirm Order">Confirm order</button>
				<?php } else { ?>
					<button id="totl_dis" class="btn btn-primary" type="submit" name="confirm" value="Confirm Order" disabled style="opacity:.55;cursor:not-allowed;">Kitchen is closed</button>
				<?php } ?>
			</div>
		</aside>
	</div>

	<script src="js/restaurant_menu.js"></script>
	<script>
		var items_list="";
		var o = new Object();
		var item = 1;
		var subtotal=0;
		var total=0;
		var otp  = Math.floor((Math.random() * 1000) + 1000);
		var savings=0;
		var gst=0;
		function refreshCartEmpty(){
			var any=false; for(var i in o){ if(o[i]>0){any=true;break;} }
			document.getElementById('cart_empty').style.display = any ? 'none' : 'block';
		}
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
					divtest.className = "cart-line";
					divtest.innerHTML = '<div id=fin_items'+cur_id+' class="cart-line-row"><span class="cl-name">'+name+'</span><span class="cl-qty">&times;<span id=fin_quan'+cur_id+'>1</span></span><span class="cl-price"><strike id=fin_price'+cur_id+'>'+price+'</strike> &#8377;<span id=fin_fin_price'+cur_id+'>'+(price*0.01*(100-discount))+'</span></span></div>';
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
				document.getElementById('subtotal').innerHTML=Math.round(1*subtotal);
				document.getElementById('gst').innerHTML=Math.round(1*gst);
				document.getElementById('total').innerHTML=Math.round(1*total);
				document.getElementById('savings').innerHTML=Math.round(1*savings);
				o[cur_id] = quan;
				refreshCartEmpty();
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
					document.getElementById('fin_items'+cur_id).remove();
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
				document.getElementById('subtotal').innerHTML=Math.round(subtotal);
				document.getElementById('gst').innerHTML=Math.round(gst);
				document.getElementById('total').innerHTML=Math.round(total);
				document.getElementById('savings').innerHTML=Math.round(savings);
				o[cur_id] = quan;
				refreshCartEmpty();
			}
		}

		$(document).ready(function(){
			$('#totl_con').click(function(){
				items_list="";
				for(var i in o){ if (o[i]>0) items_list=items_list+i+" "+o[i]+" "; }
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
						success:function(data){ window.location = "order_status.php"; }
					});
				}
			});
		});
	</script>
</body>
</html>
