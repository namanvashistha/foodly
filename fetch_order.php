<?php
	session_start();
	if(!isset($_SESSION['log_email'])){
		header("location:index.php");
		exit;
	}
	$log_email=$_SESSION['log_email'];
	include 'connection.php';

	$q="SELECT * from orders where order_by='$log_email' ORDER BY order_id desc;";
	$q1=mysqli_query($con,$q);
	$active = array();
	while ($row=mysqli_fetch_array($q1)){
		if($row['status']!="delivered" && $row['status']!="declined") $active[] = $row;
	}

	// status -> step index for the tracker
	function track_step($status){
		switch($status){
			case 'placed':     return 0;
			case 'accepted':   return 1;
			case 'On the way': return 2;
			case 'delivered':  return 3;
			default:           return 0;
		}
	}
	$steps = array('Placed','Accepted','On the way','Delivered');

	if(count($active)===0){
		echo '<div class="empty"><div class="empty-ic">&#128717;</div><h3>No active orders</h3><p>When you place an order it will show up here with live tracking.</p></div>';
	}
	foreach($active as $row){
		$order_from = $row['order_from'];
		$cur = track_step($row['status']);
?>
<article class="track-card">
	<div class="track-head">
		<div>
			<span class="track-id">Order #<?php echo htmlspecialchars($row['order_id']); ?></span>
			<div class="track-from"><?php echo htmlspecialchars($order_from); ?></div>
		</div>
		<span class="statebadge <?php echo ($row['status']=='On the way')?'on':''; ?>"><i class="ping"></i><?php echo htmlspecialchars($row['status']); ?></span>
	</div>

	<ol class="tracker">
		<?php foreach($steps as $i=>$label){ ?>
			<li class="<?php echo $i <= $cur ? 'done' : ''; ?> <?php echo $i == $cur ? 'current' : ''; ?>"><span class="dot"></span><span class="lbl"><?php echo $label; ?></span></li>
		<?php } ?>
	</ol>

	<ul class="track-items">
		<?php
		$item_list = preg_split("/ /", trim($row['items']));
		for($i=0;$i+1<sizeof($item_list);$i=$i+2){
			$q_itm="SELECT name FROM menu where sno='$item_list[$i]' and restaurant_id='$order_from' ;";
			$row_itm=mysqli_fetch_array(mysqli_query($con,$q_itm));
			$nm = ($row_itm['name'] ?? '') ?: 'Item #'.$item_list[$i];
			echo '<li><span>'.htmlspecialchars($nm).'</span><span class="q">&times; '.htmlspecialchars($item_list[$i+1]).'</span></li>';
		}
		?>
	</ul>

	<div class="track-meta">
		<div><span>Total</span><b>&#8377;<?php echo htmlspecialchars(floor($row['total'])); ?></b></div>
		<div><span>Rider</span><b><?php echo htmlspecialchars($row['rider']); ?></b></div>
		<div><span>Address</span><b><?php echo htmlspecialchars($row['address']); ?></b></div>
		<div><span>Placed</span><b><?php echo htmlspecialchars($row['instance']); ?></b></div>
	</div>

	<?php if($row['status']=="On the way"){ ?>
		<div class="track-otp">Share this OTP with your rider <b><?php echo htmlspecialchars($row['otp']); ?></b></div>
	<?php } else { ?>
		<div class="track-otp muted">Delivery OTP appears once your order is on the way.</div>
	<?php } ?>

	<div class="track-map" data-address="<?php echo htmlspecialchars($row['address']); ?>"></div>
	<div class="track-map-cap">Delivering to <?php echo htmlspecialchars($row['address']); ?></div>
</article>
<?php } ?>
