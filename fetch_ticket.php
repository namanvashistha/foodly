<?php
session_start();
include 'connection.php';
if(!isset($_SESSION['support_log_email'])){ exit; }
$me = $_SESSION['support_log_email'];

$client = trim($_GET['client'] ?? '');
if($client === ''){ exit; }
$client_e = mysqli_real_escape_string($con, $client);

$q = mysqli_query($con, "SELECT * FROM chat_support WHERE client='$client_e' ORDER BY txt_id ASC");
$rows = array();
while($r = mysqli_fetch_array($q)){ $rows[] = $r; }

$exec = 'Not Allotted';
foreach($rows as $r){ if($r['executive'] !== 'Not Allotted'){ $exec = $r['executive']; } }

// header: who + assignment + assign button
$assigned_to_me = ($exec === $me);
$status = ($exec === 'Not Allotted') ? '<span class="tk-chip open">Unassigned</span>'
        : ($assigned_to_me ? '<span class="tk-chip mine">Assigned to you</span>' : '<span class="tk-chip other">'.htmlspecialchars($exec).'</span>');
echo '<div class="thread-head">';
echo '<div><div class="thread-name">'.htmlspecialchars($client).'</div>'.$status.'</div>';
if(!$assigned_to_me){
    echo '<button type="button" class="btn btn-primary" onclick="assignTicket()">Assign to me</button>';
}
echo '</div>';

echo '<div class="thread-body" id="thread_body">';
foreach($rows as $r){
    $mine = ($r['txt_from'] === $me);
    $is_customer = ($r['txt_from'] === $client);
    $cls = $mine ? 'me' : 'them';
    echo '<div class="bubble '.$cls.'">'.htmlspecialchars($r['txt']).'</div>';
}
echo '</div>';
?>
