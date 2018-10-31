<?php
include 'connection.php';
 $msg=$_POST["msg"];
 $client=$_POST["client"] ;
 $q_snd="INSERT INTO chat_support (`client`,`txt`) values ('$client','$msg') ";
 mysqli_query($con,$q_snd);
?>