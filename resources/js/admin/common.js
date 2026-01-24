export function initClockAndGreeting(adminName) {
    const clockEl = document.getElementById('clock');
    const greetingEl = document.getElementById('greetingText');

    if (clockEl) {
        const updateClock = () => {
            clockEl.innerText = new Date().toLocaleTimeString('en-US', {
                hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true
            });
        };
        updateClock();
        setInterval(updateClock, 1000);
    }

    if (greetingEl) {
        const hour = new Date().getHours();
        let greeting = hour < 12 ? 'Good Morning' : (hour < 18 ? 'Good Afternoon' : 'Good Evening');
        greetingEl.innerHTML = `<span class="text-gray-400">${greeting}, </span><span class="text-admin-primary font-semibold">${adminName}</span>`;
    }
}