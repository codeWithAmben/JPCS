<?php
/**
 * Google Maps helper include
 * Usage: include 'includes/google_map.php'; echo renderGoogleMap($markers, $options);
 */

function renderGoogleMap($markers = [], $options = []) {
    // Ensure the API key is configured
    $apiKey = defined('GOOGLE_MAPS_API_KEY') ? GOOGLE_MAPS_API_KEY : '';
    if (empty($apiKey)) {
        // Return fallback HTML so we can show image map as fallback
        return '<div class="map-fallback">Google Maps API key not configured. Please set GOOGLE_MAPS_API_KEY in .env or config.php.</div>';
    }

    // Default center option
    $centerLat = $options['center']['lat'] ?? ($markers[0]['lat'] ?? 13.8290);
    $centerLng = $options['center']['lng'] ?? ($markers[0]['lng'] ?? 121.1210);
    $zoom = isset($options['zoom']) ? (int)$options['zoom'] : 15;

    // Markers JSON
    $markersJson = json_encode($markers, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

    $id = $options['id'] ?? 'googleMap';
    $height = $options['height'] ?? '420px';

    $html = '';
    $html .= "<div id=\"{$id}\" class=\"google-map-container\" style=\"width:100%; height:{$height}; border-radius:10px; overflow:hidden;\"></div>";
    $html .= "<script async defer src=\"https://maps.googleapis.com/maps/api/js?key={$apiKey}&callback=initGoogleMap_{$id}\"></script>";
    $html .= "<script>function initGoogleMap_{$id}(){\n";
    $html .= "  var markers = {$markersJson};\n";
    $html .= "  var center = {lat: {$centerLat}, lng: {$centerLng}};\n";
    $html .= "  var map = new google.maps.Map(document.getElementById('{$id}'), {zoom: {$zoom}, center: center});\n";
    $html .= "  var infoWindow = new google.maps.InfoWindow();\n";
    $html .= "  markers.forEach(function(m){ var marker = new google.maps.Marker({position:{lat:m.lat, lng:m.lng}, map:map, title: m.title || ''});\n";
    $html .= "    var content = '<div style=\"max-width:240px; font-size:14px;\">' + (m.title? '<strong>' + m.title + '</strong><br/>':'') + (m.description? m.description + '<br/>':'') + (m.link? '<a href=\"' + m.link + '\">Open</a>' : '') + '</div>';\n";
    $html .= "    marker.addListener('click', function(){ infoWindow.setContent(content); infoWindow.open(map, marker); if (m.navigateOnClick){ window.location.href = m.link; } });\n";
    $html .= "  });\n";
    $html .= "}\n</script>";

    return $html;
}

?>
