<?php
session_start();
include 'connection.php';
include 'geo.php';
if(!isset($_SESSION['log_email'])){ http_response_code(403); exit; }
header('Content-Type: application/json');

$email = $_SESSION['log_email'];
$q = mysqli_prepare($con, "SELECT *, TIMESTAMPDIFF(SECOND, otw_at, NOW()) AS otw_elapsed FROM orders WHERE order_by=? ORDER BY order_id DESC");
mysqli_stmt_bind_param($q, "s", $email);
mysqli_stmt_execute($q);
$res = mysqli_stmt_get_result($q);

$orders = array();
while($o = mysqli_fetch_assoc($res)){
    if($o['status'] === 'delivered' || $o['status'] === 'declined') continue; // active only

    $items = array();
    $list = preg_split('/ /', trim($o['items']));
    for($i = 0; $i + 1 < count($list); $i += 2){
        $sno = intval($list[$i]);
        $rfrom = mysqli_real_escape_string($con, $o['order_from']);
        $r = mysqli_fetch_assoc(mysqli_query($con, "SELECT name FROM menu WHERE sno='$sno' AND restaurant_id='$rfrom'"));
        $items[] = array('name' => $r['name'] ?: ('Item #'.$sno), 'qty' => $list[$i+1]);
    }
    $rn = mysqli_fetch_assoc(mysqli_query($con, "SELECT name FROM restaurants WHERE email='".mysqli_real_escape_string($con, $o['order_from'])."'"));

    $orders[] = array(
        'id'           => (int)$o['order_id'],
        'status'       => $o['status'],
        'rider_status' => $o['rider_status'],
        'rider'        => $o['rider'],
        'otp'          => $o['otp'],
        'total'        => floor($o['total']),
        'address'      => $o['address'],
        'restaurant'   => $rn['name'] ?: $o['order_from'],
        'r'            => array($o['r_lat'] !== null ? (float)$o['r_lat'] : null, $o['r_lng'] !== null ? (float)$o['r_lng'] : null),
        'd'            => array($o['d_lat'] !== null ? (float)$o['d_lat'] : null, $o['d_lng'] !== null ? (float)$o['d_lng'] : null),
        'otw_elapsed'  => $o['otw_elapsed'] !== null ? (int)$o['otw_elapsed'] : null,
        'travel'       => TRAVEL_SECONDS,
        'items'        => $items
    );
}
echo json_encode($orders);
?>
