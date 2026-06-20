<?php
session_start();
include 'connection.php';
include 'geo.php';
if(!isset($_SESSION['log_email'])){ http_response_code(403); exit; }
$lat = $_POST['lat'] ?? '';
$lng = $_POST['lng'] ?? '';
if(is_numeric($lat) && is_numeric($lng)){
    set_user_location($con, $lat, $lng, trim($_POST['place'] ?? ''));
    echo 'ok';
} else {
    http_response_code(400);
    echo 'invalid';
}
?>
