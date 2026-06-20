<?php
session_start();
include 'connection.php';
if(!isset($_SESSION['support_log_email'])){ http_response_code(403); exit; }
$me = $_SESSION['support_log_email'];

$client = trim($_POST['client'] ?? '');
if($client === ''){ exit; }
$me_e     = mysqli_real_escape_string($con, $me);
$client_e = mysqli_real_escape_string($con, $client);

mysqli_query($con, "UPDATE chat_support SET executive='$me_e' WHERE client='$client_e'");
echo 'ok';
?>
