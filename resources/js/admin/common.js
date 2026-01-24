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
        greetingEl.innerHTML = `<span class="text-slate-400 font-normal">${txt}, </span><span class="text-slate-500 font-bold">${adminName}</span>`;
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

    const nestedSortables = document.querySelectorAll('.nested-sortable-list');
    for (var i = 0; i < nestedSortables.length; i++) {
        new Sortable(nestedSortables[i], {
            group: 'nested',
            animation: 150,
            fallbackOnBody: true,
            swapThreshold: 0.65,
            handle: '.drag-handle',
            onEnd: () => saveMenuOrder()
        });
    }

    window.closeModal = () => {
        modal.classList.remove('active');
        setTimeout(() => modal.classList.add('hidden'), 300);
    };

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.onclick = (e) => {
            e.preventDefault();
            const d = btn.dataset;
            document.getElementById('editForm').action = `/admin/menus/${d.id}`;
            document.getElementById('editName').value = d.name;
            document.getElementById('editParent').value = d.parent || '';
            document.getElementById('editActive').checked = d.active == '1';
            
            if (d.multi == '1') document.getElementById('edit-type-multi').checked = true;
            else document.getElementById('edit-type-functional').checked = true;

            modal.classList.remove('hidden');
            setTimeout(() => modal.classList.add('active'), 10);
            
            const lbl = document.getElementById('toggleLabel');
            if(lbl) lbl.innerText = d.active == '1' ? 'Active' : 'Inactive';
        };
    });

    const editActive = document.getElementById('editActive');
    if(editActive) {
        editActive.onchange = () => {
            document.getElementById('toggleLabel').innerText = editActive.checked ? 'Active' : 'Inactive';
        };
    }
}

function saveMenuOrder() {
    const menus = [];
    const process = (ul, parentId) => {
        Array.from(ul.children).forEach((li, index) => {
            if (li.dataset.id) {
                menus.push({ id: li.dataset.id, parent_id: parentId, sort_order: index });
                const sub = li.querySelector('.nested-sortable-list');
                if (sub) process(sub, li.dataset.id);
            }
        });
    };
    const root = document.getElementById('menu-sortable');
    if (root) process(root, null);

    fetch('/admin/menus/update-order', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ menus })
    });
}