<?php
include 'connection.php';
$q_fet="SELECT * from chat_support where 1;";
 $q1_fet=mysqli_query($con,$q_fet);
 $exec=mysqli_fetch_array($q1_fet);
 $q_fet="SELECT * from chat_support where 1;";
 $q1_fet=mysqli_query($con,$q_fet);
 while($row_fet=mysqli_fetch_array($q1_fet)){
 	echo $row_fet['txt']." ".$row_fet['client']."<br>";
 }
?>