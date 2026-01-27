import './bootstrap';
import * as Turbo from "@hotwired/turbo";
import '@fortawesome/fontawesome-free/css/all.min.css';
import { initLayoutUI, restoreSidebarScroll, initTreeLogic, initMenuPage, initPagesPage, initBannersPage } from './admin/common';

Turbo.start();

document.addEventListener('turbo:load', () => {
    initLayoutUI();
    restoreSidebarScroll();
    initTreeLogic();
    initMenuPage();
    initPagesPage();
    initBannersPage();
});