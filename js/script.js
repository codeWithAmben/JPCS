document.addEventListener("DOMContentLoaded", () => {
    const hamburger = document.getElementById("hamburger");
    const mobileNav = document.getElementById("mobileNav");
    const mobileNavOverlay = document.getElementById("mobileNavOverlay");

    // UX Improvement: Auto-dismiss flash messages after 4 seconds
    const flashAlerts = document.querySelectorAll('.alert-dismissible');
    if (flashAlerts.length > 0) {
        setTimeout(() => {
            flashAlerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 4000);
    }

    function openMobileNav() {
        if (mobileNav) mobileNav.classList.add('active');
        if (hamburger) hamburger.classList.add('active');
        if (mobileNavOverlay) mobileNavOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // Exit-intent Subscribe Modal Logic
    (function(){
        const modal = document.getElementById('exitModal');
        const closeBtn = document.getElementById('exitModalClose');
        const noThanks = document.getElementById('exitModalNoThanks');
        const form = document.getElementById('exitNewsletterForm');
        const emailInput = document.getElementById('exitEmail');
        const msg = document.getElementById('exitModalMessage');
        const storageKey = 'jpcs_exit_subscribed';
        const sessionKey = 'jpcs_exit_dismissed_session';

        if (!modal || !form) return;

        // Helpers
        function showModal(){
            if (localStorage.getItem(storageKey)) return;
            if (sessionStorage.getItem(sessionKey)) return;
            modal.setAttribute('aria-hidden','false');
            modal.style.display = 'block';
            if (emailInput) emailInput.focus();
        }
        function hideModal(){
            modal.setAttribute('aria-hidden','true');
            modal.style.display = 'none';
        }

        closeBtn?.addEventListener('click', function(){
            // Suppress for this session if closed via X
            sessionStorage.setItem(sessionKey, 'true');
            hideModal();
        });

        noThanks?.addEventListener('click', function(){ localStorage.setItem(storageKey,'true'); hideModal(); });

        // Close on Escape key
        document.addEventListener('keydown', function(e){
            if (e.key === 'Escape' && modal.style.display === 'block') hideModal();
        });

        form.addEventListener('submit', function(e){
            e.preventDefault();
            if (msg) msg.textContent = '';
            const email = emailInput ? emailInput.value.trim() : '';
            
            if (!email) { 
                if (msg) msg.textContent = 'Please enter your email.'; 
                return; 
            }
            
            // Basic email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                if (msg) msg.textContent = 'Please enter a valid email address.';
                return;
            }

            let endpoint = 'handlers/newsletter_subscribe.php';
            if (window.JPCS && window.JPCS.siteUrl) {
                endpoint = window.JPCS.siteUrl + '/handlers/newsletter_subscribe.php';
            } else {
                // Dynamic path resolution based on depth
                const pathSegments = window.location.pathname.split('/').filter(p => p.length > 0);
                // Assuming standard structure: /JPCS/index.php (depth 1) vs /JPCS/member/dashboard.php (depth 2)
                // If we are deeper than the root folder (ignoring the project folder itself if localhost/JPCS)
                // A safer fallback for standard XAMPP structure:
                if (window.location.pathname.includes('/pages/') || window.location.pathname.includes('/member/') || window.location.pathname.includes('/admin/')) {
                     endpoint = '../handlers/newsletter_subscribe.php';
                }
            }

            fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'email=' + encodeURIComponent(email)
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    if (msg) {
                        msg.style.color = 'green';
                        msg.textContent = data.message || 'Subscribed!';
                    }
                    localStorage.setItem(storageKey,'true');
                    setTimeout(hideModal, 1200);
                } else {
                    if (msg) {
                        msg.style.color = 'red';
                        msg.textContent = data.message || 'Subscription failed';
                    }
                }
            }).catch(err => {
                if (msg) {
                    msg.style.color = 'red';
                    msg.textContent = 'Network error. Try again later.';
                }
            });
        });

        // Exit intent detection (desktop mouse leave at top)
        let exitListenerEnabled = false;
        setTimeout(() => { exitListenerEnabled = true; }, 2000); // don't show immediately

        function handleMouseLeave(e){
            if (!exitListenerEnabled) return;
            if (e.clientY <= 10) {
                showModal();
            }
        }

        document.documentElement.addEventListener('mouseleave', handleMouseLeave);

        // Mobile: show on visibility change when user switches away (best-effort)
        // Added check: only if user has been on page for > 5 seconds to avoid immediate triggers
        const loadTime = Date.now();
        document.addEventListener('visibilitychange', function(){
            if (document.hidden && !localStorage.getItem(storageKey) && !sessionStorage.getItem(sessionKey)) {
                if (Date.now() - loadTime < 5000) return; // Too soon
                // do not show immediately – defer slightly
                setTimeout(() => { showModal(); }, 400);
            }
        });
    })();
    function closeMobileNav() {
        if (mobileNav) mobileNav.classList.remove('active');
        if (hamburger) hamburger.classList.remove('active');
        if (mobileNavOverlay) mobileNavOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if(hamburger && mobileNav) {
        hamburger.addEventListener("click", () => {
            if (mobileNav.classList.contains('active')) {
                closeMobileNav();
                hamburger.setAttribute('aria-expanded', 'false');
            } else {
                openMobileNav();
                hamburger.setAttribute('aria-expanded', 'true');
            }
        });
    }
    // Ensure mobile nav starts closed on page load (in case active is persisted by older scripts)
    if (mobileNav && mobileNav.classList.contains('active') && window.innerWidth > 900) {
        mobileNav.classList.remove('active');
    }
    if (mobileNavOverlay && mobileNavOverlay.classList.contains('active') && window.innerWidth > 900) {
        mobileNavOverlay.classList.remove('active');
    }

    // Close on overlay click
    if (mobileNavOverlay) {
        mobileNavOverlay.addEventListener('click', closeMobileNav);
    }

    // Close on link click
    if (mobileNav) {
        mobileNav.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', closeMobileNav);
        });
    }

    const menuBtn = document.getElementById("menuBtn");
    const dropdownMenu = document.getElementById("dropdownMenu");

    if(menuBtn && dropdownMenu) {
        menuBtn.addEventListener("click", (e) => {
            e.stopPropagation(); 
            dropdownMenu.classList.toggle("active");
        });

        document.addEventListener("click", () => {
            dropdownMenu.classList.remove("active");
        });
    }

    // Ensure UI is reset on page unload to avoid cross-page visual artifacts
    window.addEventListener('beforeunload', () => {
        if (mobileNav && mobileNav.classList.contains('active')) closeMobileNav();
        if (dropdownMenu && dropdownMenu.classList.contains('active')) dropdownMenu.classList.remove('active');
        if (hamburger && hamburger.classList.contains('active')) hamburger.classList.remove('active');
        document.body.style.overflow = '';
    });

    const eventContainer = document.getElementById("eventContainer");

    if (eventContainer) {
        // Determine correct path to handler based on current location
        let endpoint = 'handlers/get_events.php';
        if (window.JPCS && window.JPCS.siteUrl) {
            endpoint = window.JPCS.siteUrl + '/handlers/get_events.php';
        } else if (window.location.pathname.includes('/pages/') || window.location.pathname.includes('/member/') || window.location.pathname.includes('/admin/')) {
            endpoint = '../handlers/get_events.php';
        }

        fetch(endpoint)
            .then(response => response.json())
            .then(result => {
                if (!result.success || !result.data || result.data.length === 0) {
                    eventContainer.innerHTML = "<p>No upcoming events found.</p>";
                    return;
                }

                const events = result.data;
                const today = new Date();
                today.setHours(0,0,0,0);

                // Helper to parse date string
                const getEventTime = (e) => {
                    const dStr = e.date + (e.time ? ' ' + e.time : '');
                    return new Date(dStr).getTime();
                }

                // Find the next upcoming event
                let upcoming = events.find(e => getEventTime(e) >= today.getTime());
                
                // If no future events, show the most recent one
                if (!upcoming) {
                    // Sort descending to get latest
                    events.sort((a,b) => getEventTime(b) - getEventTime(a));
                    upcoming = events[0];
                }

                if (upcoming) {
                    const displayDate = (upcoming.date) ? new Date(`${upcoming.date}T00:00:00`) : null;
                    const dateStr = displayDate ? displayDate.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' }) : '';

                    eventContainer.innerHTML = `
                        <h3 class="anton-font" style="font-size: 2rem; margin-bottom: 10px;">${escapeHtml(upcoming.title)}</h3>
                        <p style="color: #ff6a00; font-weight: bold; margin-bottom: 15px;">${escapeHtml(dateStr)} ${upcoming.time ? '- ' + escapeHtml(upcoming.time) : ''}</p>
                        <p>${escapeHtml(upcoming.description)}</p>
                        ${upcoming.location ? `<p style="margin-top:10px; font-size:0.95rem; color:#666">Location: ${escapeHtml(upcoming.location)}</p>` : ''}
                        <div style="margin-top: 20px;">
                            <a href="events.php" class="btn-cta">View Details</a>
                        </div>
                    `;
                } else {
                    eventContainer.innerHTML = "<p>No upcoming events found.</p>";
                }
            })
            .catch(error => {
                console.error('Error loading events:', error);
                eventContainer.innerHTML = "<p>Unable to load event details at this time.</p>";
            });
    }

    // Social share handlers (footer)
    function openShareWindow(url) {
        const width = 600, height = 420;
        const left = (window.screen.width / 2) - (width / 2);
        const top = (window.screen.height / 2) - (height / 2);
        window.open(url, 'sharewindow', `width=${width},height=${height},top=${top},left=${left},resizable=yes`);
    }

    document.querySelectorAll('.footer-social [data-share]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            const type = el.getAttribute('data-share');
            const shareUrl = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title || 'JPCS Malvar Chapter');

            if (type === 'facebook') {
                openShareWindow(`https://www.facebook.com/sharer/sharer.php?u=${shareUrl}`);
            } else if (type === 'twitter') {
                openShareWindow(`https://twitter.com/intent/tweet?url=${shareUrl}&text=${title}`);
            } else if (type === 'linkedin') {
                openShareWindow(`https://www.linkedin.com/sharing/share-offsite/?url=${shareUrl}`);
            } else if (type === 'whatsapp') {
                openShareWindow(`https://api.whatsapp.com/send?text=${title}%20${shareUrl}`);
            } else if (type === 'copy') {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(window.location.href).then(function() {
                        alert('Link copied to clipboard');
                    }).catch(function() {
                        prompt('Copy the link below', window.location.href);
                    });
                } else {
                    prompt('Copy the link below', window.location.href);
                }
            }
        });
    });

    // Small HTML escaping helper to avoid inline XSS from XML
    function escapeHtml(unsafe) {
        if (!unsafe && unsafe !== 0) return '';
        return String(unsafe).replace(/[&<>"'`=\/]/g, function (s) {
            return ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
                '/': '&#x2F;',
                '`': '&#x60;',
                '=': '&#x3D;'
            })[s];
        });
    }
});