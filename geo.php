<?php
/* Foodly geo helpers — distance, delivery radius, and a per-browser
   "recentre" offset so the demo always shows restaurants near the user.

   Restaurant coordinates in the DB are canonical (seeded around Delhi).
   For a given browser session we translate every restaurant by a stored
   offset so the cluster surrounds wherever the user is. The offset lives
   in the session (PHPSESSID cookie) and is wiped when cookies are cleared. */

if(!defined('DELIVERY_RADIUS_KM')) define('DELIVERY_RADIUS_KM', 20);
if(!defined('TRAVEL_SECONDS')) define('TRAVEL_SECONDS', 120); // rider restaurant->home travel time

/* Great-circle distance in km (Haversine). */
function haversine_km($lat1, $lng1, $lat2, $lng2){
    $r = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);
    $a = sin($dLat/2)**2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2)**2;
    return $r * 2 * asin(min(1, sqrt($a)));
}

function has_user_location(){
    return isset($_SESSION['user_lat']) && isset($_SESSION['user_lng']);
}

/* Average position of all pinned restaurants (the canonical cluster centre). */
function restaurant_centroid($con){
    $r = mysqli_fetch_assoc(mysqli_query($con, "SELECT AVG(lat) la, AVG(lng) lo FROM restaurants WHERE lat IS NOT NULL"));
    if($r && $r['la'] !== null) return array((float)$r['la'], (float)$r['lo']);
    return array(28.6315, 77.2167); // fallback: Connaught Place, Delhi
}

/* Set the user's delivery location and recentre the restaurant cluster on it. */
function set_user_location($con, $lat, $lng, $place = ''){
    $c = restaurant_centroid($con);
    $_SESSION['user_lat']   = (float)$lat;
    $_SESSION['user_lng']   = (float)$lng;
    $_SESSION['user_place'] = $place;
    $_SESSION['geo_off_lat'] = (float)$lat - $c[0];
    $_SESSION['geo_off_lng'] = (float)$lng - $c[1];
    unset($_SESSION['user_auto']);
}

/* Ensure there's always a location: default to the restaurant cluster
   centre (offset 0) so a fresh visitor immediately sees kitchens. */
function ensure_user_location($con){
    if(!has_user_location()){
        $c = restaurant_centroid($con);
        $_SESSION['user_lat']    = $c[0];
        $_SESSION['user_lng']    = $c[1];
        $_SESSION['geo_off_lat'] = 0.0;
        $_SESSION['geo_off_lng'] = 0.0;
        $_SESSION['user_auto']   = true;
    }
}

/* A restaurant's effective position for this browser (canonical + offset). */
function eff_coords($lat, $lng){
    return array(
        (float)$lat + ($_SESSION['geo_off_lat'] ?? 0),
        (float)$lng + ($_SESSION['geo_off_lng'] ?? 0)
    );
}

/* Distance (km) from the user to a restaurant, honouring the offset. */
function user_distance_km($rlat, $rlng){
    if(!has_user_location() || $rlat === null || $rlat === '') return null;
    $e = eff_coords($rlat, $rlng);
    return haversine_km($_SESSION['user_lat'], $_SESSION['user_lng'], $e[0], $e[1]);
}
