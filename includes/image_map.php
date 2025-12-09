<?php
/**
 * Interactive Image Map Component
 * Creates clickable image maps for navigation and user interactivity
 * 
 * Usage:
 * <?php include 'includes/image_map.php'; ?>
 * <?php echo renderImageMap('logo-nav', 'path/to/image.png', $areas); ?>
 */

/**
 * Render an interactive image map
 * 
 * @param string $mapName Unique name for the map
 * @param string $imageSrc Path to the image
 * @param array $areas Array of clickable areas with coordinates and links
 * @param array $options Additional options (alt, class, width, height)
 * @return string HTML for the image map
 */
function renderImageMap($mapName, $imageSrc, $areas, $options = []) {
    $alt = $options['alt'] ?? 'Interactive Image Map';
    $class = $options['class'] ?? 'image-map';
    $width = isset($options['width']) ? 'width="' . $options['width'] . '"' : '';
    $height = isset($options['height']) ? 'height="' . $options['height'] . '"' : '';
    $responsive = $options['responsive'] ?? true;
    
    $html = '<div class="image-map-container">';
    $html .= '<img src="' . htmlspecialchars($imageSrc) . '" ';
    $html .= 'alt="' . htmlspecialchars($alt) . '" ';
    $html .= 'class="' . htmlspecialchars($class) . '" ';
    $html .= 'usemap="#' . htmlspecialchars($mapName) . '" ';
    $html .= $width . ' ' . $height;
    if ($responsive) {
        $html .= ' data-responsive="true"';
    }
    $html .= '>';
    
    $html .= '<map name="' . htmlspecialchars($mapName) . '">';
    
    foreach ($areas as $area) {
        $html .= '<area ';
        $html .= 'shape="' . htmlspecialchars($area['shape'] ?? 'rect') . '" ';
        $html .= 'coords="' . htmlspecialchars($area['coords']) . '" ';
        $html .= 'href="' . htmlspecialchars($area['href']) . '" ';
        $html .= 'alt="' . htmlspecialchars($area['alt'] ?? '') . '" ';
        $html .= 'title="' . htmlspecialchars($area['title'] ?? $area['alt'] ?? '') . '" ';
        
        if (isset($area['target'])) {
            $html .= 'target="' . htmlspecialchars($area['target']) . '" ';
        }
        
        // Add data attributes for tooltips
        if (isset($area['tooltip'])) {
            $html .= 'data-tooltip="' . htmlspecialchars($area['tooltip']) . '" ';
        }
        
        $html .= '>';
    }
    
    $html .= '</map>';
    $html .= '</div>';
    
    return $html;
}

/**
 * Get predefined quick links image map areas
 * For use with the JPCS logo or navigation image
 */
function getQuickLinksMapAreas($baseUrl = '') {
    return [
        [
            'shape' => 'rect',
            'coords' => '0,0,100,50',
            'href' => $baseUrl . 'index.php',
            'alt' => 'Home',
            'title' => 'Go to Homepage',
            'tooltip' => '🏠 Home'
        ],
        [
            'shape' => 'rect',
            'coords' => '100,0,200,50',
            'href' => $baseUrl . 'pages/about.php',
            'alt' => 'About',
            'title' => 'About JPCS Malvar',
            'tooltip' => 'ℹ️ About Us'
        ],
        [
            'shape' => 'rect',
            'coords' => '200,0,300,50',
            'href' => $baseUrl . 'pages/events.php',
            'alt' => 'Events',
            'title' => 'View Events',
            'tooltip' => '📅 Events'
        ],
        [
            'shape' => 'rect',
            'coords' => '300,0,400,50',
            'href' => $baseUrl . 'pages/membership.php',
            'alt' => 'Membership',
            'title' => 'Join JPCS',
            'tooltip' => '👥 Membership'
        ],
        [
            'shape' => 'rect',
            'coords' => '400,0,500,50',
            'href' => $baseUrl . 'login.php',
            'alt' => 'Login',
            'title' => 'Member Login',
            'tooltip' => '🔐 Login'
        ]
    ];
}

/**
 * Render interactive campus/organization map
 */
function renderOrganizationMap($baseUrl = '') {
    $areas = [
        [
            'shape' => 'circle',
            'coords' => '150,150,50',
            'href' => $baseUrl . 'pages/about.php#president',
            'alt' => 'President',
            'title' => 'Meet the President',
            'tooltip' => '👤 President'
        ],
        [
            'shape' => 'circle',
            'coords' => '80,250,40',
            'href' => $baseUrl . 'pages/about.php#vp-internal',
            'alt' => 'VP Internal',
            'title' => 'VP for Internal Affairs',
            'tooltip' => '👤 VP Internal'
        ],
        [
            'shape' => 'circle',
            'coords' => '220,250,40',
            'href' => $baseUrl . 'pages/about.php#vp-external',
            'alt' => 'VP External',
            'title' => 'VP for External Affairs',
            'tooltip' => '👤 VP External'
        ],
        [
            'shape' => 'rect',
            'coords' => '50,320,250,380',
            'href' => $baseUrl . 'pages/about.php#officers',
            'alt' => 'Officers',
            'title' => 'View All Officers',
            'tooltip' => '👥 All Officers'
        ]
    ];
    
    return renderImageMap('org-map', $baseUrl . 'assets/images/LOGO.png', $areas, [
        'alt' => 'JPCS Organization Structure',
        'class' => 'org-image-map',
        'responsive' => true
    ]);
}
?>

