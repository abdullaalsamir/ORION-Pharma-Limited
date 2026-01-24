export function initLayoutUI() {
    const adminName = document.body.dataset.adminName;
    const greetingEl = document.getElementById('greetingText');
    const clockEl = document.getElementById('clock');
    const sidebarNav = document.querySelector('.sidebar-nav');

    if (window.clockInterval) {
        clearInterval(window.clockInterval);
    }

    if (clockEl) {
        const updateClock = () => {
            const currentClockEl = document.getElementById('clock');
            if (!currentClockEl) return; 

            const now = new Date();
            currentClockEl.innerText = now.toLocaleTimeString('en-US', {
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit', 
                hour12: true
            });
        };

        updateClock();
        window.clockInterval = setInterval(updateClock, 1000);
    }

    if (greetingEl && adminName) {
        const hour = new Date().getHours();
        let text = 'Good Morning';
        if (hour >= 12 && hour < 17) text = 'Good Afternoon';
        else if (hour >= 17) text = 'Good Evening';

        greetingEl.innerHTML = `
            <span class="text-slate-400 font-normal">${text}, </span>
            <span class="text-slate-500 font-bold">${adminName}</span>
        `;
    }

    if (sidebarNav) {
        sidebarNav.addEventListener('scroll', () => {
            sessionStorage.setItem('sidebar-scroll', sidebarNav.scrollTop);
        });
    }
}

export function restoreSidebarScroll() {
    const sidebarNav = document.querySelector('.sidebar-nav');
    const scrollPos = sessionStorage.getItem('sidebar-scroll');
    if (sidebarNav && scrollPos) {
        sidebarNav.scrollTop = scrollPos;
    }
}