import './bootstrap';
import * as Turbo from "@hotwired/turbo";
import '@fortawesome/fontawesome-free/css/all.min.css';
import {
    initGlobalHelpers,
    initLayoutUI,
    restoreSidebarScroll,
    initTreeLogic,
    initMenuPage,
    initPagesPage,
    initBannersPage,
    initSlidersPage,
    initProductsPage,
    initScholarshipPage,
    initCSRPage,
    initNewsPage,
    initDirectorsPage,
    initMedicalJournalsPage,
    initComplaintsPage,
    initReportModule,
    initFooterPage
} from './admin/common';

Turbo.start();

document.addEventListener('turbo:load', () => {
    initGlobalHelpers();
    initLayoutUI();
    restoreSidebarScroll();
    initTreeLogic();
    initMenuPage();
    initPagesPage();
    initBannersPage();
    initSlidersPage();
    initProductsPage();
    initScholarshipPage();
    initCSRPage();
    initNewsPage();
    initDirectorsPage();
    initMedicalJournalsPage();
    initReportModule();
    initComplaintsPage();
    initFooterPage();
});