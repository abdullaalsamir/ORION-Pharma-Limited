import './bootstrap';

import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, EffectFade } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-fade';

function animateHeight(selector, updateLogic) {
    const container = document.querySelector(selector);
    if (!container) {
        updateLogic();
        return;
    }

    const startHeight = container.offsetHeight;
    container.style.height = startHeight + 'px';
    
    void container.offsetHeight; 

    updateLogic();

    container.style.height = 'auto';
    const endHeight = container.offsetHeight;

    container.style.height = startHeight + 'px';
    
    void container.offsetHeight;

    container.style.height = endHeight + 'px';

    const onEnd = (e) => {
        if (e.propertyName === 'height') {
            container.style.height = 'auto';
            container.removeEventListener('transitionend', onEnd);
        }
    };
    container.addEventListener('transitionend', onEnd);
}

window.setFilterMode = function(mode) {
    window.filterMode = mode;
    document.querySelectorAll('.filter-mode-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('mode-' + mode).classList.add('active');
    animateHeight('#main-smooth-wrapper', () => applyFilter());
};

window.setLetter = function(letter, event) {
    window.selectedLetter = letter;
    document.querySelectorAll('.letter-btn').forEach(b => b.classList.remove('active'));
    if(event && event.target) event.target.classList.add('active');
    animateHeight('#main-smooth-wrapper', () => applyFilter());
};

function applyFilter() {
    const cards = document.querySelectorAll('.product-card');
    let visibleCount = 0;
    const mode = window.filterMode || 'generic';
    const letter = window.selectedLetter || 'all';

    cards.forEach(card => {
        const compareVal = mode === 'generic' ? card.dataset.generic : card.dataset.trade;
        if (letter === 'all' || compareVal.startsWith(letter)) {
            card.classList.remove('hidden');
            visibleCount++;
        } else {
            card.classList.add('hidden');
        }
    });
    document.getElementById('no-results').classList.toggle('hidden', visibleCount > 0);
}

window.switchTab = function(name) {
    animateHeight('#show-smooth-wrapper', () => {
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-content-' + name).classList.remove('hidden');
        document.getElementById('tab-btn-' + name).classList.add('active');
    });
};

document.addEventListener('DOMContentLoaded', () => {
    const productImages = document.querySelectorAll('.product-image');
    
    productImages.forEach(img => {
        const revealImage = () => {
            img.parentElement.classList.remove('shimmer');
            img.classList.add('is-loaded');
        };

        if (img.complete) {
            revealImage();
        } else {
            img.addEventListener('load', revealImage);
            
            img.addEventListener('error', () => {
                img.parentElement.classList.remove('shimmer');
                img.parentElement.classList.add('bg-slate-100');
            });
        }
    });

    const subMenuItems = document.querySelectorAll('.group\\/sub');

    subMenuItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            const dropdown = this.querySelector('.level-3-menu');
            if (!dropdown) return;

            const chevron = this.querySelector('.sub-chevron');
            
            dropdown.classList.remove('is-active', 'is-ready', 'is-flipped');

            const parentRect = this.getBoundingClientRect();
            const dropdownWidth = 224;
            const spaceOnRight = window.innerWidth - (parentRect.right + dropdownWidth);

            if (spaceOnRight < 0) {
                dropdown.classList.add('is-flipped');
                if (chevron) {
                    chevron.classList.remove('fa-chevron-right');
                    chevron.classList.add('fa-chevron-left');
                }
            } else {
                if (chevron) {
                    chevron.classList.remove('fa-chevron-left');
                    chevron.classList.add('fa-chevron-right');
                }
            }

            void dropdown.offsetWidth; 

            dropdown.classList.add('is-ready', 'is-active');
        });

        item.addEventListener('mouseleave', function() {
            const dropdown = this.querySelector('.level-3-menu');
            if (dropdown) {
                dropdown.classList.remove('is-active');
                
                setTimeout(() => {
                    if (!dropdown.classList.contains('is-active')) {
                        dropdown.classList.remove('is-ready', 'is-flipped');
                    }
                }, 200); 
            }
        });
    });

    if (document.querySelector('.homeSwiper')) {
        new Swiper('.homeSwiper', {
            modules: [Navigation, Pagination, Autoplay, EffectFade],
            
            simulateTouch: false,
            loop: true,
            effect: 'fade',
            speed: 1000,
            fadeEffect: {
                crossFade: true
            },
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    }
});

window.showSuccessModal = function () {
    const modal = document.getElementById('successModal');
    if (!modal) return;

    const box = modal.querySelector('div');

    modal.classList.remove('pointer-events-none', 'opacity-0');
    modal.classList.add('opacity-100');

    box.classList.remove('translate-y-8', 'opacity-0');
    box.classList.add('translate-y-0', 'opacity-100');
};

window.closeSuccessModal = function () {
    const modal = document.getElementById('successModal');
    if (!modal) return;

    const box = modal.querySelector('div');

    box.classList.remove('translate-y-0', 'opacity-100');
    box.classList.add('translate-y-8', 'opacity-0');

    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');

    setTimeout(() => {
        modal.classList.add('pointer-events-none');
    }, 300);
};

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('successModal')) {
        const form = document.querySelector('form');
        if (form) form.reset();

        setTimeout(showSuccessModal, 150);
    }
});
