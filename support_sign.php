<?php
$error_msg="";
if(isset($_POST['login']) || isset($_POST['signup'])){
    include 'connection.php';
    include 'auth_lib.php';
    session_start();

    if(isset($_POST['login'])){
        $log_email =$_POST['log_email'];
        $log_pass  =$_POST['log_pass'];
        $row = db_login($con, 'support', $log_email, $log_pass);
        if($row){
            auth_session_start();
            $_SESSION['support_log_name'] =$row['name'];
            $_SESSION['support_log_email'] =$log_email;
            header("location:support_home.php");
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
        if(db_email_exists($con, 'support', $sign_email)){
            $error_msg= "email already exists";
        }
        else{
            $hash = db_hash($sign_pass);
            $ins = mysqli_prepare($con, "INSERT INTO `support` (`name`,`password`,`email`,`phone`,`address`) VALUES (?,?,?,?,?)");
            mysqli_stmt_bind_param($ins, "sssss", $sign_name, $hash, $sign_email, $sign_phone, $sign_address);
            if(mysqli_stmt_execute($ins)){
                auth_session_start();
                $_SESSION['support_log_email'] =$sign_email;
                $_SESSION['support_log_name'] =$sign_name;
                header("location:support_home.php");
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
    <title>Foodly for Support — Agent sign in</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="css/theme.css?v=<?php echo @filemtime('css/theme.css'); ?>">
    <link rel="stylesheet" href="css/auth.css?v=<?php echo @filemtime('css/auth.css'); ?>">
</head>
<body>
<div class="auth-wrap">

    <!-- ===== brand panel ===== -->
    <aside class="auth-brand" style="background-image:url('images/3.jpg');">
        <a class="auth-back" href="index.php">&larr; Back to Foodly</a>
        <div class="auth-brand-body">
            <span class="eyebrow">For support executives</span>
            <h1>Keep every order on track.</h1>
            <p>Help customers in real time over chat and make sure no order slips through the cracks.</p>
            <ul class="auth-points">
                <li><span class="tick">&#10003;</span> Real-time chat tools</li>
                <li><span class="tick">&#10003;</span> Help customers directly</li>
                <li><span class="tick">&#10003;</span> Be the team people trust</li>
            </ul>
        </div>
        <div class="auth-roles">
            <span class="lbl">Join as</span>
            <a href="index.php">Customer</a>
            <a href="restaurant_sign.php">Restaurant</a>
            <a href="rider_sign.php">Rider</a>
            <a class="current" href="support_sign.php">Support</a>
        </div>
    </aside>

    <!-- ===== form panel ===== -->
    <main class="auth-form-side">
        <div class="auth-card">
            <a class="wordmark" href="index.php">foodly<span class="dot">.</span></a>
            <span class="role-tag">Chat support</span>
            <h2>Join the support desk.</h2>

            <div class="tabs">
                <button class="tab active" data-tab="login">Log in</button>
                <button class="tab" data-tab="signup">Sign up</button>
            </div>

            <!-- login -->
            <form class="tab-panel active" data-panel="login" method="POST">
                <div class="hint">Try the demo: <b>henry@support.test</b> / <b>support123</b></div>
                <div class="field">
                    <label for="log_email">Email</label>
                    <input class="input" type="text" id="log_email" name="log_email" placeholder="Enter email" value="henry@support.test" required>
                </div>
                <div class="field">
                    <label for="log_pass">Password</label>
                    <input class="input" type="password" id="log_pass" name="log_pass" placeholder="Enter password" value="support123" required>
                </div>
                <div id="log_error_msg" class="error_msg"><?php if($error_msg=="incorrect email or password") echo $error_msg; ?></div>
                <button class="btn btn-primary" type="submit" name="login" value="login">Log in</button>
                <div class="auth-foot">New here? <a onclick="showTab('signup')">Create a support account</a></div>
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
                <div class="field">
                    <label for="sign_desc">Short bio</label>
                    <input class="input" type="text" id="sign_desc" name="sign_desc" placeholder="A line about you" required>
                </div>
                <div id="sign_error_msg" class="error_msg"><?php if($error_msg=="email already exists") echo $error_msg; ?></div>
                <button class="btn btn-primary" type="submit" name="signup" value="Sign Up">Create account</button>
                <div class="auth-foot">Already on the team? <a onclick="showTab('login')">Log in</a></div>
            </form>
        </div>
    </main>
</div>
<script src="js/auth.js"></script>
</body>
</html>
