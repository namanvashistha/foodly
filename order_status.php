<?php
echo $_POST['user']."<br>".$_POST['restaurant']."<br>".$_POST['total']."<br>";
$no_items=$_POST['no_items'];
for ($i=0;$i<$no_items;$i++) { 
	$item_name=$_POST['item'.$i];
	$item_quantity=$_POST['quantity'.$i];
	echo $item_name." ".$item_quantity."<br>";

}
?>