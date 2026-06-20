<?php
	session_start();
	if(!isset($_SESSION['log_email'])){
		header("location:index.php");
		exit;
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Foodly — Your orders</title>
	<link rel="shortcut icon" href="images/logo.png" type="image/png">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="stylesheet" href="css/theme.css?v=<?php echo @filemtime('css/theme.css'); ?>">
	<link rel="stylesheet" href="css/app.css?v=<?php echo @filemtime('css/app.css'); ?>">
	<link rel="stylesheet" href="css/order_status.css?v=<?php echo @filemtime('css/order_status.css'); ?>">
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
</head>
<body>

	<header class="topbar">
		<div class="wrap">
			<a class="wordmark" href="home.php">foodly<span class="dot">.</span></a>
			<nav class="topbar-nav">
				<a href="home.php">Home</a>
				<a href="logout.php">Log out</a>
			</nav>
		</div>
	</header>

	<div class="page wrap">
		<div class="page-head">
			<div>
				<span class="eyebrow">Order tracking</span>
				<h1>Your active orders</h1>
				<p class="sub">Live status, refreshed automatically.</p>
			</div>
		</div>
		<div id="order_box"></div>
	</div>

	<script>
		function loadOrders(){ $('#order_box').load("fetch_order.php"); }
		loadOrders();
		setInterval(loadOrders, 2000);
	</script>
</body>
</html>
