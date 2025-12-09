<?php
/**
 * Tawk.to Live Chat Widget
 * Centralized chat widget for all pages
 * 
 * IMPORTANT: Replace the property ID and widget ID with your actual Tawk.to credentials
 * Get them from: https://dashboard.tawk.to/ -> Administration -> Channels -> Chat Widget
 */

// Only load chat widget if not in admin panel or if specifically enabled
$show_chat = true;

// Disable chat in admin/member dashboards by default (optional)
if (isset($_SERVER['REQUEST_URI'])) {
    $uri = $_SERVER['REQUEST_URI'];
    // Uncomment to disable chat in admin/member areas
    // if (strpos($uri, '/admin/') !== false || strpos($uri, '/member/') !== false) {
    //     $show_chat = false;
    // }
}

if ($show_chat):
?>
<!-- Start of Tawk.to Script -->
<!--
    TO FIX: Replace the URL below with YOUR actual Tawk.to widget code!
    
    Steps to get your widget code:
    1. Go to https://www.tawk.to/ and sign in (or create account)
    2. Go to Administration -> Channels -> Chat Widget
    3. Copy the script src URL (looks like: https://embed.tawk.to/YOUR_PROPERTY_ID/YOUR_WIDGET_ID)
    4. Replace the URL in the script below
    
    Current URL uses placeholder IDs that may not exist!
-->
<script type="text/javascript">
var Tawk_API = Tawk_API || {};
var Tawk_LoadStart = new Date();

// Optional: Customize widget position
Tawk_API.onLoad = function() {
    // Widget loaded successfully
    console.log('Tawk.to chat widget loaded');
};

Tawk_API.onStatusChange = function(status) {
    // Status: 'online', 'away', 'offline'
    console.log('Tawk.to status:', status);
};

(function(){
    var s1 = document.createElement("script");
    var s0 = document.getElementsByTagName("script")[0];
    s1.async = true;
    // REPLACE THIS URL with your actual Tawk.to widget URL
    s1.src = 'https://embed.tawk.to/69246f4d7a43e3195d75d1fb/1jbevccl4';
    s1.charset = 'UTF-8';
    s1.setAttribute('crossorigin', 'anonymous');
    s0.parentNode.insertBefore(s1, s0);
})();
</script>
<!-- End of Tawk.to Script -->
<?php endif; ?>
