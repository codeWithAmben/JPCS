<?php
/**
 * Leaflet + OpenStreetMap helper include
 * Usage: include 'includes/leaflet_map.php'; echo renderLeafletMap($markers, $options);
 */

function renderLeafletMap($markers = [], $options = []) {
    $id = $options['id'] ?? 'leafletMap';
    $height = $options['height'] ?? '360px';
    $zoom = isset($options['zoom']) ? (int)$options['zoom'] : 15;

    $centerLat = $options['center']['lat'] ?? ($markers[0]['lat'] ?? 13.8290);
    $centerLng = $options['center']['lng'] ?? ($markers[0]['lng'] ?? 121.1210);

    $markersJson = json_encode($markers, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

    $html = <<<HTML
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<div id="{$id}" class="leaflet-map-container" style="width:100%; height:{$height}; border-radius:10px; overflow:hidden;"></div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
(function(){
  // If Leaflet failed to load (e.g., CDN blocked), show a clear fallback message
  function showLeafletFallback(){
    var container = document.getElementById('{$id}');
    if (container) {
      container.innerHTML = '<div class="map-error" style="padding:20px; background:#fff; color:#333; border-radius:8px; text-align:center; box-shadow:0 6px 18px rgba(0,0,0,0.08);">Map failed to load. Please check your internet connection or try again later.</div>';
      console.warn('Leaflet did not load. Falling back to static message.');
    }
  }

  if (typeof L === 'undefined') {
    // Wait a short time in case script is still loading
    setTimeout(function(){
      if (typeof L === 'undefined') {
        showLeafletFallback();
      }
    }, 700);
    return;
  }

  try {
    var map = L.map('{$id}').setView([{$centerLat}, {$centerLng}], {$zoom});
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
    var markers = {$markersJson};
    markers.forEach(function(m){
      if (!m || typeof m.lat === 'undefined' || typeof m.lng === 'undefined') return;
      var marker = L.marker([m.lat, m.lng]).addTo(map);
      var content = '';
      if (m.title) content += '<strong>' + m.title + '</strong><br/>';
      if (m.description) content += '<div>' + m.description + '</div>';
      if (m.link) content += '<div style="margin-top:8px;"><a href="' + m.link + '">Open</a></div>';
      marker.bindPopup(content);

      if (m.navigateOnClick) {
        // On click: fly to maximum zoom level with a smooth, slower animation
        marker.on('click', function(e){
          var maxZ = (map.getMaxZoom && map.getMaxZoom()) || 18;
          var desiredZoom = maxZ;

          // Use flyTo for a smooth, slow animated zoom; adjust duration for slower effect
          var flyOptions = {animate: true, duration: 2.2, easeLinearity: 0.25};

          // If already at or above desired zoom, just open popup
          if (map.getZoom() >= desiredZoom) {
            marker.openPopup();
            return;
          }

          // Fly to the marker and open popup after animation completes
          map.flyTo(marker.getLatLng(), desiredZoom, flyOptions);
          var navTimeout = setTimeout(function(){ marker.openPopup(); }, 2600);
          map.once('moveend', function(){ clearTimeout(navTimeout); marker.openPopup(); });
        });
      }
    });
  } catch (err) {
    console.error('Error initializing Leaflet map:', err);
    showLeafletFallback();
  }
})();
</script>
<style>.leaflet-map-container{width:100%; height:{$height}; border-radius:10px;}
.map-error{font-size:0.95rem;}
.map-instructions{max-width:900px;margin:0 auto 12px;background:rgba(255,255,255,0.95);padding:10px 14px;border-radius:8px;box-shadow:0 6px 18px rgba(0,0,0,0.06);font-size:0.95rem;color:#333;text-align:center}
</style>
HTML;

    return $html;
}

?>