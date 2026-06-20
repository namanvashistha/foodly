<?php
	session_start();
	if(!isset($_SESSION['log_email'])){
		header("location:index.php");
		exit;
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Foodly — Tracking your order</title>
	<link rel="shortcut icon" href="images/logo.png" type="image/png">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="stylesheet" href="css/theme.css?v=<?php echo @filemtime('css/theme.css'); ?>">
	<link rel="stylesheet" href="css/app.css?v=<?php echo @filemtime('css/app.css'); ?>">
	<link rel="stylesheet" href="css/order_status.css?v=<?php echo @filemtime('css/order_status.css'); ?>">
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
	<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body>

	<header class="topbar">
		<div class="wrap">
			<a class="wordmark" href="home.php">foodly<span class="dot">.</span></a>
			<nav class="topbar-nav">
				<a href="home.php">Home</a>
				<a href="logout.php">Log out</a>
			</nav>
		</div>
	</header>

	<div class="track-page wrap">
		<div class="track-head">
			<span class="eyebrow">Live tracking</span>
			<h1 id="t-title">Your order</h1>
			<div id="t-switch" class="track-switch"></div>
		</div>

		<div class="track-grid" id="track-grid">
			<div class="live-map-wrap">
				<div id="live-map"></div>
				<div id="eta-pill" class="eta-pill">Loading…</div>
			</div>
			<aside class="track-panel">
				<ol class="tracker" id="t-stepper"></ol>
				<div class="track-rider" id="t-rider"></div>
				<ul class="track-items" id="t-items"></ul>
				<div class="track-meta" id="t-meta"></div>
				<div class="track-otp" id="t-otp"></div>
			</aside>
		</div>

		<div class="empty" id="t-empty" style="display:none;">
			<div class="empty-ic">&#128717;</div>
			<h2>No active orders</h2>
			<p>When you place an order you'll see it here with a live map.</p>
			<a class="btn btn-primary" href="home.php" style="margin-top:1rem;">Browse restaurants</a>
		</div>
	</div>

	<script>
		var STEPS = ['Placed','Accepted','On the way','Delivered'];
		function stepIndex(s){ return { 'placed':0,'accepted':1,'On the way':2,'delivered':3 }[s] || 0; }
		function fmt(sec){ sec = Math.max(0, Math.round(sec)); var m = Math.floor(sec/60), s = sec%60; return m+':'+(s<10?'0':'')+s; }
		function lerp(a,b,t){ return a + (b-a)*t; }

		var map, restM, homeM, riderM, routeL, fitted = false;
		var orders = [], selId = null, sel = null, pollAt = 0;

		var GLYPH = {
			rest: '<svg viewBox="0 0 24 24" width="15" height="15"><path fill="currentColor" d="M5 3h14l1 5.2a2.6 2.6 0 0 1-5.1.3 2.6 2.6 0 0 1-4.9 0 2.6 2.6 0 0 1-4.9 0A2.6 2.6 0 0 1 4 8.2zM5.5 12h13V21h-13z"/></svg>',
			home: '<svg viewBox="0 0 24 24" width="15" height="15"><path fill="currentColor" d="M12 3 21 11h-2.6v9.5h-4.6v-5.7H10.2v5.7H5.6V11H3z"/></svg>',
			rider: '<svg viewBox="0 0 24 24" width="15" height="15"><path fill="currentColor" d="M5 16a3 3 0 1 0 0 .01zM19 16a3 3 0 1 0 0 .01zM14.5 6h2.2l1.2 3 1.1.4-.7 1.9-1.5-.6-1.1-2.7H13l1.8 3.9H10l-.7-1.6h3l-1.4-3zM6 13l2-3h3l-1 1.6H8.7L7.6 13z"/></svg>'
		};
		function makeIcon(kind){
			return L.divIcon({ className: 'mk mk-'+kind, html: '<div class="mk-in">'+GLYPH[kind]+'</div>', iconSize: [30,30], iconAnchor: [15,15] });
		}

		function ensureMap(){
			if(map) return;
			map = L.map('live-map', { zoomControl: true, attributionControl: false }).setView([28.63,77.22], 13);
			L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { subdomains: 'abcd', maxZoom: 20 }).addTo(map);
		}

		function selectOrder(id){ selId = id; fitted = false; render(); }

		function render(){
			sel = orders.find(function(o){ return o.id === selId; }) || orders[0];
			if(!sel){ return; }
			selId = sel.id;
			document.getElementById('t-title').textContent = 'Order #' + sel.id + ' · ' + sel.restaurant;

			// switch pills
			var sw = document.getElementById('t-switch');
			sw.innerHTML = orders.length > 1 ? orders.map(function(o){
				return '<button class="track-pill '+(o.id===selId?'active':'')+'" onclick="selectOrder('+o.id+')">#'+o.id+'</button>';
			}).join('') : '';

			// stepper
			var cur = stepIndex(sel.status);
			document.getElementById('t-stepper').innerHTML = STEPS.map(function(label,i){
				return '<li class="'+(i<=cur?'done':'')+' '+(i===cur?'current':'')+'"><span class="dot"></span><span class="lbl">'+label+'</span></li>';
			}).join('');

			// rider line
			var rs = document.getElementById('t-rider');
			if(sel.status === 'On the way'){ rs.innerHTML = '<span class="ic">&#128692;</span> <b>'+sel.rider+'</b> is on the way to you'; }
			else if(sel.status === 'accepted'){ rs.innerHTML = '<span class="ic">&#127859;</span> '+sel.restaurant+' is preparing your food'; }
			else { rs.innerHTML = '<span class="ic">&#128221;</span> Waiting for the kitchen to accept'; }

			// items / meta / otp
			document.getElementById('t-items').innerHTML = sel.items.map(function(it){
				return '<li><span>'+it.name+'</span><span class="q">&times; '+it.qty+'</span></li>'; }).join('');
			document.getElementById('t-meta').innerHTML =
				'<div><span>Total</span><b>&#8377;'+sel.total+'</b></div>'+
				'<div><span>Deliver to</span><b>'+sel.address+'</b></div>';
			document.getElementById('t-otp').innerHTML = (sel.status === 'On the way')
				? 'Share this OTP with your rider <b>'+sel.otp+'</b>'
				: 'Your delivery OTP appears once the rider is on the way.';
			document.getElementById('t-otp').className = 'track-otp' + (sel.status==='On the way'?'':' muted');

			// map markers
			ensureMap();
			var r = sel.r, d = sel.d;
			if(r[0] !== null && d[0] !== null){
				if(!routeL) routeL = L.polyline([r,d], { className: 'route-line', weight: 3, opacity: 0.7, dashArray: '4 7' }).addTo(map);
				else routeL.setLatLngs([r,d]);
				if(!restM) restM = L.marker(r, { icon: makeIcon('rest') }).addTo(map).bindPopup(sel.restaurant);
				else restM.setLatLng(r);
				if(!homeM) homeM = L.marker(d, { icon: makeIcon('home') }).addTo(map).bindPopup('Your location');
				else homeM.setLatLng(d);
				if(!riderM) riderM = L.marker(r, { icon: makeIcon('rider'), zIndexOffset: 1000 }).addTo(map);
				if(!fitted){ map.fitBounds(L.latLngBounds([r,d]).pad(0.4)); fitted = true; }
			}
		}

		// smooth real-time rider animation
		function animate(){
			if(sel && sel.r[0] !== null && sel.d[0] !== null && riderM){
				var r = sel.r, d = sel.d, etaText = '';
				if(sel.status === 'On the way' && sel.otw_elapsed !== null){
					var elapsed = sel.otw_elapsed + (performance.now() - pollAt)/1000;
					var p = Math.max(0, Math.min(1, elapsed / sel.travel));
					riderM.setLatLng([ lerp(r[0],d[0],p), lerp(r[1],d[1],p) ]);
					etaText = p >= 1 ? 'Arriving now' : 'Arriving in ' + fmt(sel.travel - elapsed);
				} else {
					riderM.setLatLng(r);
					etaText = (sel.status === 'accepted') ? 'Preparing · rider at the kitchen' : 'Order placed';
				}
				document.getElementById('eta-pill').textContent = etaText;
			}
			requestAnimationFrame(animate);
		}

		function poll(){
			$.getJSON('order_track.php', function(data){
				orders = data || [];
				pollAt = performance.now();
				if(orders.length === 0){
					document.getElementById('track-grid').style.display = 'none';
					document.getElementById('t-switch').style.display = 'none';
					document.getElementById('t-title').textContent = 'Your order';
					document.getElementById('t-empty').style.display = 'block';
					document.getElementById('eta-pill').textContent = '';
					sel = null; return;
				}
				document.getElementById('track-grid').style.display = '';
				document.getElementById('t-empty').style.display = 'none';
				if(!orders.find(function(o){ return o.id === selId; })) selId = orders[0].id;
				render();
				setTimeout(function(){ if(map) map.invalidateSize(); }, 100);
			});
		}

		$(document).ready(function(){
			poll();
			requestAnimationFrame(animate);
			setInterval(function(){ $.get('tick.php').always(poll); }, 4000);
		});
	</script>
</body>
</html>
