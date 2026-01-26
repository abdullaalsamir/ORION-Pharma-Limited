import Sortable from 'sortablejs';

export function initLayoutUI() {
    const adminName = document.body.dataset.adminName;
    const greetingEl = document.getElementById('greetingText');
    const clockEl = document.getElementById('clock');
    const sidebarNav = document.querySelector('.sidebar-nav');

    if (window.clockInterval) clearInterval(window.clockInterval);
    if (clockEl) {
        const updateClock = () => {
            const el = document.getElementById('clock');
            if (el) el.innerText = new Date().toLocaleTimeString('en-US', { hour:'2-digit', minute:'2-digit', second:'2-digit', hour12:true });
        };
        updateClock();
        window.clockInterval = setInterval(updateClock, 1000);
    }
    if (greetingEl && adminName) {
        const hr = new Date().getHours();
        let txt = hr < 12 ? 'Good Morning' : (hr < 17 ? 'Good Afternoon' : 'Good Evening');
        greetingEl.innerHTML = `<span class="text-slate-400 font-normal">${txt}, </span><span class="text-slate-600 font-bold">${adminName}</span>`;
    }
    if (sidebarNav) {
        sidebarNav.onscroll = () => sessionStorage.setItem('sidebar-scroll', sidebarNav.scrollTop);
    }
}

export function restoreSidebarScroll() {
    const nav = document.querySelector('.sidebar-nav');
    if (nav && sessionStorage.getItem('sidebar-scroll')) nav.scrollTop = sessionStorage.getItem('sidebar-scroll');
}

export function initMenuPage() {
    const modal = document.getElementById('editModal');
    if (!modal) return;

    document.querySelectorAll('.menu-sortable-list').forEach(list => {
        new Sortable(list, {
            animation: 150, handle: '.drag-handle', draggable: '.sortable-item', ghostClass: 'bg-slate-50',
            onEnd: () => saveMenuOrder()
        });
    });

    document.querySelectorAll('.collapse-toggle').forEach(btn => {
        btn.onclick = () => {
            const container = document.getElementById(btn.dataset.target);
            if (!container) return;
            const isExpanding = container.classList.contains('hidden') || !container.classList.contains('expanded');
            if (isExpanding) {
                container.classList.remove('hidden');
                requestAnimationFrame(() => container.classList.add('expanded'));
                btn.querySelector('i').style.transform = 'rotate(90deg)';
            } else {
                container.classList.remove('expanded');
                btn.querySelector('i').style.transform = 'rotate(0deg)';
                setTimeout(() => { if (!container.classList.contains('expanded')) container.classList.add('hidden'); }, 300);
                container.querySelectorAll('.tree-list').forEach(childList => {
                    childList.classList.remove('expanded');
                    childList.classList.add('hidden');
                    const childBtn = document.querySelector(`[data-target="${childList.id}"]`);
                    if(childBtn) childBtn.querySelector('i').style.transform = 'rotate(0deg)';
                });
            }
        };
    });

    window.closeModal = () => {
        modal.classList.remove('active');
        setTimeout(() => modal.classList.add('hidden'), 300);
    };

    const editParent = document.getElementById('editParent');
    const editActive = document.getElementById('editActive');
    const lbl = document.getElementById('toggleLabel');

    const checkParentStatus = (isParentActive) => {
        if (isParentActive === '0') {
            editActive.checked = false;
            editActive.disabled = true;
            editActive.parentElement.style.opacity = '0.7';
            lbl.innerText = 'Inactive (by Parent)';
            lbl.className = 'ml-3 font-bold text-red-400';
        } else {
            editActive.disabled = false;
            editActive.checked = true;
            editActive.parentElement.style.opacity = '1';
            lbl.innerText = 'Active';
            lbl.className = 'ml-3 font-bold text-slate-600';
        }
    };

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.onclick = (e) => {
            e.preventDefault();
            const d = btn.dataset;
            document.getElementById('editForm').action = `/admin/menus/${d.id}`;
            document.getElementById('editName').value = d.name;
            editParent.value = d.parent || '';
            
            Array.from(editParent.options).forEach(opt => {
                opt.disabled = false; opt.style.color = "";
                if(opt.value == d.id) { opt.disabled = true; opt.style.color = "#f87171"; }
            });

            if (d.multi == '1') document.getElementById('edit-type-multi').checked = true;
            else document.getElementById('edit-type-functional').checked = true;

            if (d.parentActive === '0') {
                checkParentStatus('0');
            } else {
                editActive.disabled = false;
                editActive.parentElement.style.opacity = '1';
                editActive.checked = d.active === '1';
                lbl.innerText = editActive.checked ? 'Active' : 'Inactive';
                lbl.className = 'ml-3 font-bold text-slate-600';
            }

            modal.classList.remove('hidden');
            setTimeout(() => modal.classList.add('active'), 10);
        };
    });

    if(editParent) {
        editParent.onchange = () => {
            const selectedOption = editParent.options[editParent.selectedIndex];
            checkParentStatus(selectedOption.dataset.active);
        };
    }

    if(editActive) {
        editActive.onchange = () => {
            lbl.innerText = editActive.checked ? 'Active' : 'Inactive';
        };
    }
}

function saveMenuOrder() {
    const menus = [];
    const process = (ul, parentId) => {
        Array.from(ul.children).forEach((li, index) => {
            if (li.dataset.id) {
                menus.push({ id: li.dataset.id, parent_id: parentId, sort_order: index });
                const subUl = li.querySelector('.menu-sortable-list');
                if (subUl) process(subUl, li.dataset.id);
            }
        });
    };
    const root = document.getElementById('root-menu-list');
    if (root) process(root, null);
    fetch('/admin/menus/update-order', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ menus })
    });
}