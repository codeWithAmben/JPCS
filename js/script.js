document.addEventListener("DOMContentLoaded", () => {
    const hamburger = document.getElementById("hamburger");
    const mobileNav = document.getElementById("mobileNav");

    if(hamburger && mobileNav) {
        hamburger.addEventListener("click", () => {
            mobileNav.classList.toggle("active");
            
            // Animate hamburger lines
            const spans = hamburger.querySelectorAll('span');
            if(mobileNav.classList.contains('active')){
                spans[0].style.transform = "rotate(45deg) translate(5px, 6px)";
                spans[1].style.opacity = "0";
                spans[2].style.transform = "rotate(-45deg) translate(5px, -6px)";
            } else {
                spans[0].style.transform = "none";
                spans[1].style.opacity = "1";
                spans[2].style.transform = "none";
            }
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