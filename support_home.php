<?php
session_start();
if(!isset($_SESSION['support_log_email'])){
	header("location:index.php");
}
include 'connection.php';
$support_log_email= $_SESSION['support_log_email'];

if(isset($_POST['line'])){
        $line=$_POST['line'];
        $q="UPDATE support SET status='$line' where email='$support_log_email' ;";
        mysqli_query($con,$q);
    }


?>
<!DOCTYPE html>
<html>
<head>
	<title>support home</title>
	<link rel="shortcut icon" href="images/logo.png" type="image/png">
     <link rel="stylesheet" type="text/css" href="css/support_home.css">
     <script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>
</head>
<body>
     <div class="topnav">
        <img src="images/header_logo.jpeg" height= "45px" width = "110px" align="left"></div>
	<h3><?php echo $_SESSION['support_log_name'];?></h3>

    <?php
        $q="select status from support where email='$support_log_email';";
        $q1=mysqli_query($con,$q);
        $row=mysqli_fetch_array($q1);
        echo "You are currently ";
        echo ($row['status'] == 'Go Online') ? 'Online':'Offline';
        ?>
        <form method="post">
            <input type="submit" name="line" value="<?php echo ($row['status'] == 'Go Online') ? 'Go Offline':'Go Online'; ?>" >
        </form> 

        <div id="chat-box" >
            <div id="msg-box">
                msg-box
            </div>
            <div>
                <input type="" name="">
                <button>send</button>
            </div>
        </div>


 <div class="navbar">
        <div class="for">Chat with</div> 
        <a href="#">Restaurant</a>
        <a href="#">User</a>
        <a href="#">Rider</a>
           <a href="index.php">Logout</a>
        <div class="copy">&copy; foodly</div>
        </div>
        <script src="js/support_home.js"></script>
</body>
</html>