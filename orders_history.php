<?php
session_start();
if(!isset($_SESSION['log_email'])){
	header("location:index.php");
	exit;
}
include 'connection.php';
$email = $_SESSION['log_email'];

$q = mysqli_prepare($con, "SELECT * FROM orders WHERE order_by=? ORDER BY order_id DESC");
mysqli_stmt_bind_param($q, "s", $email);
mysqli_stmt_execute($q);
$res = mysqli_stmt_get_result($q);
$history = array();
while($o = mysqli_fetch_assoc($res)){
	if($o['status'] === 'delivered' || $o['status'] === 'declined') $history[] = $o;
}

function order_item_names($con, $items, $res){
	$out = array();
	$list = preg_split('/ /', trim($items));
	for($i=0;$i+1<count($list);$i+=2){
		$sno = intval($list[$i]); $rf = mysqli_real_escape_string($con,$res);
		$r = mysqli_fetch_assoc(mysqli_query($con,"SELECT name FROM menu WHERE sno='$sno' AND restaurant_id='$rf'"));
		$out[] = (($r['name'] ?? '') ?: 'Item #'.$sno).' &times; '.$list[$i+1];
	}
	return $out;
}
function rest_name($con, $email){
	$r = mysqli_fetch_assoc(mysqli_query($con,"SELECT name FROM restaurants WHERE email='".mysqli_real_escape_string($con,$email)."'"));
	return ($r['name'] ?? '') ?: $email;
}
function existing_rating($con, $order_id){
	$r = mysqli_fetch_assoc(mysqli_query($con,"SELECT stars FROM ratings WHERE order_id=".intval($order_id)));
	return $r ? (int)$r['stars'] : 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Foodly — Order history</title>
	<link rel="shortcut icon" href="images/logo.png" type="image/png">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="stylesheet" href="css/theme.css?v=<?php echo @filemtime('css/theme.css'); ?>">
	<link rel="stylesheet" href="css/app.css?v=<?php echo @filemtime('css/app.css'); ?>">
	<link rel="stylesheet" href="css/history.css?v=<?php echo @filemtime('css/history.css'); ?>">
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
</head>
<body>

	<header class="topbar">
		<div class="wrap">
			<a class="wordmark" href="home.php">foodly<span class="dot">.</span></a>
			<nav class="topbar-nav">
				<a href="home.php">Home</a>
				<a href="order_status.php">Active orders</a>
				<a href="logout.php">Log out</a>
			</nav>
		</div>
	</header>

	<div class="page wrap">
		<div class="page-head">
			<div>
				<span class="eyebrow">Your orders</span>
				<h1>Order history</h1>
				<p class="sub"><?php echo count($history); ?> past orders</p>
			</div>
		</div>

		<?php if(count($history) === 0){ ?>
			<div class="empty">
				<div class="empty-ic">&#129362;</div>
				<h3>No past orders yet</h3>
				<p>Your completed and cancelled orders will live here.</p>
				<a class="btn btn-primary" href="home.php" style="margin-top:1rem;">Order something</a>
			</div>
		<?php } else { ?>
		<div class="hist-list">
			<?php foreach($history as $o){
				$delivered = ($o['status'] === 'delivered');
				$rated = existing_rating($con, $o['order_id']);
			?>
			<article class="hist-card">
				<div class="hist-top">
					<div>
						<div class="hist-rest"><?php echo htmlspecialchars(rest_name($con,$o['order_from'])); ?></div>
						<div class="hist-sub">Order #<?php echo htmlspecialchars($o['order_id']); ?> &middot; <?php echo htmlspecialchars($o['instance']); ?></div>
					</div>
					<span class="hist-status <?php echo $delivered ? 'ok':'no'; ?>"><?php echo $delivered ? 'Delivered' : 'Cancelled'; ?></span>
				</div>
				<div class="hist-items"><?php echo implode(' &middot; ', order_item_names($con,$o['items'],$o['order_from'])); ?></div>
				<div class="hist-foot">
					<span class="hist-total">&#8377;<?php echo htmlspecialchars(floor($o['total'])); ?></span>
					<?php if($delivered){ ?>
						<div class="rate" data-order="<?php echo (int)$o['order_id']; ?>" data-rated="<?php echo $rated; ?>">
							<span class="rate-label"><?php echo $rated ? 'Your rating' : 'Rate this order'; ?></span>
							<div class="stars">
								<?php for($s=1;$s<=5;$s++){ ?>
									<button type="button" class="star <?php echo $s<=$rated?'on':''; ?>" data-v="<?php echo $s; ?>">&#9733;</button>
								<?php } ?>
							</div>
						</div>
					<?php } ?>
				</div>
			</article>
			<?php } ?>
		</div>
		<?php } ?>
	</div>

	<script>
		document.querySelectorAll('.rate').forEach(function(rate){
			var order = rate.dataset.order;
			var stars = rate.querySelectorAll('.star');
			var label = rate.querySelector('.rate-label');
			function paint(n){ stars.forEach(function(st){ st.classList.toggle('on', +st.dataset.v <= n); }); }
			stars.forEach(function(st){
				st.addEventListener('mouseenter', function(){ paint(+st.dataset.v); });
				st.addEventListener('click', function(){
					var v = +st.dataset.v;
					paint(v);
					label.textContent = 'Saving…';
					$.post('submit_rating.php', { order_id: order, stars: v }, function(){ label.textContent = 'Thanks for rating!'; })
						.fail(function(){ label.textContent = 'Could not save'; });
				});
			});
			rate.querySelector('.stars').addEventListener('mouseleave', function(){ paint(+rate.dataset.rated); });
		});
	</script>
</body>
</html>