<!-- Image Map Styles and Scripts -->
<style>
.image-map-container {
    position: relative;
    display: inline-block;
    max-width: 100%;
}

.image-map-container img {
    max-width: 100%;
    height: auto;
}

.image-map {
    cursor: pointer;
    transition: transform 0.3s ease;
}

.image-map:hover {
    transform: scale(1.02);
}

/* Tooltip for image map areas */
.map-tooltip {
    position: absolute;
    background: rgba(26, 35, 126, 0.95);
    color: white;
    padding: 8px 15px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.3s ease, transform 0.3s ease;
    transform: translateY(-10px);
    z-index: 1000;
    white-space: nowrap;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.map-tooltip.visible {
    opacity: 1;
    transform: translateY(0);
}

.map-tooltip::after {
    content: '';
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -6px;
    border-width: 6px;
    border-style: solid;
    border-color: rgba(26, 35, 126, 0.95) transparent transparent transparent;
}

/* Quick Links Image Map Section */
.quick-links-map {
    text-align: center;
    padding: 30px 20px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 15px;
    margin: 20px 0;
}

.quick-links-map h3 {
    color: #1a237e;
    margin-bottom: 20px;
    font-size: 1.5rem;
}

/* Interactive Navigation Map */
.nav-image-map {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 15px;
    padding: 20px;
}

.nav-map-item {
    position: relative;
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, #1a237e, #3949ab);
    border-radius: 15px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
}

.nav-map-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.nav-map-item:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 10px 30px rgba(26, 35, 126, 0.4);
}

.nav-map-item:hover::before {
    opacity: 1;
}

.nav-map-item .icon {
    font-size: 36px;
    margin-bottom: 8px;
}

.nav-map-item .label {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Responsive Image Map Helper */
@media (max-width: 768px) {
    .nav-map-item {
        width: 100px;
        height: 100px;
    }
    
    .nav-map-item .icon {
        font-size: 28px;
    }
    
    .nav-map-item .label {
        font-size: 10px;
    }
}
</style>

<script>
// Make image maps responsive
document.addEventListener('DOMContentLoaded', function() {
    // Store original image dimensions
    const imageMaps = document.querySelectorAll('img[usemap]');
    
    imageMaps.forEach(function(img) {
        if (img.dataset.responsive !== 'true') return;
        
        const mapName = img.getAttribute('usemap').replace('#', '');
        const map = document.querySelector('map[name="' + mapName + '"]');
        if (!map) return;
        
        const areas = map.querySelectorAll('area');
        const originalWidth = img.naturalWidth || img.width;
        
        // Store original coordinates
        areas.forEach(function(area) {
            area.dataset.originalCoords = area.getAttribute('coords');
        });
        
        // Resize function
        function resizeMap() {
            const currentWidth = img.clientWidth;
            const scale = currentWidth / originalWidth;
            
            areas.forEach(function(area) {
                const originalCoords = area.dataset.originalCoords.split(',');
                const newCoords = originalCoords.map(function(coord) {
                    return Math.round(parseInt(coord) * scale);
                });
                area.setAttribute('coords', newCoords.join(','));
            });
        }
        
        // Initial resize
        if (img.complete) {
            resizeMap();
        } else {
            img.addEventListener('load', resizeMap);
        }
        
        // Resize on window resize
        window.addEventListener('resize', resizeMap);
    });
    
    // Tooltip functionality for image map areas
    const tooltip = document.createElement('div');
    tooltip.className = 'map-tooltip';
    document.body.appendChild(tooltip);
    
    document.querySelectorAll('area[data-tooltip]').forEach(function(area) {
        area.addEventListener('mouseenter', function(e) {
            tooltip.textContent = this.dataset.tooltip;
            tooltip.classList.add('visible');
            
            // Position tooltip near cursor
            const img = document.querySelector('img[usemap="#' + this.closest('map').name + '"]');
            const rect = img.getBoundingClientRect();
            const coords = this.getAttribute('coords').split(',');
            
            // Calculate center of area
            let x, y;
            if (this.getAttribute('shape') === 'circle') {
                x = parseInt(coords[0]) + rect.left;
                y = parseInt(coords[1]) + rect.top - 40;
            } else {
                x = (parseInt(coords[0]) + parseInt(coords[2])) / 2 + rect.left;
                y = parseInt(coords[1]) + rect.top - 40;
            }
            
            tooltip.style.left = x + 'px';
            tooltip.style.top = y + 'px';
        });
        
        area.addEventListener('mouseleave', function() {
            tooltip.classList.remove('visible');
        });
    });
});
</script>
