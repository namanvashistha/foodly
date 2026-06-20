<?php
session_start();
include 'connection.php';
if(!isset($_SESSION['log_email'])){ exit; }

$client   = $_SESSION['log_email'];
$client_e = mysqli_real_escape_string($con, $client);
$q = mysqli_query($con, "SELECT * FROM chat_support WHERE client='$client_e' ORDER BY txt_id ASC");
$rows = array();
while($r = mysqli_fetch_array($q)){ $rows[] = $r; }

$exec = 'Not Allotted';
foreach($rows as $r){ if($r['executive'] !== 'Not Allotted'){ $exec = $r['executive']; } }

if(count($rows) === 0){
    echo '<div class="chat-empty-state">Say hello to start a conversation with Foodly support.</div>';
} else {
    if($exec === 'Not Allotted'){
        echo '<div class="chat-assign waiting">Waiting for an agent to pick up your chat&hellip;</div>';
    } else {
        echo '<div class="chat-assign">Connected with <b>'.htmlspecialchars($exec).'</b></div>';
    }
    foreach($rows as $r){
        $mine = ($r['txt_from'] === $client);
        echo '<div class="bubble '.($mine ? 'me' : 'them').'">'.htmlspecialchars($r['txt']).'</div>';
    }
}
?>
