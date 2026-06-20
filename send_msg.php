<?php
session_start();
include 'connection.php';

$msg = trim($_POST['msg'] ?? '');
if($msg === '') { exit; }
$msg_e = mysqli_real_escape_string($con, $msg);

if(isset($_SESSION['support_log_email'])){
    // ---- support agent replying to a ticket ----
    $exec   = $_SESSION['support_log_email'];
    $client = trim($_POST['ticket'] ?? '');
    if($client === '') { exit; }
    $exec_e   = mysqli_real_escape_string($con, $exec);
    $client_e = mysqli_real_escape_string($con, $client);
    // replying claims the ticket for this agent
    mysqli_query($con, "UPDATE chat_support SET executive='$exec_e' WHERE client='$client_e'");
    mysqli_query($con, "INSERT INTO chat_support (`client`,`executive`,`txt_from`,`txt_to`,`txt`) VALUES ('$client_e','$exec_e','$exec_e','$client_e','$msg_e')");
}
else if(isset($_SESSION['log_email'])){
    // ---- customer sending into their own ticket ----
    $client   = $_SESSION['log_email'];
    $client_e = mysqli_real_escape_string($con, $client);
    // carry forward the current assignment, if any
    $r = mysqli_fetch_array(mysqli_query($con, "SELECT executive FROM chat_support WHERE client='$client_e' AND executive<>'Not Allotted' ORDER BY txt_id DESC LIMIT 1"));
    $exec   = $r ? $r['executive'] : 'Not Allotted';
    $exec_e = mysqli_real_escape_string($con, $exec);
    mysqli_query($con, "INSERT INTO chat_support (`client`,`executive`,`txt_from`,`txt_to`,`txt`) VALUES ('$client_e','$exec_e','$client_e','support','$msg_e')");
}
?>
