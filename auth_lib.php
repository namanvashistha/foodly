<?php
/* Foodly auth helpers — prepared statements + password hashing.
   $table is always a trusted string literal from the caller (never user input),
   so interpolating it into the SQL is safe; all VALUES are bound parameters. */

function db_login($con, $table, $email, $pass){
    $stmt = mysqli_prepare($con, "SELECT name, password FROM `$table` WHERE email=? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if(!$row){ return false; }
    $stored = $row['password'];
    if(password_verify($pass, $stored)){
        return $row;
    }
    // legacy plaintext seed account -> verify, then transparently upgrade to a hash
    if(hash_equals($stored, $pass)){
        $new = password_hash($pass, PASSWORD_DEFAULT);
        $u = mysqli_prepare($con, "UPDATE `$table` SET password=? WHERE email=?");
        mysqli_stmt_bind_param($u, "ss", $new, $email);
        mysqli_stmt_execute($u);
        return $row;
    }
    return false;
}

function db_email_exists($con, $table, $email){
    $stmt = mysqli_prepare($con, "SELECT 1 FROM `$table` WHERE email=? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    return mysqli_stmt_num_rows($stmt) > 0;
}

function db_hash($pass){ return password_hash($pass, PASSWORD_DEFAULT); }

/* harden the session on a successful login/signup */
function auth_session_start(){
    session_regenerate_id(true);
}
