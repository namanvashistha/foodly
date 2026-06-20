<?php
$error_msg="";
if(isset($_POST['login']) || isset($_POST['signup'])){
    include 'connection.php';
    include 'auth_lib.php';
    session_start();

    if(isset($_POST['login'])){
        $log_email =$_POST['log_email'];
        $log_pass  =$_POST['log_pass'];
        $row = db_login($con, 'riders', $log_email, $log_pass);
        if($row){
            auth_session_start();
            $_SESSION['rider_log_name'] =$row['name'];
            $_SESSION['rider_log_email'] =$log_email;
            $_SESSION['log_client'] ="rider";
            header("location:rider_home.php");
            exit;
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
        if(db_email_exists($con, 'riders', $sign_email)){
            $error_msg= "email already exists";
        }
        else{
            $hash = db_hash($sign_pass);
            $ins = mysqli_prepare($con, "INSERT INTO `riders` (`name`,`password`,`email`,`phone`,`address`) VALUES (?,?,?,?,?)");
            mysqli_stmt_bind_param($ins, "sssss", $sign_name, $hash, $sign_email, $sign_phone, $sign_address);
            if(mysqli_stmt_execute($ins)){
                auth_session_start();
                $_SESSION['rider_log_email'] =$sign_email;
                $_SESSION['rider_log_name'] =$sign_name;
                $_SESSION['log_client'] ="rider";
                header("location:rider_home.php");
                exit;
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
    <title>Foodly for Riders — Rider sign in</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="css/theme.css?v=<?php echo @filemtime('css/theme.css'); ?>">
    <link rel="stylesheet" href="css/auth.css?v=<?php echo @filemtime('css/auth.css'); ?>">
</head>
<body>
<div class="auth-wrap">

    <!-- ===== brand panel ===== -->
    <aside class="auth-brand" style="background-image:url('images/9.jpg');">
        <a class="auth-back" href="index.php">&larr; Back to Foodly</a>
        <div class="auth-brand-body">
            <span class="eyebrow">For riders</span>
            <h1>Deliver on your own schedule.</h1>
            <p>Pick up, drop off, and build your streak. Flexible hours and steady earnings, right across the city.</p>
            <ul class="auth-points">
                <li><span class="tick">&#10003;</span> Choose your own hours</li>
                <li><span class="tick">&#10003;</span> Steady, transparent earnings</li>
                <li><span class="tick">&#10003;</span> Build streaks and bonuses</li>
            </ul>
        </div>
        <div class="auth-roles">
            <span class="lbl">Join as</span>
            <a href="index.php">Customer</a>
            <a href="restaurant_sign.php">Restaurant</a>
            <a class="current" href="rider_sign.php">Rider</a>
            <a href="support_sign.php">Support</a>
        </div>
    </aside>

    <!-- ===== form panel ===== -->
    <main class="auth-form-side">
        <div class="auth-card">
            <a class="wordmark" href="index.php">foodly<span class="dot">.</span></a>
            <span class="role-tag">Rider</span>
            <h2>Hit the road with Foodly.</h2>

            <div class="tabs">
                <button class="tab active" data-tab="login">Log in</button>
                <button class="tab" data-tab="signup">Sign up</button>
            </div>

            <!-- login -->
            <form class="tab-panel active" data-panel="login" method="POST">
                <div class="hint">Try the demo: <b>speedy@rider.test</b> / <b>rider123</b></div>
                <div class="field">
                    <label for="log_email">Email</label>
                    <input class="input" type="text" id="log_email" name="log_email" placeholder="Enter email" value="speedy@rider.test" required>
                </div>
                <div class="field">
                    <label for="log_pass">Password</label>
                    <input class="input" type="password" id="log_pass" name="log_pass" placeholder="Enter password" value="rider123" required>
                </div>
                <div id="log_error_msg" class="error_msg"><?php if($error_msg=="incorrect email or password") echo $error_msg; ?></div>
                <button class="btn btn-primary" type="submit" name="login" value="login">Log in</button>
                <div class="auth-foot">New rider? <a onclick="showTab('signup')">Create a rider account</a></div>
            </form>

            <!-- signup -->
            <form class="tab-panel" data-panel="signup" method="POST">
                <div class="field">
                    <label for="sign_name">Full name</label>
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
                <div id="sign_error_msg" class="error_msg"><?php if($error_msg=="email already exists") echo $error_msg; ?></div>
                <button class="btn btn-primary" type="submit" name="signup" value="Sign Up">Create account</button>
                <div class="auth-foot">Already riding? <a onclick="showTab('login')">Log in</a></div>
            </form>
        </div>
    </main>
</div>
<script src="js/auth.js"></script>
</body>
</html>
