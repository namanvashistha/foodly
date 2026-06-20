<?php
/* Foodly demo simulation tick — idempotent, time-based. Called by the
   polling pages. Advances orders on timers and moves riders along their
   restaurant -> destination route. No background worker needed. */
session_start();
include 'connection.php';
include 'geo.php';
if(!isset($_SESSION['log_email']) && !isset($_SESSION['restaurant_log_email'])
   && !isset($_SESSION['rider_log_email']) && !isset($_SESSION['support_log_email'])){
    http_response_code(403); exit;
}
$travel = TRAVEL_SECONDS;
$changed = 0;

// 1. restaurant auto-accepts a placed order (60s)
mysqli_query($con, "UPDATE orders SET status='accepted'
    WHERE status='placed' AND TIMESTAMPDIFF(SECOND, instance, NOW()) >= 60");
$changed += mysqli_affected_rows($con);
// 2. assigned rider auto-accepts (60s)
mysqli_query($con, "UPDATE orders SET rider_status='accepted'
    WHERE rider_status='pending' AND status IN('accepted','On the way')
    AND TIMESTAMPDIFF(SECOND, updated, NOW()) >= 60");
$changed += mysqli_affected_rows($con);
// 3. rider heads out — stamp the journey start
mysqli_query($con, "UPDATE orders SET rider_status='On the way', status='On the way', otw_at=NOW()
    WHERE rider_status='accepted' AND status='accepted'
    AND TIMESTAMPDIFF(SECOND, updated, NOW()) >= 60");
$changed += mysqli_affected_rows($con);
// 4. delivered once the 2-minute journey completes
mysqli_query($con, "UPDATE orders SET rider_status='delivered', status='delivered'
    WHERE status='On the way' AND otw_at IS NOT NULL AND TIMESTAMPDIFF(SECOND, otw_at, NOW()) >= $travel");
$changed += mysqli_affected_rows($con);

/* ---- move riders along their route (so other views match the live map) ---- */
$active = mysqli_query($con, "SELECT o.rider, o.status, o.r_lat, o.r_lng, o.d_lat, o.d_lng,
        TIMESTAMPDIFF(SECOND, o.otw_at, NOW()) elapsed
    FROM orders o JOIN riders ri ON o.rider = ri.email
    WHERE o.status IN('accepted','On the way') AND o.r_lat IS NOT NULL");
$busy = array();
while($a = mysqli_fetch_assoc($active)){
    $busy[$a['rider']] = true;
    if($a['status'] === 'On the way' && $a['elapsed'] !== null){
        $p = max(0, min(1, $a['elapsed'] / $travel));
    } else {
        $p = 0; // waiting at the restaurant
    }
    $nl = $a['r_lat'] + ($a['d_lat'] - $a['r_lat']) * $p;
    $ng = $a['r_lng'] + ($a['d_lng'] - $a['r_lng']) * $p;
    $u = mysqli_prepare($con, "UPDATE riders SET lat=?, lng=? WHERE email=?");
    mysqli_stmt_bind_param($u, "dds", $nl, $ng, $a['rider']);
    mysqli_stmt_execute($u);
}
// idle online riders drift a little
$idle = mysqli_query($con, "SELECT email, lat, lng FROM riders WHERE status='Online' AND lat IS NOT NULL");
while($i = mysqli_fetch_assoc($idle)){
    if(isset($busy[$i['email']])) continue;
    $nl = (float)$i['lat'] + (mt_rand(-3,3) / 10000.0);
    $ng = (float)$i['lng'] + (mt_rand(-3,3) / 10000.0);
    $u = mysqli_prepare($con, "UPDATE riders SET lat=?, lng=? WHERE email=?");
    mysqli_stmt_bind_param($u, "dds", $nl, $ng, $i['email']);
    mysqli_stmt_execute($u);
}
echo $changed > 0 ? 'changed' : 'ok';
?>
