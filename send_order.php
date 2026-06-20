<?php
	include 'connection.php';
	include 'geo.php';
	session_start();
	if(!isset($_SESSION['log_email']) || !isset($_SESSION['cur_restaurant'])){ http_response_code(403); exit; }
	// enforce the delivery radius server-side (offset-aware)
	ensure_user_location($con);
	$rgeo = mysqli_prepare($con, "SELECT lat,lng FROM restaurants WHERE email=? LIMIT 1");
	mysqli_stmt_bind_param($rgeo, "s", $_SESSION['cur_restaurant']);
	mysqli_stmt_execute($rgeo);
	$rg = mysqli_fetch_assoc(mysqli_stmt_get_result($rgeo));
	if(!$rg || $rg['lat'] === null){ http_response_code(400); echo 'restaurant has no location'; exit; }
	$dist = user_distance_km($rg['lat'], $rg['lng']);
	if($dist === null || $dist > DELIVERY_RADIUS_KM){ http_response_code(400); echo 'outside delivery range'; exit; }

	$order_from = $_SESSION['cur_restaurant'];
	$order_by   = $_SESSION['log_email'];
	$items   = $_POST['items']   ?? '';
	$total   = $_POST['total']   ?? '0';
	$address = trim($_POST['address'] ?? '');
	$otp     = $_POST['otp']     ?? '';
	if($items === '' || $address === ''){ http_response_code(400); echo 'missing address or items'; exit; }

	// assign the nearest online rider to the restaurant
	$rlat = (float)$rg['lat']; $rlng = (float)$rg['lng'];
	$riders = mysqli_query($con, "SELECT email,lat,lng FROM riders WHERE status='Online' AND lat IS NOT NULL");
	$rider = ''; $best = PHP_FLOAT_MAX;
	while($rr = mysqli_fetch_assoc($riders)){
		$d = haversine_km($rlat, $rlng, (float)$rr['lat'], (float)$rr['lng']);
		if($d < $best){ $best = $d; $rider = $rr['email']; }
	}
	if($rider === ''){
		$rider = 'Not Alloted (Refresh Page)';
		$rider_status = 'not allotted';
	} else {
		$rider_status = 'pending';
	}

	// pickup = restaurant's effective (offset-aware) position; destination = customer's location
	$reff = eff_coords($rg['lat'], $rg['lng']);
	$rlat_e = $reff[0]; $rlng_e = $reff[1];
	$dlat = $_SESSION['user_lat']; $dlng = $_SESSION['user_lng'];

	$ins = mysqli_prepare($con, "INSERT INTO `orders` (`order_from`,`order_by`,`rider`,`items`,`total`,`address`,`rider_status`,`otp`,`r_lat`,`r_lng`,`d_lat`,`d_lng`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
	mysqli_stmt_bind_param($ins, "ssssssssdddd", $order_from, $order_by, $rider, $items, $total, $address, $rider_status, $otp, $rlat_e, $rlng_e, $dlat, $dlng);
	mysqli_stmt_execute($ins);
	echo 'ok';
?>
