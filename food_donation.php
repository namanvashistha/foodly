<?php
include "connection.php";
if(isset($_POST['update'])){
  $item_name=$_POST['item_name'];
  $item_quan=$_POST['item_quan'];
  $ph = $_POST['phone'];
  $res=$_POST['res'];
  for($i=0;$i<sizeof($item_name);$i++){
    if(empty($item_name[$i]) || empty($item_quan[$i]))
            continue;
    $q="INSERT INTO donate(`restaurant`,`item_name`,`item_quan`,`phone`) VALUES ('$res','$item_name[$i]', '$item_quan[$i]','$ph');";
    mysqli_query($con,$q);
  }
  header('location:food_donation.php');
}
$donations = array();
$dq = mysqli_query($con,"SELECT * FROM `donate` ORDER BY instance DESC;");
while($k=mysqli_fetch_array($dq)){ $donations[] = $k; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Foodly — Food donation</title>
	<link rel="shortcut icon" href="images/logo.png" type="image/png">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="stylesheet" href="css/theme.css?v=<?php echo @filemtime('css/theme.css'); ?>">
	<link rel="stylesheet" href="css/app.css?v=<?php echo @filemtime('css/app.css'); ?>">
	<link rel="stylesheet" href="css/food_donation.css?v=<?php echo @filemtime('css/food_donation.css'); ?>">
</head>
<body>

	<header class="topbar">
		<div class="wrap">
			<a class="wordmark" href="index.php">foodly<span class="dot">.</span></a>
			<nav class="topbar-nav">
				<a href="index.php">Back to Foodly</a>
			</nav>
		</div>
	</header>

	<section class="donate-hero">
		<div class="wrap">
			<span class="eyebrow">Food donation</span>
			<h1>Good food should never go to waste.</h1>
			<p>Restaurants with surplus can list it here so it reaches people who need it. A few details, a real meal for someone.</p>
		</div>
	</section>

	<div class="page wrap donate-layout">

		<!-- ===== donate form ===== -->
		<section class="panel donate-form-panel">
			<div class="panel-title">List surplus food</div>
			<p class="panel-sub">Tell us what you have and how to reach you.</p>
			<form method="post">
				<div class="field">
					<label for="res">Restaurant name</label>
					<input class="input" type="text" id="res" name="res" placeholder="Your restaurant" required>
				</div>
				<div class="field">
					<label for="phone">Phone</label>
					<input class="input" type="text" id="phone" name="phone" placeholder="Contact number" required>
				</div>
				<div id="item_fileds">
					<div class="item-row">
						<div class="item-row-head">Item 1</div>
						<div class="don-grid">
							<input class="input" type="text" name="item_name[]" placeholder="Item name">
							<input class="input" type="text" name="item_quan[]" placeholder="Quantity">
						</div>
					</div>
				</div>
				<div class="menu-form-actions">
					<button type="button" class="btn-soft" id="more_fields" onclick="add_fields();">+ Add another item</button>
					<button type="submit" class="btn btn-primary" name="update">Donate</button>
				</div>
			</form>
		</section>

		<!-- ===== donations feed ===== -->
		<section>
			<h2 class="donate-feed-title">Recent donations</h2>
			<?php if(count($donations)>0){ ?>
				<div class="donate-grid">
					<?php foreach($donations as $k){ ?>
					<article class="donate-card">
						<div class="donate-item"><?php echo htmlspecialchars($k['item_name']); ?> <span class="q">&times; <?php echo htmlspecialchars($k['item_quan']); ?></span></div>
						<div class="donate-by"><?php echo htmlspecialchars($k['restaurant']); ?></div>
						<div class="donate-meta">
							<span>&#9742; <?php echo htmlspecialchars($k['phone']); ?></span>
							<span><?php echo htmlspecialchars($k['instance']); ?></span>
						</div>
					</article>
					<?php } ?>
				</div>
			<?php } else { ?>
				<div class="empty"><div class="empty-ic">&#127869;</div><h3>No donations yet</h3><p>Be the first to share surplus food with your community.</p></div>
			<?php } ?>
		</section>
	</div>

	<script src="js/food_donation.js"></script>
</body>
</html>
