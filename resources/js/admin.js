import './bootstrap';
import * as Turbo from "@hotwired/turbo";
import '@fortawesome/fontawesome-free/css/all.min.css';
import { initLayoutUI, restoreSidebarScroll, initMenuPage } from './admin/common';

Turbo.start();

document.addEventListener('turbo:load', () => {
    initLayoutUI();
    restoreSidebarScroll();
    initMenuPage();
});