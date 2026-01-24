import './bootstrap';
import { initClockAndGreeting } from './admin/common';

document.addEventListener('DOMContentLoaded', () => {
    const adminName = document.body.dataset.adminName;
    if (adminName) {
        initClockAndGreeting(adminName);
    }
});