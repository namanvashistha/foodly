<?php
session_start();
if(!isset($_SESSION['support_log_email'])){
	header("location:index.php");
	exit;
}
include 'connection.php';
$support_log_email= $_SESSION['support_log_email'];

if(isset($_POST['line'])){
        $line=$_POST['line'];
        $q="UPDATE support SET status='$line' where email='$support_log_email' ;";
        mysqli_query($con,$q);
}

$sd = mysqli_fetch_array(mysqli_query($con,"select * from support where email='$support_log_email';"));
$isOnline = ($sd['status'] == 'Go Online');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Foodly — Support desk</title>
	<link rel="shortcut icon" href="images/logo.png" type="image/png">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="stylesheet" href="css/theme.css?v=<?php echo @filemtime('css/theme.css'); ?>">
	<link rel="stylesheet" href="css/app.css?v=<?php echo @filemtime('css/app.css'); ?>">
	<link rel="stylesheet" href="css/support_home.css?v=<?php echo @filemtime('css/support_home.css'); ?>">
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
</head>
<body>

	<header class="topbar">
		<div class="wrap">
			<a class="wordmark" href="support_home.php">foodly<span class="dot">.</span><span class="role">Support</span></a>
			<nav class="topbar-nav">
				<div class="dropdown">
					<button class="dropbtn" onclick="myFunction()">
						<span class="avatar"><?php echo strtoupper(substr($sd['name'],0,1)); ?></span>
						<span class="who"><?php echo htmlspecialchars($sd['name']); ?></span>
						<span class="caret">&#9662;</span>
					</button>
					<div class="dropdown-content" id="myDropdown">
						<a href="logout.php">Log out</a>
					</div>
				</div>
			</nav>
		</div>
	</header>

	<div class="page wrap">
		<div class="page-head">
			<div>
				<span class="eyebrow">Support desk</span>
				<h1><?php echo htmlspecialchars($sd['name']); ?></h1>
				<p class="sub"><?php echo htmlspecialchars($sd['email']); ?> &middot; <span class="statebadge <?php echo $isOnline ? 'on':''; ?>"><i class="ping"></i><?php echo $isOnline ? 'Online' : 'Offline'; ?></span></p>
			</div>
			<form method="post">
				<button class="btn <?php echo $isOnline ? 'btn-soft' : 'btn-primary'; ?>" type="submit" name="line" value="<?php echo $isOnline ? 'Go Offline':'Go Online'; ?>">
					<?php echo $isOnline ? 'Go offline' : 'Go online'; ?>
				</button>
			</form>
		</div>

		<div class="support-grid">
			<section class="panel ticket-pane">
				<div class="panel-title">Tickets</div>
				<p class="panel-sub">Pick one to reply. Replying assigns it to you.</p>
				<div id="ticket_list" class="ticket-list"></div>
			</section>

			<section class="panel convo-pane">
				<div id="thread_view" class="thread-view">
					<div class="empty"><div class="empty-ic">&#128172;</div><h3>Select a ticket</h3><p>Choose a customer conversation on the left to read and reply.</p></div>
				</div>
				<div class="thread-reply" id="thread_reply" style="display:none;">
					<input id="reply" class="input" type="text" placeholder="Type a reply" autocomplete="off">
					<button id="reply_send" class="btn btn-primary" type="button">Send</button>
				</div>
			</section>
		</div>
	</div>

	<script>
		var currentTicket = null;

		function loadTickets() {
			$('#ticket_list').load("fetch_tickets.php", function () {
				if (currentTicket) {
					$('#ticket_list .ticket').each(function () {
						$(this).toggleClass('active', this.dataset.client === currentTicket);
					});
				}
			});
		}
		function loadThread() {
			if (!currentTicket) return;
			$('#thread_view').load("fetch_ticket.php?client=" + encodeURIComponent(currentTicket), function () {
				var b = document.getElementById('thread_body');
				if (b) b.scrollTop = b.scrollHeight;
			});
		}
		function openTicket(client) {
			currentTicket = client;
			$('#thread_reply').show();
			$('#ticket_list .ticket').each(function () { $(this).toggleClass('active', this.dataset.client === client); });
			loadThread();
		}
		function assignTicket() {
			if (!currentTicket) return;
			$.post("assign_ticket.php", { client: currentTicket }, function () { loadThread(); loadTickets(); });
		}
		function sendReply() {
			var msg = $('#reply').val();
			if (!currentTicket || $.trim(msg) === '') return;
			$.post("send_msg.php", { msg: msg, ticket: currentTicket }, function () {
				$('#reply').val(""); loadThread(); loadTickets();
			});
		}

		$(document).ready(function () {
			loadTickets();
			$('#reply_send').click(sendReply);
			$('#reply').keydown(function (e) { if (e.key === 'Enter') { e.preventDefault(); sendReply(); } });
			setInterval(function () { loadTickets(); loadThread(); }, 2500);
		});
	</script>
	<script src="js/support_home.js"></script>
</body>
</html>
