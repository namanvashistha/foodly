<?php
session_start();
if(!isset($_SESSION['restaurant_log_email'])){
	header("location:index.php");
	exit;
}
include 'connection.php';
$restaurant_log_email= $_SESSION['restaurant_log_email'];
if(isset($_POST['update'])){
	$item_name=$_POST['item_name'];
	$item_price=$_POST['item_price'];
	$item_discount=$_POST['item_discount'];
	$item_desc=$_POST['item_desc'];
	for($i=0;$i<sizeof($item_name);$i++){
		$q="SELECT name from menu where name='$item_name[$i]' and restaurant_id='$restaurant_log_email' ";
		$q1=mysqli_query($con,$q);
		$rowcount=mysqli_num_rows($q1);
		if(empty($item_name[$i]) || empty($item_price[$i]) || empty($item_discount[$i]) || empty($item_desc[$i]) || $rowcount>0)
            continue;
		$q="INSERT INTO menu (`restaurant_id`,`name`,`price`,`discount`,`description`) VALUES ('$restaurant_log_email','$item_name[$i]', '$item_price[$i]','$item_discount[$i]','$item_desc[$i]');";
		$q1=mysqli_query($con,$q);
	}
    header('location:restaurant_home.php');
}
    if(isset($_POST['delete'])){
        $del_name=$_POST['del_name'];
        $q="DELETE FROM menu where restaurant_id='$restaurant_log_email' and name='$del_name' ;";
        mysqli_query($con,$q);
        header('location:restaurant_home.php');
    }
    if(isset($_POST['line'])){
        $line=$_POST['line'];
        $line=($line == 'Go Online') ? 'Online':'Offline';
        $q="UPDATE restaurants SET status='$line' where email='$restaurant_log_email' ;";
        mysqli_query($con,$q);
        header('location:restaurant_home.php');
    }
    if(isset($_POST['accept'])){
        $act_id=$_POST['order_id'];
        $q="UPDATE orders SET status='accepted' where order_id='$act_id' ;";
        mysqli_query($con,$q);
        header('location:restaurant_home.php');
    }
    if(isset($_POST['decline'])){
        $act_id=$_POST['order_id'];
        $q="UPDATE orders SET status='declined' ,rider_status='declined' where order_id='$act_id' ;";
        mysqli_query($con,$q);
        header('location:restaurant_home.php');
    }
    if(isset($_POST['savepin'])){
        $lat=$_POST['lat']; $lng=$_POST['lng'];
        if(is_numeric($lat) && is_numeric($lng)){
            $u=mysqli_prepare($con,"UPDATE restaurants SET lat=?, lng=? WHERE email=?");
            mysqli_stmt_bind_param($u,"dds",$lat,$lng,$restaurant_log_email);
            mysqli_stmt_execute($u);
        }
        header('location:restaurant_home.php');
        exit;
    }

// ----- data for the view -----
$rq = mysqli_query($con,"select * from restaurants where email='$restaurant_log_email';");
$rdetails = mysqli_fetch_array($rq);
$isOnline = ($rdetails['status'] == 'Online');

$oq = mysqli_query($con,"select * from orders where order_from='$restaurant_log_email' ORDER BY order_id DESC;");
$orders = array();
while($o = mysqli_fetch_array($oq)){ $orders[] = $o; }
$active = array(); $past = array();
foreach($orders as $o){
    if($o['status']!="delivered" && $o['status']!="declined") $active[] = $o;
    else $past[] = $o;
}

// resolve an order's items string ("sno qty sno qty ...") into [name, qty] pairs
function order_items($con, $items, $res){
    $out = array();
    $list = preg_split("/ /", trim($items));
    for($i=0;$i+1<sizeof($list);$i=$i+2){
        $sno = $list[$i];
        $r = mysqli_fetch_array(mysqli_query($con,"SELECT name FROM menu where sno='$sno' and restaurant_id='$res'"));
        $out[] = array(($r['name'] ?? '') ?: 'Item #'.$sno, $list[$i+1]);
    }
    return $out;
}

