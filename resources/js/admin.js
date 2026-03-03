import './bootstrap';
import * as Turbo from "@hotwired/turbo";

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
    initReportModule,
    initCareerPage,
    initComplaintsPage,
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
    initCareerPage();
    initComplaintsPage();
    initFooterPage();
});