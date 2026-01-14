import './bootstrap';
import '@fortawesome/fontawesome-free/css/all.min.css';

import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, EffectFade } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-fade';

document.addEventListener('DOMContentLoaded', () => {
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