// recommendation engine (linear regression over the recommend table) — preserved logic
$recommendations = array();
$res = $restaurant_log_email;
date_default_timezone_set('Asia/Kolkata');
$a=date("h"); $a=(int)$a;
$part;
if($a>7 && $a<15) $part=1;
else if($a>=15 && $a<20) $part=2;
else if($a>=19 && $a<22) $part=3;
else $part=4;
$part=$part+2;
$sql = "SELECT distinct(item_name) FROM `recommend` WHERE res_name='$res';";
$ym=0; $xm=0;
$query=mysqli_query($con,$sql);
while($k = mysqli_fetch_array($query)){
    $d=$k['item_name'];
    $count=0;
    $que = mysqli_query($con,"SELECT * FROM `recommend` WHERE res_name='$res' and item_name='$d';");
    while($g = mysqli_fetch_array($que)){ $count++; $s=(int)$g[$part]; $ym+=$s; $xm+=$count; }
    if($count<=1) continue;
    $num=0; $ym=$ym/$count; $xm=$xm/$count; $den1=0; $den2=0;
    $quw = mysqli_query($con,"SELECT * FROM `recommend` WHERE res_name='$res' and item_name='$d';");
    $cou=0;
    while($y = mysqli_fetch_array($quw)){
        $v = (int)$y[$part];
        $num+=($cou-$xm)*($v-$ym);
        $den1+=pow(($cou-$xm),2);
        $den2+=pow(($v-$ym),2);
        $cou++;
    }
    $dem = pow(($den1*$den2),0.5);
    $r=1; if($dem!=0) $r = $num/$dem;
    $sx=pow($den1/($count-1),0.5); $sy=pow($den2/($count-1),0.5);
    $b = ($sx!=0) ? $r*($sy/$sx) : 0;
    $a = $ym-$b*$xm;
    $pre = $a+$b*$count;
    $recommendations[] = array($k['item_name'], (int)$pre);
}

