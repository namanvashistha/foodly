<?php
session_start();
if(!isset($_SESSION['log_email'])){
	header("location:index.php");
}
include 'connection.php';
$no_items=$_GET['count'];
$restaurant= $_SESSION['cur_restaurant'];
$total=0;
$subtotal=0;
?>
<table>
<?php
for ($i=0;$i<$no_items;$i++) { 
	$item_name=$_GET['item'.$i];
	$item_quantity=$_GET['quantity'.$i];
	$q="SELECT * FROM `$restaurant` where name='$item_name'; ";
	$q1=mysqli_query($con,$q);
	$row=mysqli_fetch_array($q1);

	$item_price_total=$row['price']*$item_quantity;
	$item_discount=$row['discount'];
	$item_price_discount=$item_price_total-(0.01*$item_price_total*$item_discount);
	$total+=$item_price_discount;
	$subtotal+=$item_price_total;
	echo "<tr><td>".$item_name."</td><td>X ".$item_quantity."</td><td>= <strike>".$item_price_total."</strike></td><td>".$item_price_discount."</td></tr>";
}?>	
	</table>

	<?php
	$gst=0.05*$total;
	$savings=$subtotal-$total;
	echo "<br>Subtotal = ₹".$subtotal."<br>"."GST = ₹".$gst."<br>";
	echo "Savings= ₹".$savings."</br>";
	echo "<b>Total= ₹".$total."</b>";
	?>
