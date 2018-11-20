<?php
	session_start();
	if(!isset($_SESSION['log_email'])){
		header("location:index.php");
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>order status</title>
 <link rel="shortcut icon" href="images\logo.png" type="image/png">
    <link rel="stylesheet" type="text/css" href="css\order_status.css">
    <script
      src="https://code.jquery.com/jquery-3.3.1.min.js"
      integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
      crossorigin="anonymous"></script>
</head>
<body style="font-family: Helvetica;">
	 <div class="topnav">
        <img src="images/header_logo.jpeg" height= "45px" width = "110px" align="left"></div>

    <div id="order_box">
        
    </div>

     <div class="navbar">
       
       
        
        <div class="copy">&copy; foodly</div>
        </div>
        <script type="text/javascript">
            setInterval(function(){
                $('#order_box').load("fetch_order.php").fadeIn("slow");
            },1000);
        </script>
</body>
</html>