import './bootstrap';

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
});