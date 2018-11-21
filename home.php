<?php
session_start();
if(!isset($_SESSION['log_email'])){
	header("location:index.php");
}
include 'connection.php';
$q="SELECT * FROM `restaurants`; ";
$q1=mysqli_query($con,$q);
?>
<!DOCTYPE html>
<html>
<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">
<head>
	<link rel="stylesheet" type="text/css" href="css/home.css">
	<title>home page</title>
	<link rel="shortcut icon" href="images/logo.png" type="image/png">
	<script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>
 
</head>

<body>
<ul class="links_head">
<li><img src="images\header_logo.jpeg" align="left" width="100" height="52"></li>
  <div class="dropdown">
    <button style= "float:right;"   class="dropbtn" onclick="myFunction()"><?php echo $_SESSION['log_name']; ?>
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content" id="myDropdown">
      <a href="#">Profile</a>
      <a href="#">Past orders</a>
      <a href="index.php">Logout</a></div></div>
</ul>
<center><h2>Restaurants</h2></center>






<ul class="cards">
	<?php
	while($row=mysqli_fetch_array($q1)){ ?>
  <li class="cards__item">
  	<a href="restaurant_menu.php?restaurant=<?php echo $row['email']; ?>">
    <div class="card">
      <div style="background-image: url(images/<?php  echo mt_rand(1,9);?>.jpg);" class="card__image"></div>
      <div class="card__content">
        <div class="card__title"><?php echo $row['name'];  ?></div><span><b><?php  echo ($row['status']=="Online")?"<font color='green'>Online</font>":"<b>Offline</b>";  ?></b></span>
        <p class="card__text"><?php echo $row['address']."<br>".$row['description'];  ?></p>
      </div>
    </div>
	</a>
  </li>
  <?php } ?>
</ul>





<div class="cards">
	<?php
	while($row=mysqli_fetch_array($q1)){ ?>
		<div class="card-container">
		<a href="restaurant_menu.php?restaurant=<?php echo $row['email']; ?>">
    		<div class="card">
    		
    				<h4><b><?php echo $row['name'];  ?></b></h4> 
    				<p><?php echo $row['status'];  ?></p>
    			
    		</div>
  		</a>
  	</div>
  	<?php } ?>
</div>


<div id="chat-box">
	<div id="msg-box">
	</div>
	<div>
		<input id="send_msg" type="text" name="msg" >
		<input type="submit" id="send_button" value="send" >
	</div>
</div>
<br><br><br><br>

 <div class="navbar">
        <a href="order_status.php">Active Orders</a>
        <a href="logout.php">Log Out</a>
        <div class="copy">&copy; foodly</div>
</div>
       	<a class="boxe" onclick="show_chat_box()" class="js-close-modal"><b>Support</b></a>
       	

<script>

	$(document).ready(function(){
	$('#send_button').click(function(){
		var send_msg = $('#send_msg').val();
		if($.trim(send_msg) !=''){
			$.ajax({
				url:"send_msg.php",
				method:"POST",
				data:{msg:send_msg,client:"user"},
				dataType:"text",
				success:function(data){
					$('#send_msg').val("");
				}
			});
		}
	});
	setInterval(function(){
		$('#msg-box').load("fetch_msg.php").fadeIn("slow");
	},1000);
});
</script>
<script src="js/home.js" type="text/javascript"></script>

</body>
</html>