<?php
session_start();
include 'connection.php';
if(!isset($_SESSION['log_email'])){ http_response_code(403); exit; }

$order_id = intval($_POST['order_id'] ?? 0);
$stars    = intval($_POST['stars'] ?? 0);
$review   = trim($_POST['review'] ?? '');
if($stars < 1 || $stars > 5 || $order_id < 1){ http_response_code(400); echo 'invalid'; exit; }

$cust = $_SESSION['log_email'];
// the order must belong to this customer and be delivered
$o = mysqli_prepare($con, "SELECT order_from, status FROM orders WHERE order_id=? AND order_by=? LIMIT 1");
mysqli_stmt_bind_param($o, "is", $order_id, $cust);
mysqli_stmt_execute($o);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($o));
if(!$row || $row['status'] !== 'delivered'){ http_response_code(400); echo 'not deliverable'; exit; }

$rest = $row['order_from'];
$ins = mysqli_prepare($con, "INSERT INTO ratings (restaurant,customer,order_id,stars,review) VALUES (?,?,?,?,?)
    ON DUPLICATE KEY UPDATE stars=VALUES(stars), review=VALUES(review)");
mysqli_stmt_bind_param($ins, "ssiss", $rest, $cust, $order_id, $stars, $review);
mysqli_stmt_execute($ins);
echo 'ok';
?>
