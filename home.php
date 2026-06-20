<?php
session_start();
if(!isset($_SESSION['log_email'])){
	header("location:index.php");
	exit;
}
include 'connection.php';
include 'geo.php';
ensure_user_location($con); // fresh visitor defaults to the restaurant cluster

$restaurants = array();
$rs = mysqli_query($con, "SELECT * FROM `restaurants`");
while($row = mysqli_fetch_array($rs)){ $restaurants[] = $row; }

$ratings = array();
$rq = mysqli_query($con, "SELECT restaurant, ROUND(AVG(stars),1) avg, COUNT(*) n FROM ratings GROUP BY restaurant");
while($rr = mysqli_fetch_assoc($rq)){ $ratings[$rr['restaurant']] = $rr; }

$inrange = array();
foreach($restaurants as $row){
	$d = user_distance_km($row['lat'], $row['lng']);
	if($d !== null && $d <= DELIVERY_RADIUS_KM){ $row['distance'] = $d; $inrange[] = $row; }
}
usort($inrange, function($a,$b){ return $a['distance'] <=> $b['distance']; });
$online_inrange = 0;
foreach($inrange as $r){ if($r['status']=="Online") $online_inrange++; }
$auto  = !empty($_SESSION['user_auto']);
$located = has_user_location() && !$auto; // user has an explicit (non-default) location
$place = $_SESSION['user_place'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Foodly — Restaurants near you</title>
	<link rel="shortcut icon" href="images/logo.png" type="image/png">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="stylesheet" href="css/theme.css?v=<?php echo @filemtime('css/theme.css'); ?>">
	<link rel="stylesheet" href="css/home.css?v=<?php echo @filemtime('css/home.css'); ?>">
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
	<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body>

	<!-- ===== top bar ===== -->
	<header class="topbar">
		<div class="wrap">
			<a class="wordmark" href="home.php">foodly<span class="dot">.</span></a>
			<nav class="topbar-nav">
				<a href="order_status.php">Active orders</a>
				<div class="dropdown">
					<button class="dropbtn" onclick="myFunction()">
						<span class="avatar"><?php echo strtoupper(substr($_SESSION['log_name'],0,1)); ?></span>
						<span class="who"><?php echo htmlspecialchars($_SESSION['log_name']); ?></span>
						<span class="caret">&#9662;</span>
					</button>
					<div class="dropdown-content" id="myDropdown">
						<a href="profile.php">Profile</a>
						<a href="orders_history.php">Past orders</a>
						<a href="logout.php">Log out</a>
					</div>
				</div>
			</nav>
		</div>
	</header>

	<!-- ===== greeting ===== -->
	<section class="greet">
		<div class="wrap">
			<span class="eyebrow">Tonight's table</span>
			<h1>Good to see you, <?php echo htmlspecialchars(strtok($_SESSION['log_name'],' ')); ?>.</h1>
			<p class="greet-sub"><?php echo count($inrange); ?> kitchens deliver to you (<?php echo $online_inrange; ?> open now), within <?php echo DELIVERY_RADIUS_KM; ?> km.</p>
			<div class="loc-bar">
				<span class="loc-pin">&#128205;</span>
				<?php if($auto){ ?>
					<span class="loc-text">Showing kitchens near a <b>sample location</b></span>
					<button class="btn-soft" type="button" onclick="useMyLocation()">Use my location</button>
					<button class="btn-soft" type="button" onclick="openLocPicker()">Pick on map</button>
				<?php } else { ?>
					<span class="loc-text">Delivering to <b><?php echo $place ? htmlspecialchars($place) : 'your pinned location'; ?></b></span>
					<button class="btn-soft" type="button" onclick="openLocPicker()">Change location</button>
				<?php } ?>
			</div>
			<div id="loc-status" class="loc-status"></div>
			<div class="search">
				<span class="search-ic">&#9906;</span>
				<input id="restaurant-search" class="input" type="text" placeholder="Search restaurants by name" oninput="filterRestaurants()">
			</div>
		</div>
	</section>

	<main class="section">
		<div class="wrap">
		<?php if(count($inrange) === 0){ ?>
			<div class="empty">
				<div class="empty-ic">&#128533;</div>
				<h2>No kitchens reach you yet</h2>
				<p>No restaurants deliver within <?php echo DELIVERY_RADIUS_KM; ?> km of here. Try a different location.</p>
				<div class="loc-prompt-actions" style="justify-content:center;margin-top:1rem;">
					<button class="btn btn-primary" type="button" onclick="useMyLocation()">Use my location</button>
					<button class="btn btn-ghost" type="button" onclick="openLocPicker()">Pick on map</button>
				</div>
			</div>
		<?php } else { ?>
			<div class="grid" id="restaurant-grid">
				<?php
				$pool = array(6,8,3,5,7,9);
				foreach($inrange as $i => $row){
					$isOnline = ($row['status']=="Online");
					$img = $pool[ $i % count($pool) ];
				?>
				<a class="rcard" href="restaurant_menu.php?restaurant=<?php echo urlencode($row['email']); ?>"
					data-name="<?php echo htmlspecialchars(strtolower($row['name'])); ?>">
					<div class="rcard-img" style="background-image:url('images/<?php echo $img; ?>.jpg');">
						<span class="status <?php echo $isOnline ? 'is-online' : 'is-offline'; ?>">
							<i class="ping"></i><?php echo $isOnline ? 'Open now' : 'Closed'; ?>
						</span>
						<span class="dist-badge"><?php echo number_format($row['distance'],1); ?> km</span>
					</div>
					<div class="rcard-body">
						<div class="rcard-name-row">
							<h3 class="rcard-name"><?php echo htmlspecialchars($row['name']); ?></h3>
							<?php if(isset($ratings[$row['email']])){ $rt=$ratings[$row['email']]; ?>
								<span class="rating-badge"><span class="s">&#9733;</span><?php echo $rt['avg']; ?> <span class="n">(<?php echo $rt['n']; ?>)</span></span>
							<?php } ?>
						</div>
						<p class="rcard-addr"><?php echo htmlspecialchars($row['address']); ?></p>
						<p class="rcard-desc"><?php echo htmlspecialchars($row['description']); ?></p>
						<span class="rcard-go">View menu &rarr;</span>
					</div>
				</a>
				<?php } ?>
			</div>
			<div class="empty" id="no-results" style="display:none;">
				<div class="empty-ic">&#128269;</div>
				<h2>No matches</h2>
				<p>No restaurants match that search. Try a different name.</p>
			</div>
		<?php } ?>
		</div>
	</main>

	<!-- ===== location picker modal ===== -->
	<div id="loc-modal" class="loc-modal">
		<div class="loc-modal-card">
			<div class="loc-modal-head">
				<div>
					<div class="loc-modal-title">Set your delivery location</div>
					<div class="loc-modal-sub">Tap the map to drop a pin, or use your current location.</div>
				</div>
				<span class="chat-close" onclick="closeLocPicker()" title="Close">&times;</span>
			</div>
			<div id="loc-map"></div>
			<div class="loc-modal-foot">
				<button class="btn-soft" type="button" onclick="useMyLocation(true)">Use my current location</button>
				<button class="btn btn-primary" type="button" id="loc-confirm" onclick="confirmLocation()" disabled>Confirm location</button>
			</div>
		</div>
	</div>

	<!-- ===== support chat ===== -->
	<button class="boxe" onclick="show_chat_box()" aria-label="Open support chat">
		<span class="boxe-ic">&#128172;</span>
	</button>
	<div id="chat-box">
		<div class="chat-head">
			<div>
				<div class="chat-title">Foodly Support</div>
				<div class="chat-status"><i></i>We usually reply in a minute</div>
			</div>
			<span class="chat-close" onclick="show_chat_box()" title="Close">&times;</span>
		</div>
		<div id="msg-box"></div>
		<div class="chat-input">
			<input id="send_msg" class="input" type="text" name="msg" placeholder="Type a message" autocomplete="off">
			<button type="submit" id="send_button" class="btn btn-primary">Send</button>
		</div>
	</div>

	<script>
		// ----- location picker -----
		var locMap, locMarker, picked = null;
		function openLocPicker() {
			document.getElementById('loc-modal').classList.add('open');
			if (!locMap) {
				locMap = L.map('loc-map').setView([<?php echo $located ? $_SESSION['user_lat'].','.$_SESSION['user_lng'] : '28.6315,77.2167'; ?>], <?php echo $located ? 14 : 11; ?>);
				L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { subdomains: 'abcd', maxZoom: 20 }).addTo(locMap);
				locMap.on('click', function (e) { setPin(e.latlng.lat, e.latlng.lng); });
				<?php if($located){ ?>setPin(<?php echo $_SESSION['user_lat']; ?>, <?php echo $_SESSION['user_lng']; ?>);<?php } ?>
			}
			setTimeout(function () { locMap.invalidateSize(); }, 150);
		}
		function closeLocPicker() { document.getElementById('loc-modal').classList.remove('open'); }
		function setPin(lat, lng) {
			picked = { lat: lat, lng: lng };
			if (!locMarker) locMarker = L.marker([lat, lng]).addTo(locMap);
			else locMarker.setLatLng([lat, lng]);
			locMap.panTo([lat, lng]);
			document.getElementById('loc-confirm').disabled = false;
		}
		function useMyLocation(inModal) {
			var status = document.getElementById('loc-status');
			if (!navigator.geolocation) { if (status) status.textContent = "Geolocation is not supported by your browser."; return; }
			if (status) status.textContent = "Locating you…";
			navigator.geolocation.getCurrentPosition(function (pos) {
				var lat = pos.coords.latitude, lng = pos.coords.longitude;
				if (inModal) { setPin(lat, lng); locMap.setView([lat, lng], 15); }
				else { saveLocation(lat, lng); }
			}, function () {
				if (status) status.textContent = "Couldn't get your location. Pick it on the map instead.";
				openLocPicker();
			});
		}
		function confirmLocation() { if (picked) saveLocation(picked.lat, picked.lng); }
		function saveLocation(lat, lng) {
			// reverse geocode for a friendly label, then persist
			fetch('https://nominatim.openstreetmap.org/reverse?format=json&zoom=14&lat=' + lat + '&lon=' + lng)
				.then(function (r) { return r.json(); })
				.then(function (d) { post(lat, lng, (d && d.display_name) ? d.display_name.split(',').slice(0,2).join(',') : ''); })
				.catch(function () { post(lat, lng, ''); });
		}
		function post(lat, lng, place) {
			$.post("set_location.php", { lat: lat, lng: lng, place: place }, function () { window.location.reload(); });
		}

		// ----- support chat -----
		$(document).ready(function () {
			function sendMsg() {
				var send_msg = $('#send_msg').val();
				if ($.trim(send_msg) !== '') {
					$.ajax({ url: "send_msg.php", method: "POST", data: { msg: send_msg, client: "user" }, dataType: "text",
						success: function () { $('#send_msg').val(""); } });
				}
			}
			$('#send_button').click(sendMsg);
			$('#send_msg').keydown(function (e) { if (e.key === 'Enter') { e.preventDefault(); sendMsg(); } });
			setInterval(function () {
				var box = document.getElementById('msg-box');
				var atBottom = box.scrollHeight - box.scrollTop - box.clientHeight < 40;
				$('#msg-box').load("fetch_msg.php", function () { if (atBottom) box.scrollTop = box.scrollHeight; });
			}, 1500);
		});
	</script>
	<script src="js/home.js"></script>
</body>
</html>
