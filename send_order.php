<?php
	include 'connection.php';
	session_start();
	if(!isset($_SESSION['log_email']) || !isset($_SESSION['cur_restaurant'])){ http_response_code(403); exit; }

	$order_from = $_SESSION['cur_restaurant'];
	$order_by   = $_SESSION['log_email'];
	$items   = $_POST['items']   ?? '';
	$total   = $_POST['total']   ?? '0';
	$address = $_POST['address'] ?? '';
	$otp     = $_POST['otp']     ?? '';

	// pick an available rider: online, lowest streak
	$rrow = mysqli_fetch_array(mysqli_query($con, "SELECT email FROM riders WHERE status='Online' AND streak IN (SELECT MIN(streak) FROM riders WHERE status='Online')"));
	$rider = $rrow['email'] ?? '';

	if($rider === ''){
		$rider = 'Not Alloted (Refresh Page)';
		$rider_status = 'not allotted';
	} else {
		$rider_status = 'pending';
	}

	$ins = mysqli_prepare($con, "INSERT INTO `orders` (`order_from`,`order_by`,`rider`,`items`,`total`,`address`,`rider_status`,`otp`) VALUES (?,?,?,?,?,?,?,?)");
	mysqli_stmt_bind_param($ins, "ssssssss", $order_from, $order_by, $rider, $items, $total, $address, $rider_status, $otp);
	mysqli_stmt_execute($ins);

	// if nobody was free at insert time, try once more to allot a rider (fixed: was a broken UPDATE)
	mysqli_query($con, "UPDATE orders SET rider_status='pending', rider=(SELECT MAX(email) FROM riders WHERE status='Online' AND streak IN (SELECT MIN(streak) FROM riders WHERE status='Online')) WHERE rider_status='not allotted' AND (SELECT COUNT(*) FROM riders WHERE status='Online')>0");
	echo 'ok';
?>
