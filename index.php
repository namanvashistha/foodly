<?php
include 'connection.php';
include 'auth_lib.php';
	$ipaddress = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

	// log the visit (prepared)
	$st = mysqli_prepare($con, "INSERT INTO `stats` (`ip_address`,`coordinates`,`city`) VALUES (?,'','')");
	mysqli_stmt_bind_param($st, "s", $ipaddress);
	mysqli_stmt_execute($st);

function log_stat($con, $ip, $email, $status){
	$st = mysqli_prepare($con, "INSERT INTO `stats` (`ip_address`,`coordinates`,`city`,`client`,`status`) VALUES (?,'','',?,?)");
	mysqli_stmt_bind_param($st, "sss", $ip, $email, $status);
	mysqli_stmt_execute($st);
}

$error_msg="";
if(isset($_POST['login']) || isset($_POST['signup'])){
	session_start();

	if(isset($_POST['login'])){
		$log_email =$_POST['log_email'];
		$log_pass  =$_POST['log_pass'];
		$row = db_login($con, 'users', $log_email, $log_pass);
		if($row){
			auth_session_start();
			$_SESSION['log_email'] =$log_email;
			$_SESSION['log_name'] =$row['name'];
			$_SESSION['log_client'] ="user";
			log_stat($con, $ipaddress, $log_email, 'login');
			header("location:home.php");
			exit;
		}
		else{
			$error_msg="incorrect email or password";
		}
	}
	else if(isset($_POST['signup'])){
		$sign_name    =$_POST['sign_name'];
		$sign_pass    =$_POST['sign_pass'];
		$sign_email   =$_POST['sign_email'];
		$sign_phone   =$_POST['sign_phone'];
		$sign_address =$_POST['sign_address'];
		if(db_email_exists($con, 'users', $sign_email)){
			$error_msg= "email already exists";
		}
		else{
			$hash = db_hash($sign_pass);
			$ins = mysqli_prepare($con, "INSERT INTO `users` (`name`,`password`,`email`,`phone`,`address`) VALUES (?,?,?,?,?)");
			mysqli_stmt_bind_param($ins, "sssss", $sign_name, $hash, $sign_email, $sign_phone, $sign_address);
			if(mysqli_stmt_execute($ins)){
				auth_session_start();
				$_SESSION['log_email'] =$sign_email;
				$_SESSION['log_name'] =$sign_name;
				$_SESSION['log_client'] ="user";
				log_stat($con, $ipaddress, $sign_email, 'signup');
				header("location:home.php");
				exit;
			}
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Foodly — Crave. Tap. Devoured.</title>
	<meta name="description" content="Foodly is an online food ordering platform connecting hungry customers, restaurants, and riders. Fresh food, a tap away.">
	<link rel="shortcut icon" href="images/logo.png" type="image/png">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="stylesheet" href="css/theme.css?v=<?php echo @filemtime('css/theme.css'); ?>">
	<link rel="stylesheet" href="css/index.css?v=<?php echo @filemtime('css/index.css'); ?>">
</head>
<body>

	<!-- ===== nav ===== -->
	<nav class="nav">
		<div class="wrap">
			<a class="wordmark" href="index.php">foodly<span class="dot">.</span></a>
			<div class="nav-links">
				<a class="desk" href="#how">How it works</a>
				<a class="desk" href="#business">For business</a>
				<a class="nav-cta" onclick="openModal('id01')">Log in</a>
				<a class="btn btn-primary" onclick="openModal('id02')">Sign up</a>
			</div>
		</div>
	</nav>

	<!-- ===== hero ===== -->
	<header class="hero">
		<div class="wrap">
			<div class="hero-copy">
				<span class="eyebrow">Fresh food, delivered</span>
				<h1>Crave.<br>Tap.<br><span class="accent">Devoured.</span></h1>
				<p class="hero-sub">Real meals from kitchens near you, framed like the gallery they deserve. Order in seconds, eat in minutes.</p>
				<div class="hero-actions">
					<a class="btn btn-primary btn-lg" onclick="openModal('id02')">Order now</a>
					<a class="btn btn-ghost btn-lg" href="#how">See how it works</a>
				</div>
				<div class="hero-meta">
					<div class="stat"><span class="num">1,200+</span><span class="lbl">Dishes</span></div>
					<div class="stat"><span class="num">40+</span><span class="lbl">Kitchens</span></div>
					<div class="stat"><span class="num">22 min</span><span class="lbl">Avg delivery</span></div>
				</div>
			</div>
			<div class="hero-art">
				<img src="images/6.jpg" alt="A spread of three rustic bowls, herb-flecked and freshly plated on weathered wood">
				<div class="hero-badge">
					<span class="ring">&#127869;</span>
					<span>
						<span class="t">Hot &amp; on the way</span><br>
						<span class="s">Live order tracking</span>
					</span>
				</div>
			</div>
		</div>
	</header>

	<!-- ===== how it works ===== -->
	<section class="section" id="how">
		<div class="wrap">
			<div class="section-head reveal">
				<span class="eyebrow">The short version</span>
				<h2>Three taps between you and dinner.</h2>
			</div>
			<div class="steps reveal">
				<div class="step">
					<h3>Browse the menu</h3>
					<div class="rule"></div>
					<p>Scroll real photos from kitchens around you. No stock, no surprises, just what lands on your plate.</p>
				</div>
				<div class="step">
					<h3>Place your order</h3>
					<div class="rule"></div>
					<p>Build your cart, pay, done. A rider is matched the moment the kitchen fires it up.</p>
				</div>
				<div class="step">
					<h3>Track to your door</h3>
					<div class="rule"></div>
					<p>Watch it travel in real time and meet your rider with a one-tap handoff code.</p>
				</div>
			</div>
		</div>
	</section>

	<!-- ===== featured dishes ===== -->
	<section class="section" id="featured">
		<div class="wrap">
			<div class="section-head reveal">
				<span class="eyebrow">On the menu tonight</span>
				<h2>A few things worth being hungry for.</h2>
			</div>
			<div class="gallery reveal">
				<div class="dish tall">
					<img src="images/9.jpg" alt="A steaming clay pot of seafood and vegetables served on a dark wooden table">
					<div class="cap"><div class="name">Steamed Clay Pot</div><div class="meta">Coastal Kitchen &middot; 26 min</div></div>
				</div>
				<div class="dish">
					<img src="images/8.jpg" alt="A wood-fired pizza with mushrooms, olives and peppers on a board">
					<div class="cap"><div class="name">Garden Pizza</div><div class="meta">Pizza Palace &middot; 18 min</div></div>
				</div>
				<div class="dish">
					<img src="images/3.jpg" alt="A bright salad of greens, pomegranate, goat cheese and candied nuts">
					<div class="cap"><div class="name">Pomegranate Salad</div><div class="meta">Green Bowl &middot; 14 min</div></div>
				</div>
				<div class="dish tall">
					<img src="images/5.jpg" alt="Grilled chicken with asparagus, fresh bread and a garden salad on rustic wood">
					<div class="cap"><div class="name">Grilled Plate</div><div class="meta">Burger Barn &middot; 21 min</div></div>
				</div>
				<div class="dish">
					<img src="images/7.jpg" alt="A loaded pizza with peppers, olives and herbs, a slice pulled away">
					<div class="cap"><div class="name">Pepperoni Pie</div><div class="meta">Pizza Palace &middot; 19 min</div></div>
				</div>
			</div>
		</div>
	</section>

	<!-- ===== for business ===== -->
	<section class="section business" id="business">
		<div class="wrap">
			<div class="section-head reveal">
				<span class="eyebrow">For business</span>
				<h2>Foodly works for everyone at the table.</h2>
				<p>Whether you cook it, carry it, or keep customers smiling, there is a place for you here.</p>
			</div>
			<div class="roles reveal">
				<a class="role" href="restaurant_sign.php">
					<span class="ic">&#127859;</span>
					<h3>Restaurants</h3>
					<p>List your menu, manage incoming orders, and reach hungry customers nearby.</p>
					<span class="go">Partner with us &rarr;</span>
				</a>
				<a class="role" href="rider_sign.php">
					<span class="ic">&#128692;</span>
					<h3>Riders</h3>
					<p>Pick up, deliver, and build your streak. Flexible hours, steady earnings.</p>
					<span class="go">Start riding &rarr;</span>
				</a>
				<a class="role" href="support_sign.php">
					<span class="ic">&#128172;</span>
					<h3>Support agents</h3>
					<p>Help customers in real time over chat and keep every order on track.</p>
					<span class="go">Join support &rarr;</span>
				</a>
			</div>
		</div>
	</section>

	<!-- ===== donation callout ===== -->
	<section class="section">
		<div class="wrap">
			<div class="donate-band reveal">
				<div>
					<h2>Good food should never go to waste.</h2>
					<p>Restaurants with surplus can donate it to people who need it. A few taps, a real meal for someone.</p>
				</div>
				<a class="btn btn-light btn-lg" href="food_donation.php">Donate food</a>
			</div>
		</div>
	</section>

	<!-- ===== footer ===== -->
	<footer class="footer">
		<div class="wrap">
			<div>
				<a class="wordmark" href="index.php">foodly<span class="dot">.</span></a>
				<p class="footer-tag">Fast, fresh, and instant. Good food, a tap away from your pick.</p>
			</div>
			<div class="footer-links">
				<div class="footer-col">
					<span class="h">Eat</span>
					<a onclick="openModal('id02')">Order food</a>
					<a href="food_donation.php">Food donation</a>
				</div>
				<div class="footer-col">
					<span class="h">Business</span>
					<a href="restaurant_sign.php">Restaurants</a>
					<a href="rider_sign.php">Riders</a>
					<a href="support_sign.php">Support</a>
				</div>
				<div class="footer-col">
					<span class="h">Project</span>
					<a href="https://github.com/namanvashistha/foodly" target="_blank" rel="noopener">GitHub</a>
					<a href="mailto:naman.vashistha@dailyrounds.org">Contact</a>
				</div>
			</div>
		</div>
		<div class="wrap footer-base">&copy; <?php echo date('Y'); ?> Foodly. Built by Naman Vashistha.</div>
	</footer>

	<!-- ===== login modal (id01) ===== -->
	<div id="id01" class="modal">
		<form class="modal-card" method="POST">
			<span class="close" onclick="closeModal('id01')" title="Close">&times;</span>
			<h3>Welcome back</h3>
			<p class="lead">Log in to pick up where your appetite left off.</p>
			<div class="hint">Try the demo: <b>admin</b> / <b>admin</b></div>
			<div class="field">
				<label for="log_email">Username</label>
				<input class="input" type="text" id="log_email" placeholder="Enter username" name="log_email" value="admin" required>
			</div>
			<div class="field">
				<label for="log_pass">Password</label>
				<input class="input" type="password" id="log_pass" placeholder="Enter password" name="log_pass" value="admin" required>
			</div>
			<div id="log_error_msg" class="error_msg"><?php if($error_msg=="incorrect email or password") echo $error_msg; ?></div>
			<button class="btn btn-primary" type="submit" name="login" value="login">Log in</button>
			<div class="modal-switch">New here? <a onclick="switchModal('id01','id02')">Create an account</a></div>
		</form>
	</div>

	<!-- ===== signup modal (id02) ===== -->
	<div id="id02" class="modal">
		<form class="modal-card" method="POST">
			<span class="close" onclick="closeModal('id02')" title="Close">&times;</span>
			<h3>Create your account</h3>
			<p class="lead">A minute now, dinner sorted for good.</p>
			<div class="field">
				<label for="sign_name">Name</label>
				<input class="input" type="text" id="sign_name" placeholder="Enter name" name="sign_name" required>
			</div>
			<div class="field">
				<label for="sign_email">Email</label>
				<input class="input" type="text" id="sign_email" placeholder="Enter email" name="sign_email" required>
			</div>
			<div class="field">
				<label for="sign_pass">Password</label>
				<input class="input" type="password" id="sign_pass" placeholder="Create a password" name="sign_pass" required>
			</div>
			<div class="field">
				<label for="sign_phone">Phone</label>
				<input class="input" type="text" id="sign_phone" placeholder="Enter phone" name="sign_phone" required>
			</div>
			<div class="field">
				<label for="sign_address">Address</label>
				<input class="input" type="text" id="sign_address" placeholder="Enter address" name="sign_address" required>
			</div>
			<div id="sign_error_msg" class="error_msg"><?php if($error_msg=="email already exists") echo $error_msg; ?></div>
			<button class="btn btn-primary" type="submit" name="signup" value="Sign Up">Sign up</button>
			<div class="modal-switch">Already have an account? <a onclick="switchModal('id02','id01')">Log in</a></div>
		</form>
	</div>

	<script src="js/index.js"></script>
</body>
</html>
