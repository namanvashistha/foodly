<?php
$error_msg="";
if(isset($_POST['login']) || isset($_POST['signup'])){
    include 'connection.php';
    session_start();

    if(isset($_POST['login'])){
        $log_email =$_POST['log_email'];
        $log_pass  =$_POST['log_pass'];
        $q="SELECT name,password from restaurants where email='$log_email'; ";
        $q1=mysqli_query($con,$q);
        $row=mysqli_fetch_array($q1);
        if($row['password'] == $log_pass){
            $_SESSION['restaurant_log_name'] =$row['name'];
            $_SESSION['restaurant_log_email'] =$log_email;
            $_SESSION['log_client'] ="restaurant";
            header("location:restaurant_home.php");
        }
        else{
            $error_msg="incorrect email or password";
        }
    }
    else if(isset($_POST['signup'])){
        $sign_name    =$_POST['sign_name'];
        $sign_pass    =$_POST['sign_pass'];
        $sign_email   =$_POST['sign_email'];
        $sign_phone   =$_POST['sign_phone'];
        $sign_address =$_POST['sign_address'];
        $sign_desc    =$_POST['sign_desc'];
        $q2="SELECT email from restaurants where email='$sign_email' ";
        $row=mysqli_query($con,$q2);
        $rowcount=mysqli_num_rows($row);
        if($rowcount>0){
            $error_msg= "email already exists";
        }
        else{
            $q1="INSERT INTO `restaurants` (`name`, `password`, `email`, `phone`, `address`,`description`) VALUES ('$sign_name', '$sign_pass', '$sign_email', '$sign_phone', '$sign_address','$sign_desc');";
            $q3=mysqli_query($con,$q1);
            if($q3){
                $_SESSION['restaurant_log_email'] =$sign_email;
                $_SESSION['restaurant_log_name'] =$sign_name;
                $_SESSION['log_client'] ="restaurant";
                header("location:restaurant_home.php");
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Foodly for Restaurants — Partner sign in</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="css/theme.css?v=<?php echo @filemtime('css/theme.css'); ?>">
    <link rel="stylesheet" href="css/auth.css?v=<?php echo @filemtime('css/auth.css'); ?>">
</head>
<body>
<div class="auth-wrap">

    <!-- ===== brand panel ===== -->
    <aside class="auth-brand" style="background-image:url('images/8.jpg');">
        <a class="auth-back" href="index.php">&larr; Back to Foodly</a>
        <div class="auth-brand-body">
            <span class="eyebrow">For restaurants</span>
            <h1>Fill more tables, fire more orders.</h1>
            <p>List your kitchen on Foodly and reach hungry customers nearby. Menu and live orders, all in one place.</p>
            <ul class="auth-points">
                <li><span class="tick">&#10003;</span> Reach customers around you</li>
                <li><span class="tick">&#10003;</span> Manage menu and live orders</li>
                <li><span class="tick">&#10003;</span> Get started with zero setup cost</li>
            </ul>
        </div>
        <div class="auth-roles">
            <span class="lbl">Join as</span>
            <a href="index.php">Customer</a>
            <a class="current" href="restaurant_sign.php">Restaurant</a>
            <a href="rider_sign.php">Rider</a>
            <a href="support_sign.php">Support</a>
        </div>
    </aside>

    <!-- ===== form panel ===== -->
    <main class="auth-form-side">
        <div class="auth-card">
            <a class="wordmark" href="index.php">foodly<span class="dot">.</span></a>
            <span class="role-tag">Restaurant partner</span>
            <h2>Welcome to your kitchen.</h2>

            <div class="tabs">
                <button class="tab active" data-tab="login">Log in</button>
                <button class="tab" data-tab="signup">Sign up</button>
            </div>

            <!-- login -->
            <form class="tab-panel active" data-panel="login" method="POST">
                <div class="field">
                    <label for="log_email">Email</label>
                    <input class="input" type="text" id="log_email" name="log_email" placeholder="Enter email" required>
                </div>
                <div class="field">
                    <label for="log_pass">Password</label>
                    <input class="input" type="password" id="log_pass" name="log_pass" placeholder="Enter password" required>
                </div>
                <div id="log_error_msg" class="error_msg"><?php if($error_msg=="incorrect email or password") echo $error_msg; ?></div>
                <button class="btn btn-primary" type="submit" name="login" value="login">Log in</button>
                <div class="auth-foot">New partner? <a onclick="showTab('signup')">Create a restaurant account</a></div>
            </form>

            <!-- signup -->
            <form class="tab-panel" data-panel="signup" method="POST">
                <div class="field">
                    <label for="sign_name">Restaurant name</label>
                    <input class="input" type="text" id="sign_name" name="sign_name" placeholder="Enter name" required>
                </div>
                <div class="field">
                    <label for="sign_email">Email</label>
                    <input class="input" type="text" id="sign_email" name="sign_email" placeholder="Enter email" required>
                </div>
                <div class="field">
                    <label for="sign_pass">Password</label>
                    <input class="input" type="password" id="sign_pass" name="sign_pass" placeholder="Create a password" required>
                </div>
                <div class="field">
                    <label for="sign_phone">Phone</label>
                    <input class="input" type="text" id="sign_phone" name="sign_phone" placeholder="Enter phone" required>
                </div>
                <div class="field">
                    <label for="sign_address">Address</label>
                    <input class="input" type="text" id="sign_address" name="sign_address" placeholder="Enter address" required>
                </div>
                <div class="field">
                    <label for="sign_desc">Description</label>
                    <input class="input" type="text" id="sign_desc" name="sign_desc" placeholder="A short line about your food" required>
                </div>
                <div id="sign_error_msg" class="error_msg"><?php if($error_msg=="email already exists") echo $error_msg; ?></div>
                <button class="btn btn-primary" type="submit" name="signup" value="Sign Up">Create account</button>
                <div class="auth-foot">Already a partner? <a onclick="showTab('login')">Log in</a></div>
            </form>
        </div>
    </main>
</div>
<script src="js/auth.js"></script>
</body>
</html>
