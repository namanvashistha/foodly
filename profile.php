<?php
session_start();
if(!isset($_SESSION['log_email'])){
	header("location:index.php");
	exit;
}
include 'connection.php';
$email = $_SESSION['log_email'];
$saved = false;

if(isset($_POST['save'])){
	$phone   = $_POST['phone'] ?? '';
	$address = $_POST['address'] ?? '';
	$u = mysqli_prepare($con, "UPDATE users SET phone=?, address=? WHERE email=?");
	mysqli_stmt_bind_param($u, "sss", $phone, $address, $email);
	mysqli_stmt_execute($u);
	$saved = true;
}

$stmt = mysqli_prepare($con, "SELECT * FROM users WHERE email=? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$u = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$oc = mysqli_prepare($con, "SELECT COUNT(*) c, COALESCE(SUM(total),0) spent FROM orders WHERE order_by=?");
mysqli_stmt_bind_param($oc, "s", $email);
mysqli_stmt_execute($oc);
$stats = mysqli_fetch_assoc(mysqli_stmt_get_result($oc));
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Foodly — Your profile</title>
	<link rel="shortcut icon" href="images/logo.png" type="image/png">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="stylesheet" href="css/theme.css?v=<?php echo @filemtime('css/theme.css'); ?>">
	<link rel="stylesheet" href="css/app.css?v=<?php echo @filemtime('css/app.css'); ?>">
	<link rel="stylesheet" href="css/profile.css?v=<?php echo @filemtime('css/profile.css'); ?>">
</head>
<body>

	<header class="topbar">
		<div class="wrap">
			<a class="wordmark" href="home.php">foodly<span class="dot">.</span></a>
			<nav class="topbar-nav">
				<a href="home.php">Home</a>
				<a href="order_status.php">Orders</a>
				<a href="logout.php">Log out</a>
			</nav>
		</div>
	</header>

	<div class="page wrap">
		<div class="page-head">
			<div>
				<span class="eyebrow">Your account</span>
				<h1><?php echo htmlspecialchars($u['name']); ?></h1>
				<p class="sub"><?php echo htmlspecialchars($u['email']); ?></p>
			</div>
		</div>

		<div class="stat-row">
			<div class="stat-tile"><div class="k">Wallet</div><div class="v">&#8377;<?php echo htmlspecialchars($u['wallet']); ?></div></div>
			<div class="stat-tile"><div class="k">Orders placed</div><div class="v"><?php echo htmlspecialchars($stats['c']); ?></div></div>
			<div class="stat-tile"><div class="k">Total spent</div><div class="v">&#8377;<?php echo htmlspecialchars(floor($stats['spent'])); ?></div></div>
		</div>

		<section class="panel profile-panel">
			<div class="panel-title">Contact details</div>
			<p class="panel-sub">Keep these up to date so your food finds you.</p>
			<?php if($saved){ ?><div class="profile-saved">Saved.</div><?php } ?>
			<form method="post">
				<div class="field">
					<label>Name</label>
					<input class="input" type="text" value="<?php echo htmlspecialchars($u['name']); ?>" disabled>
				</div>
				<div class="field">
					<label>Email</label>
					<input class="input" type="text" value="<?php echo htmlspecialchars($u['email']); ?>" disabled>
				</div>
				<div class="field">
					<label for="phone">Phone</label>
					<input class="input" type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($u['phone']); ?>" required>
				</div>
				<div class="field">
					<label for="address">Address</label>
					<input class="input" type="text" id="address" name="address" value="<?php echo htmlspecialchars($u['address']); ?>" required>
				</div>
				<button class="btn btn-primary" type="submit" name="save" value="1">Save changes</button>
			</form>
		</section>
	</div>
</body>
</html>
