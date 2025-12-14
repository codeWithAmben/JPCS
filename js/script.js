document.addEventListener("DOMContentLoaded", () => {
    const hamburger = document.getElementById("hamburger");
    const mobileNav = document.getElementById("mobileNav");
    const mobileNavOverlay = document.getElementById("mobileNavOverlay");

    function openMobileNav() {
        if (mobileNav) mobileNav.classList.add('active');
        if (hamburger) hamburger.classList.add('active');
        if (mobileNavOverlay) mobileNavOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

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
            } else {
                openMobileNav();
            }

            // Animate hamburger lines using CSS class control when possible
            const spans = hamburger.querySelectorAll('span');
            if(hamburger.classList.contains('active')){
                spans[0].style.transform = "rotate(45deg) translate(6px, 6px)";
                spans[1].style.opacity = "0";
                spans[2].style.transform = "rotate(-45deg) translate(6px, -6px)";
            } else {
                spans[0].style.transform = "none";
                spans[1].style.opacity = "1";
                spans[2].style.transform = "none";
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
        const candidatePaths = [
            '/JPCS/database/events.xml',
            '/database/events.xml',
            'database/events.xml',
            '../database/events.xml',
            './database/events.xml'
        ];

        async function fetchFirstAvailable(paths) {
            for (const p of paths) {
                try {
                    const res = await fetch(p);
                    if (res.ok) return await res.text();
                } catch (e) {
                    // try next path
                }
            }
            throw new Error('All event XML fetch attempts failed');
        }

        (async () => {
            try {
                const xmlText = await fetchFirstAvailable(candidatePaths);
                const parser = new DOMParser();
                const xml = parser.parseFromString(xmlText, "application/xml");
                const eventNodes = Array.from(xml.querySelectorAll('event'));

                // Map to object and pick the next active upcoming event
                const events = eventNodes.map(node => ({
                    title: node.querySelector('title')?.textContent || '',
                    date: node.querySelector('date')?.textContent || '',
                    time: node.querySelector('time')?.textContent || '',
                    description: node.querySelector('description')?.textContent || '',
                    location: node.querySelector('location')?.textContent || '',
                    status: node.querySelector('status')?.textContent || ''
                }))
                .filter(e => (e.status || '').toLowerCase() === 'active');

                const today = new Date();
                today.setHours(0,0,0,0);

                const parseDateTs = (d, t) => {
                    if (!d) return NaN;
                    // Normalize date string YYYY-MM-DD
                    const dateOnly = d.trim();
                    // If time exists, try to parse it, otherwise default to midnight
                    let iso = dateOnly;
                    if (t) {
                        // Convert time like '09:00 AM' to '09:00:00'
                        const tm = t.trim();
                        // Use Date.parse on combined string for best result
                        const dt = new Date(`${dateOnly} ${tm}`);
                        return isNaN(dt.getTime()) ? new Date(`${dateOnly}T00:00:00`).getTime() : dt.getTime();
                    }
                    return new Date(`${dateOnly}T00:00:00`).getTime();
                };

                // Find earliest event with date >= today
                events.sort((a,b) => parseDateTs(a.date,a.time) - parseDateTs(b.date,b.time));
                const upcoming = events.find(e => parseDateTs(e.date,e.time) >= today.getTime()) || events[0];

                if (upcoming) {
                    const displayDate = (upcoming.date) ? new Date(`${upcoming.date}T00:00:00`) : null;
                    const dateStr = displayDate ? displayDate.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' }) : '';

                    eventContainer.innerHTML = `
                        <h3 class="anton-font" style="font-size: 2rem; margin-bottom: 10px;">${escapeHtml(upcoming.title)}</h3>
                        <p style="color: #ff6a00; font-weight: bold; margin-bottom: 15px;">${escapeHtml(dateStr)} ${upcoming.time ? '- ' + escapeHtml(upcoming.time) : ''}</p>
                        <p>${escapeHtml(upcoming.description)}</p>
                        ${upcoming.location ? `<p style="margin-top:10px; font-size:0.95rem; color:#666">Location: ${escapeHtml(upcoming.location)}</p>` : ''}
                    `;
                } else {
                    eventContainer.innerHTML = "<p>No upcoming events found.</p>";
                }
            } catch (error) {
                console.error('Error loading events XML:', error);
                eventContainer.innerHTML = "<p>Unable to load event details at this time.</p>";
            }
        })();
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