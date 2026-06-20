<?php
session_start();
if(!isset($_SESSION['rider_log_email'])){
	header("location:index.php");
	exit;
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
        $q="UPDATE orders SET rider_status='On the way' , status='On the way', otw_at=NOW() where order_id='$act_id' ;";
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

// ----- data for the view -----
$rd = mysqli_fetch_array(mysqli_query($con,"SELECT * from riders where email='$rider_log_email';"));
$isOnline = ($rd['status'] == 'Online');
$oq = mysqli_query($con,"SELECT *, TIMESTAMPDIFF(SECOND, otw_at, NOW()) AS otw_elapsed from orders where rider='$rider_log_email' ORDER BY order_id DESC;");
$orders = array();
while($o = mysqli_fetch_array($oq)){ $orders[] = $o; }
$active = array(); $past = array();
foreach($orders as $o){
    if($o['status']!="delivered" && $o['rider_status']!="declined") $active[] = $o;
    if($o['status']=="delivered") $past[] = $o;
}
// the delivery to show on the live map (prefer one already on the way)
$focus = null;
foreach($active as $o){
    if($o['r_lat'] !== null && $o['d_lat'] !== null){ $focus = $o; if($o['status']=='On the way') break; }
}
function order_items($con, $items, $res){
    $out = array();
    $list = preg_split("/ /", trim($items));
    for($i=0;$i+1<sizeof($list);$i=$i+2){
        $sno = $list[$i];
        $r = mysqli_fetch_array(mysqli_query($con,"SELECT name FROM menu where sno='$sno' and restaurant_id='$res'"));
        $out[] = array($r['name'] ?: 'Item #'.$sno, $list[$i+1]);
    }
    return $out;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Foodly — Rider dashboard</title>
	<link rel="shortcut icon" href="images/logo.png" type="image/png">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="stylesheet" href="css/theme.css?v=<?php echo @filemtime('css/theme.css'); ?>">
	<link rel="stylesheet" href="css/app.css?v=<?php echo @filemtime('css/app.css'); ?>">
	<link rel="stylesheet" href="css/rider_home.css?v=<?php echo @filemtime('css/rider_home.css'); ?>">
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
	<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body>

	<header class="topbar">
		<div class="wrap">
			<a class="wordmark" href="rider_home.php">foodly<span class="dot">.</span><span class="role">Rider</span></a>
			<nav class="topbar-nav">
				<div class="dropdown">
					<button class="dropbtn" onclick="myFunction()">
						<span class="avatar"><?php echo strtoupper(substr($rd['name'],0,1)); ?></span>
						<span class="who"><?php echo htmlspecialchars($rd['name']); ?></span>
						<span class="caret">&#9662;</span>
					</button>
					<div class="dropdown-content" id="myDropdown">
						<a href="tel:<?php echo htmlspecialchars($rd['phone']);?>">Call</a>
						<a href="logout.php">Log out</a>
					</div>
				</div>
			</nav>
		</div>
	</header>

	<div class="page wrap">
		<div class="page-head">
			<div>
				<span class="eyebrow">Rider dashboard</span>
				<h1><?php echo htmlspecialchars($rd['name']); ?></h1>
				<p class="sub"><?php echo htmlspecialchars($rd['email']); ?></p>
			</div>
			<form method="post">
				<button class="btn <?php echo $isOnline ? 'btn-soft' : 'btn-primary'; ?>" type="submit" name="line" value="<?php echo $isOnline ? 'Go Offline':'Go Online'; ?>">
					<?php echo $isOnline ? 'Go offline' : 'Go online'; ?>
				</button>
			</form>
		</div>

		<div class="stat-row">
			<div class="stat-tile"><div class="k">Status</div><div class="v"><span class="statebadge <?php echo $isOnline ? 'on':''; ?>"><i class="ping"></i><?php echo $isOnline ? 'Online' : 'Offline'; ?></span></div></div>
			<div class="stat-tile"><div class="k">Pending payout</div><div class="v">&#8377;<?php echo htmlspecialchars($rd['wallet']); ?></div></div>
			<div class="stat-tile"><div class="k">Streak</div><div class="v"><?php echo htmlspecialchars($rd['streak']); ?></div></div>
			<div class="stat-tile"><div class="k">Active deliveries</div><div class="v"><?php echo count($active); ?></div></div>
		</div>

		<?php if($focus){ ?>
		<section class="panel">
			<div class="panel-title">Live delivery</div>
			<p class="panel-sub">Order #<?php echo htmlspecialchars($focus['order_id']); ?> &middot; <?php echo $focus['status']=='On the way' ? 'heading to the customer' : 'pick up from the restaurant'; ?></p>
			<div id="rider-map" style="height:340px;border-radius:var(--r-md);overflow:hidden;border:1px solid var(--border);z-index:0;"></div>
		</section>
		<script>
			(function(){
				var R = [<?php echo $focus['r_lat']; ?>, <?php echo $focus['r_lng']; ?>];
				var D = [<?php echo $focus['d_lat']; ?>, <?php echo $focus['d_lng']; ?>];
				var status = <?php echo json_encode($focus['status']); ?>;
				var elapsed0 = <?php echo $focus['otw_elapsed'] !== null ? (int)$focus['otw_elapsed'] : 'null'; ?>;
				var travel = <?php echo (int)(defined('TRAVEL_SECONDS')?TRAVEL_SECONDS:120); ?>;
				var t0 = performance.now();
				var map = L.map('rider-map', { zoomControl: true, attributionControl: false });
				L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { subdomains:'abcd', maxZoom:20 }).addTo(map);
				function mk(kind){ var g = { rest:'&#127869;', home:'&#127968;' }; return L.divIcon({ className:'mk mk-'+kind, html:'<div class="mk-in" style="font-size:15px">'+g[kind]+'</div>', iconSize:[30,30], iconAnchor:[15,15] }); }
				L.polyline([R,D], { className:'route-line', weight:3, opacity:0.7, dashArray:'4 7' }).addTo(map);
				L.marker(R, { icon: mk('rest') }).addTo(map);
				L.marker(D, { icon: mk('home') }).addTo(map);
				var me = L.marker(R, { icon: L.divIcon({ className:'mk mk-rider', html:'<div class="mk-in"></div>', iconSize:[26,26], iconAnchor:[13,13] }), zIndexOffset:1000 }).addTo(map);
				map.fitBounds(L.latLngBounds([R,D]).pad(0.4));
				setTimeout(function(){ map.invalidateSize(); }, 200);
				function frame(){
					if(status === 'On the way' && elapsed0 !== null){
						var e = elapsed0 + (performance.now()-t0)/1000;
						var p = Math.max(0, Math.min(1, e/travel));
						me.setLatLng([ R[0]+(D[0]-R[0])*p, R[1]+(D[1]-R[1])*p ]);
					} else { me.setLatLng(R); }
					requestAnimationFrame(frame);
				}
				requestAnimationFrame(frame);
			})();
		</script>
		<?php } ?>

		<section class="panel">
			<div class="panel-title">Active deliveries</div>
			<p class="panel-sub">Accept a job, head out, then confirm delivery with the customer's OTP.</p>
			<?php if(count($active)===0){ ?>
				<div class="empty"><div class="empty-ic">&#128692;</div><h3>No active deliveries</h3><p>Go online to start receiving delivery jobs near you.</p></div>
			<?php } else { ?>
			<div class="order-grid">
				<?php foreach($active as $row){ ?>
				<article class="order-card">
					<div class="order-top">
						<span class="oid">#<?php echo htmlspecialchars($row['order_id']); ?></span>
						<span class="statebadge"><i class="ping"></i><?php echo htmlspecialchars($row['rider_status']); ?></span>
					</div>
					<div class="order-cust"><?php echo htmlspecialchars($row['order_from']); ?> &rarr; <?php echo htmlspecialchars($row['order_by']); ?></div>
					<ul class="order-items">
						<?php foreach(order_items($con,$row['items'],$row['order_from']) as $it){ ?>
							<li><span><?php echo htmlspecialchars($it[0]); ?></span><span class="q">&times; <?php echo htmlspecialchars($it[1]); ?></span></li>
						<?php } ?>
					</ul>
					<div class="order-meta">
						<div><span>Collect</span><b>&#8377;<?php echo htmlspecialchars($row['total']); ?></b></div>
						<div><span>Address</span><b><?php echo htmlspecialchars($row['address']); ?></b></div>
						<div><span>Placed</span><b><?php echo htmlspecialchars($row['instance']); ?></b></div>
					</div>
					<form method="post" class="order-actions">
						<input type="text" name="order_id" value="<?php echo htmlspecialchars($row['order_id']); ?>" hidden>
						<?php
						if ($row['status']=="accepted" || $row['status']=="On the way" ){
							if ($row['rider_status']=="pending"){ ?>
								<button class="btn-soft" type="submit" name="accept" value="accept">Accept</button>
							<?php } ?>
							<button class="btn-danger" type="submit" name="decline" value="decline">Decline</button>
							<?php if ($row['rider_status']=="accepted") { ?>
								<button class="btn btn-primary" type="submit" name="on_the_way" value="Mark as On the way">On the way</button>
							<?php }
							if($row['rider_status']=="On the way") { ?>
								<div class="otp-row">
									<input class="input" type="text" name="otp" placeholder="Enter delivery OTP">
									<?php if($error=="wrong otp") echo '<div class="error_msg">Wrong OTP</div>'; ?>
									<button class="btn btn-primary" type="submit" name="delivered" value="Mark as Delivered">Mark delivered</button>
								</div>
							<?php }
						} else { ?>
							<span class="order-hint">Waiting for the restaurant to accept.</span>
						<?php } ?>
					</form>
				</article>
				<?php } ?>
			</div>
			<?php } ?>
		</section>

		<section class="panel">
			<div class="panel-title">Past deliveries</div>
			<p class="panel-sub"><?php echo count($past); ?> completed</p>
			<?php if(count($past)>0){ ?>
				<div class="table-scroll">
					<table class="data-table">
						<tr><th>Order</th><th>Restaurant</th><th>Customer</th><th>Total</th><th>Status</th></tr>
						<?php foreach($past as $row){ ?>
						<tr>
							<td>#<?php echo htmlspecialchars($row['order_id']); ?></td>
							<td><?php echo htmlspecialchars($row['order_from']); ?></td>
							<td><?php echo htmlspecialchars($row['order_by']); ?></td>
							<td class="num">&#8377;<?php echo htmlspecialchars($row['total']); ?></td>
							<td><?php echo htmlspecialchars($row['rider_status']); ?></td>
						</tr>
						<?php } ?>
					</table>
				</div>
			<?php } else { ?>
				<div class="empty"><div class="empty-ic">&#9203;</div><h3>Nothing yet</h3><p>Your completed deliveries will be archived here.</p></div>
			<?php } ?>
		</section>
	</div>
	<script>
		// advance the demo simulation; reload when an order changes state
		setInterval(function(){
			fetch('tick.php').then(function(r){ return r.text(); }).then(function(t){ if(t === 'changed') location.reload(); });
		}, 5000);
	</script>
	<script src="js/rider_home.js"></script>
</body>
</html>
