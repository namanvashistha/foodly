<?php
session_start();
include 'connection.php';
if(!isset($_SESSION['support_log_email'])){ exit; }
$me = $_SESSION['support_log_email'];

// latest message per ticket (one row per customer/client)
$sql = "SELECT cs.* FROM chat_support cs
        JOIN (SELECT client, MAX(txt_id) AS mid FROM chat_support GROUP BY client) m
        ON cs.client = m.client AND cs.txt_id = m.mid
        ORDER BY cs.txt_id DESC";
$q = mysqli_query($con, $sql);
$tickets = array();
while($r = mysqli_fetch_array($q)){ $tickets[] = $r; }

if(count($tickets) === 0){
    echo '<div class="empty"><div class="empty-ic">&#128237;</div><h3>No tickets yet</h3><p>Customer chats will show up here as they come in.</p></div>';
    exit;
}

foreach($tickets as $t){
    $client = $t['client'];
    $exec   = $t['executive'];
    if($exec === 'Not Allotted'){ $chip = '<span class="tk-chip open">Unassigned</span>'; }
    else if($exec === $me){ $chip = '<span class="tk-chip mine">Assigned to you</span>'; }
    else { $chip = '<span class="tk-chip other">'.htmlspecialchars($exec).'</span>'; }
    $preview = $t['txt'];
    if(strlen($preview) > 48){ $preview = substr($preview,0,48).'&hellip;'; } else { $preview = htmlspecialchars($preview); }
    $who = ($t['txt_from'] === $client) ? '' : 'You: ';
    echo '<button type="button" class="ticket" data-client="'.htmlspecialchars($client).'" onclick="openTicket(this.dataset.client)">'
        .'<div class="tk-top"><span class="tk-name">'.htmlspecialchars($client).'</span>'.$chip.'</div>'
        .'<div class="tk-preview">'.$who.$preview.'</div>'
        .'</button>';
}
?>