$mq = mysqli_query($con,"SELECT * FROM menu where restaurant_id='$restaurant_log_email';");
$menu = array();
while($m = mysqli_fetch_array($mq)){ $menu[] = $m; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Foodly — Restaurant dashboard</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="css/theme.css?v=<?php echo @filemtime('css/theme.css'); ?>">
    <link rel="stylesheet" href="css/app.css?v=<?php echo @filemtime('css/app.css'); ?>">
    <link rel="stylesheet" href="css/restaurant_home.css?v=<?php echo @filemtime('css/restaurant_home.css'); ?>">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body>

    <!-- ===== top bar ===== -->
    <header class="topbar">
        <div class="wrap">
            <a class="wordmark" href="restaurant_home.php">foodly<span class="dot">.</span><span class="role">Restaurant</span></a>
            <nav class="topbar-nav">
                <a href="support_sign.php">Support</a>
                <div class="dropdown">
                    <button class="dropbtn" onclick="myFunction()">
                        <span class="avatar"><?php echo strtoupper(substr($rdetails['name'],0,1)); ?></span>
                        <span class="who"><?php echo htmlspecialchars($rdetails['name']); ?></span>
                        <span class="caret">&#9662;</span>
                    </button>
                    <div class="dropdown-content" id="myDropdown">
                        <a target="_blank" href="mailto:<?php echo htmlspecialchars($rdetails['email']);?>">Email</a>
                        <a href="tel:<?php echo htmlspecialchars($rdetails['phone']);?>">Call</a>
                        <a href="logout.php">Log out</a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <div class="page wrap">

        <!-- ===== header + online toggle ===== -->
        <div class="page-head">
            <div>
                <span class="eyebrow">Restaurant dashboard</span>
                <h1><?php echo htmlspecialchars($rdetails['name']); ?></h1>
                <p class="sub"><?php echo htmlspecialchars($rdetails['email']); ?></p>
            </div>
            <form method="post">
                <button class="btn <?php echo $isOnline ? 'btn-soft' : 'btn-primary'; ?>" type="submit" name="line" value="<?php echo $isOnline ? 'Go Offline':'Go Online'; ?>">
                    <?php echo $isOnline ? 'Go offline' : 'Go online'; ?>
                </button>
            </form>
        </div>

        <!-- ===== stats ===== -->
        <div class="stat-row">
            <div class="stat-tile">
                <div class="k">Status</div>
                <div class="v"><span class="statebadge <?php echo $isOnline ? 'on':''; ?>"><i class="ping"></i><?php echo $isOnline ? 'Online' : 'Offline'; ?></span></div>
            </div>
            <div class="stat-tile">
                <div class="k">Pending payout</div>
                <div class="v">&#8377;<?php echo htmlspecialchars($rdetails['wallet']); ?></div>
            </div>
            <div class="stat-tile">
                <div class="k">Active orders</div>
                <div class="v"><?php echo count($active); ?></div>
            </div>
            <div class="stat-tile">
                <div class="k">Menu items</div>
                <div class="v"><?php echo count($menu); ?></div>
            </div>
        </div>

        <!-- ===== location pin ===== -->
        <section class="panel">
            <div class="panel-title">Your location</div>
            <p class="panel-sub">Drop a pin where your kitchen is. Customers within <?php echo (int)(defined('DELIVERY_RADIUS_KM')?DELIVERY_RADIUS_KM:20); ?> km can order from you.<?php if($rdetails['lat']===null){ echo ' <b style="color:var(--accent)">No location set yet — you are hidden from customers until you set one.</b>'; } ?></p>
            <div id="rest-map" style="height:320px;border-radius:var(--r-md);overflow:hidden;border:1px solid var(--border);z-index:0;"></div>
            <form method="post" style="margin-top:1rem;display:flex;gap:0.7rem;flex-wrap:wrap;align-items:center;">
                <input type="hidden" name="lat" id="pin_lat" value="<?php echo htmlspecialchars($rdetails['lat'] ?? ''); ?>">
                <input type="hidden" name="lng" id="pin_lng" value="<?php echo htmlspecialchars($rdetails['lng'] ?? ''); ?>">
                <button type="button" class="btn-soft" onclick="useMyLocation()">Use my current location</button>
                <button type="submit" name="savepin" value="1" class="btn btn-primary" id="save_pin" <?php echo $rdetails['lat']===null?'disabled':''; ?>>Save location</button>
                <span id="pin_note" class="panel-sub" style="margin:0;"></span>
            </form>
        </section>

        <?php
        $mapOrders = array();
        foreach($active as $o){ if($o['d_lat'] !== null){ $mapOrders[] = $o; } }
        if(count($mapOrders) > 0 && $rdetails['lat'] !== null){ ?>
        <!-- ===== where orders are going ===== -->
        <section class="panel">
            <div class="panel-title">Live delivery map</div>
            <p class="panel-sub"><?php echo count($mapOrders); ?> active <?php echo count($mapOrders)==1?'order':'orders'; ?> &middot; you are the marked kitchen.</p>
            <div id="orders-map" style="height:340px;border-radius:var(--r-md);overflow:hidden;border:1px solid var(--border);z-index:0;"></div>
        </section>
        <script>
            (function(){
                var here = [<?php echo $rdetails['lat']; ?>, <?php echo $rdetails['lng']; ?>];
                var dests = <?php echo json_encode(array_map(function($o){ return array('lat'=>(float)$o['d_lat'],'lng'=>(float)$o['d_lng'],'id'=>$o['order_id'],'status'=>$o['status']); }, $mapOrders)); ?>;
                var map = L.map('orders-map', { zoomControl: true, attributionControl: false });
                L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { subdomains:'abcd', maxZoom:20 }).addTo(map);
                function mk(kind, glyph){ return L.divIcon({ className:'mk mk-'+kind, html:'<div class="mk-in" style="font-size:14px">'+(glyph||'')+'</div>', iconSize:[30,30], iconAnchor:[15,15] }); }
                L.marker(here, { icon: mk('rest','&#127869;'), zIndexOffset:1000 }).addTo(map).bindPopup('Your kitchen');
                var pts = [here];
                dests.forEach(function(d){
                    L.marker([d.lat,d.lng], { icon: mk('home','&#127968;') }).addTo(map).bindPopup('Order #'+d.id+' &middot; '+d.status);
                    L.polyline([here,[d.lat,d.lng]], { className:'route-line', weight:2, opacity:0.4, dashArray:'4 7' }).addTo(map);
                    pts.push([d.lat,d.lng]);
                });
                map.fitBounds(L.latLngBounds(pts).pad(0.3));
                setTimeout(function(){ map.invalidateSize(); }, 200);
            })();
        </script>
        <?php } ?>

        <!-- ===== active orders ===== -->
        <section class="panel">
            <div class="panel-title">Active orders</div>
            <p class="panel-sub">Accept incoming orders to send them to a rider.</p>
            <?php if(count($active)===0){ ?>
                <div class="empty"><div class="empty-ic">&#128221;</div><h3>No active orders</h3><p>New orders will appear here the moment a customer checks out.</p></div>
            <?php } else { ?>
            <div class="order-grid">
                <?php foreach($active as $row){ ?>
                <article class="order-card">
                    <div class="order-top">
                        <span class="oid">#<?php echo htmlspecialchars($row['order_id']); ?></span>
                        <span class="statebadge"><i class="ping"></i><?php echo htmlspecialchars($row['status']); ?></span>
                    </div>
                    <div class="order-cust"><?php echo htmlspecialchars($row['order_by']); ?></div>
                    <ul class="order-items">
                        <?php foreach(order_items($con,$row['items'],$restaurant_log_email) as $it){ ?>
                            <li><span><?php echo htmlspecialchars($it[0]); ?></span><span class="q">&times; <?php echo htmlspecialchars($it[1]); ?></span></li>
                        <?php } ?>
                    </ul>
                    <div class="order-meta">
                        <div><span>Collect</span><b>&#8377;<?php echo htmlspecialchars($row['total']); ?></b></div>
                        <div><span>Rider</span><b><?php echo htmlspecialchars($row['rider']); ?></b></div>
                        <div><span>Placed</span><b><?php echo htmlspecialchars($row['instance']); ?></b></div>
                    </div>
                    <form method="post" class="order-actions">
                        <input type="text" name="order_id" value="<?php echo htmlspecialchars($row['order_id']); ?>" hidden>
                        <?php if ($row['status']=="placed") { ?>
                            <button class="btn-soft" type="submit" name="accept" value="accept">Accept</button>
                        <?php } ?>
                        <?php if ($row['status']!="declined") { ?>
                            <button class="btn-danger" type="submit" name="decline" value="decline">Decline</button>
                        <?php } ?>
                    </form>
                </article>
                <?php } ?>
            </div>
            <?php } ?>
        </section>

        <!-- ===== menu management ===== -->
        <section class="panel">
            <div class="panel-title">Your menu</div>
            <p class="panel-sub">Add new dishes, then manage what is live below.</p>

            <form method="post" class="menu-form">
                <div id="item_fileds">
                    <div class="item-row">
                        <div class="item-row-head">Item 1</div>
                        <div class="item-grid">
                            <input class="input" type="text" name="item_name[]" placeholder="Item name">
                            <input class="input" type="text" name="item_price[]" placeholder="Price">
                            <input class="input" type="text" name="item_discount[]" placeholder="Discount %" maxlength="3" required>
                            <input class="input" type="text" name="item_desc[]" placeholder="Description">
                        </div>
                    </div>
                </div>
                <div class="menu-form-actions">
                    <button type="button" class="btn-soft" id="more_fields" onclick="add_fields();">+ Add another item</button>
                    <button type="submit" class="btn btn-primary" name="update" value="Update">Save items</button>
                </div>
            </form>

            <?php if(count($menu)>0){ ?>
            <div class="table-scroll">
                <table class="data-table">
                    <tr><th>Name</th><th>Price</th><th>Discount</th><th>Description</th><th></th></tr>
                    <?php foreach($menu as $row){ ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td class="num">&#8377;<?php echo htmlspecialchars($row['price']); ?></td>
                        <td class="num"><?php echo htmlspecialchars($row['discount']); ?>%</td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td>
                            <form method="post">
                                <input type="text" name="del_name" value="<?php echo htmlspecialchars($row['name']); ?>" hidden>
                                <button class="btn-danger" type="submit" name="delete" value="delete">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
            <?php } else { ?>
                <div class="empty"><div class="empty-ic">&#127869;</div><h3>No items yet</h3><p>Add your first dish above and it will show up on your menu.</p></div>
            <?php } ?>
        </section>

        <!-- ===== recommendations + past orders ===== -->
        <div class="two-col">
            <section class="panel">
                <div class="panel-title">Recommended to make</div>
                <p class="panel-sub">Predicted demand for this time of day.</p>
                <?php if(count($recommendations)>0){ ?>
                    <ul class="reco-list">
                        <?php foreach($recommendations as $rc){ ?>
                            <li><span><?php echo htmlspecialchars($rc[0]); ?></span><b class="num"><?php echo htmlspecialchars($rc[1]); ?></b></li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    <div class="empty"><div class="empty-ic">&#128200;</div><h3>Not enough data yet</h3><p>Predictions appear once you have order history.</p></div>
                <?php } ?>
            </section>

            <section class="panel">
                <div class="panel-title">Past orders</div>
                <p class="panel-sub"><?php echo count($past); ?> completed or declined</p>
                <?php if(count($past)>0){ ?>
                    <div class="table-scroll">
                        <table class="data-table">
                            <tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th></tr>
                            <?php foreach($past as $row){ ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($row['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['order_by']); ?></td>
                                <td class="num">&#8377;<?php echo htmlspecialchars($row['total']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                <?php } else { ?>
                    <div class="empty"><div class="empty-ic">&#9203;</div><h3>Nothing yet</h3><p>Completed orders will be archived here.</p></div>
                <?php } ?>
            </section>
        </div>

    </div>
    <script>
        (function(){
            var hasPin = <?php echo $rdetails['lat']===null ? 'false' : 'true'; ?>;
            var start = hasPin ? [<?php echo $rdetails['lat'] ?: 0; ?>, <?php echo $rdetails['lng'] ?: 0; ?>] : [28.6315, 77.2167];
            var map = L.map('rest-map').setView(start, hasPin ? 15 : 11);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { subdomains: 'abcd', maxZoom: 20 }).addTo(map);
            var marker = hasPin ? L.marker(start).addTo(map) : null;
            function place(lat, lng){
                if(marker) marker.setLatLng([lat,lng]); else marker = L.marker([lat,lng]).addTo(map);
                document.getElementById('pin_lat').value = lat.toFixed(6);
                document.getElementById('pin_lng').value = lng.toFixed(6);
                document.getElementById('save_pin').disabled = false;
                document.getElementById('pin_note').textContent = 'Pin moved — click Save location.';
            }
            map.on('click', function(e){ place(e.latlng.lat, e.latlng.lng); });
            window.useMyLocation = function(){
                if(!navigator.geolocation){ document.getElementById('pin_note').textContent='Geolocation not supported.'; return; }
                navigator.geolocation.getCurrentPosition(function(p){ place(p.coords.latitude, p.coords.longitude); map.setView([p.coords.latitude,p.coords.longitude],15); },
                    function(){ document.getElementById('pin_note').textContent='Could not get location — click the map.'; });
            };
            setTimeout(function(){ map.invalidateSize(); }, 200);
        })();
        // advance the demo simulation; reload when an order changes state
        setInterval(function(){ $.get('tick.php', function(r){ if(r === 'changed') location.reload(); }); }, 5000);
    </script>
    <script src="js/restaurant_home.js"></script>
</body>
</html>
