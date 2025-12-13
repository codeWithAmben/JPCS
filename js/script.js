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
        fetch("events.xml")
            .then(response => {
                if (!response.ok) throw new Error("Network response was not ok");
                return response.text();
            })
            .then(xmlText => {
                const parser = new DOMParser();
                const xml = parser.parseFromString(xmlText, "application/xml");
                const event = xml.querySelector("event");
                
                if(event) {
                    const title = event.querySelector("title").textContent;
                    const date = event.querySelector("date").textContent;
                    const desc = event.querySelector("description").textContent;
                    
                    eventContainer.innerHTML = `
                        <h3 class="anton-font" style="font-size: 2rem; margin-bottom: 10px;">${title}</h3>
                        <p style="color: #ff6a00; font-weight: bold; margin-bottom: 15px;">${date}</p>
                        <p>${desc}</p>
                    `;
                } else {
                    eventContainer.innerHTML = "<p>No upcoming events found.</p>";
                }
            })
            .catch(error => {
                console.error("Error loading XML:", error);
                eventContainer.innerHTML = "<p>Unable to load event details at this time.</p>";
            });
    }
});