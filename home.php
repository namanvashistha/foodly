<?php
session_start();
if(!isset($_SESSION['log_email'])){
	header("location:index.php");
	exit;
}
include 'connection.php';
$q="SELECT * FROM `restaurants`; ";
$q1=mysqli_query($con,$q);
$restaurants = array();
while($row=mysqli_fetch_array($q1)){ $restaurants[] = $row; }
$online = 0;
foreach($restaurants as $r){ if($r['status']=="Online") $online++; }
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
	<script src="https://code.jquery.com/jquery-3.3.1.min.js"
		integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
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
						<a href="order_status.php">Past orders</a>
						<a href="logout.php">Log out</a>
					</div>
				</div>
			</nav>
		</div>
	</header>

	<!-- ===== greeting + search ===== -->
	<section class="greet">
		<div class="wrap">
			<span class="eyebrow">Tonight's table</span>
			<h1>Good to see you, <?php echo htmlspecialchars(strtok($_SESSION['log_name'],' ')); ?>.</h1>
			<p class="greet-sub"><?php echo $online; ?> of <?php echo count($restaurants); ?> kitchens are open and cooking right now.</p>
			<div class="search">
				<span class="search-ic">&#9906;</span>
				<input id="restaurant-search" class="input" type="text" placeholder="Search restaurants by name" oninput="filterRestaurants()">
			</div>
		</div>
	</section>

	<!-- ===== restaurant grid ===== -->
	<main class="section">
		<div class="wrap">
			<?php if(count($restaurants) === 0){ ?>
				<div class="empty">
					<div class="empty-ic">&#127869;</div>
					<h2>No kitchens yet</h2>
					<p>Restaurants are still joining Foodly in your area. Check back soon, the menus are on their way.</p>
				</div>
			<?php } else { ?>
			<div class="grid" id="restaurant-grid">
				<?php
				// curated, palette-friendly photos (skip the pink cake / orange burger);
				// assigned by position so adjacent cards differ and stay stable across reloads.
				$pool = array(6,8,3,5,7,9);
				foreach($restaurants as $i => $row){
					$isOnline = ($row['status']=="Online");
					$img = $pool[ $i % count($pool) ];
				?>
				<a class="rcard" href="restaurant_menu.php?restaurant=<?php echo urlencode($row['email']); ?>"
					data-name="<?php echo htmlspecialchars(strtolower($row['name'])); ?>">
					<div class="rcard-img" style="background-image:url('images/<?php echo $img; ?>.jpg');">
						<span class="status <?php echo $isOnline ? 'is-online' : 'is-offline'; ?>">
							<i class="ping"></i><?php echo $isOnline ? 'Open now' : 'Closed'; ?>
						</span>
					</div>
					<div class="rcard-body">
						<h3 class="rcard-name"><?php echo htmlspecialchars($row['name']); ?></h3>
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
		$(document).ready(function () {
			function sendMsg() {
				var send_msg = $('#send_msg').val();
				if ($.trim(send_msg) !== '') {
					$.ajax({
						url: "send_msg.php",
						method: "POST",
						data: { msg: send_msg, client: "user" },
						dataType: "text",
						success: function () { $('#send_msg').val(""); }
					});
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
