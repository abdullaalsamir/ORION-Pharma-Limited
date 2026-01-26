import Sortable from 'sortablejs';

export function initLayoutUI() {
    const body = document.body;
    const adminName = body.dataset.adminName;
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

export function initTreeLogic() {
    document.querySelectorAll('.collapse-toggle').forEach(btn => {
        btn.onclick = (e) => {
            e.preventDefault();
            const targetId = btn.dataset.target;
            const container = document.getElementById(targetId);
            if (!container) return;

            const isExpanding = container.classList.contains('hidden');

            if (isExpanding) {
                container.classList.remove('hidden');
                requestAnimationFrame(() => container.classList.add('expanded'));
                btn.querySelector('i').style.transform = 'rotate(90deg)';
            } else {
                container.classList.remove('expanded');
                btn.querySelector('i').style.transform = 'rotate(0deg)';
                setTimeout(() => {
                    if (!container.classList.contains('expanded')) container.classList.add('hidden');
                }, 300);
            }
        };
    });
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
            
            if (d.multi == '1') document.getElementById('edit-type-multi').checked = true;
            else document.getElementById('edit-type-functional').checked = true;

            if (d.parentActive === '0') checkParentStatus('0');
            else {
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

    if(editParent) editParent.onchange = () => checkParentStatus(editParent.options[editParent.selectedIndex].dataset.active);
    if(editActive) editActive.onchange = () => lbl.innerText = editActive.checked ? 'Active' : 'Inactive';
}

export function initPagesPage() {
    const modal = document.getElementById('pageModal');
    const editorEl = document.getElementById('ace-editor');
    if (!modal || !editorEl) return;

    if (typeof ace !== 'undefined') {
        ace.config.set("basePath", "/js/ace/src-min-noconflict");
        const editor = ace.edit("ace-editor");
        editor.setTheme("ace/theme/github_light_default");
        editor.session.setMode("ace/mode/php");
        editor.setOptions({ fontSize: "14px", showPrintMargin: false, useSoftTabs: true, tabSize: 4, wrap: true });

        const applyFormatting = (type) => {
            const selectedText = editor.getSelectedText();
            const range = editor.getSelectionRange();

            switch (type) {
                case 'b': editor.insert(`<b>${selectedText}</b>`); break;
                case 'i': editor.insert(`<i>${selectedText}</i>`); break;
                case 'p': editor.insert(`<p>${selectedText}</p>`); break;
                case 'h1': editor.insert(`<h1>${selectedText}</h1>`); break;
                case 'h2': editor.insert(`<h2>${selectedText}</h2>`); break;
                case 'br': 
                    editor.insert(`<br>\n`);
                    break;
                case 'ul':
                case 'ol':
                    const lines = selectedText.split('\n');
                    const items = lines.map(line => `  <li>${line}</li>`).join('\n');
                    const tag = type === 'ul' ? 'ul' : 'ol';
                    editor.insert(`<${tag}>\n${items}\n</${tag}>`);
                    break;
            }
            editor.focus();
        };

        document.querySelectorAll('#editor-toolbar button').forEach(btn => {
            btn.onclick = () => applyFormatting(btn.dataset.format);
        });

        window.closePageModal = () => {
            modal.classList.remove('active');
            setTimeout(() => modal.classList.add('hidden'), 300);
        };

        let currentPageId = null;
        document.querySelectorAll('.edit-page').forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                currentPageId = btn.dataset.id;
                document.getElementById('modalTitle').innerText = `Edit: ${btn.dataset.name}`;
                
                const strip = document.getElementById('imageStrip');
                strip.innerHTML = '<div class="text-[10px] text-slate-400 font-bold px-4 uppercase italic">Loading...</div>';
                fetch(`/admin/banners/get-for-editor/${currentPageId}`)
                    .then(res => res.json())
                    .then(images => {
                        strip.innerHTML = images.length ? '' : '<div class="text-[10px] text-slate-400 font-bold px-4">No Banners Found</div>';
                        images.forEach(img => {
                            const div = document.createElement('div');
                            div.className = "shrink-0 w-full h-20 rounded-xl overflow-hidden border-2 border-transparent hover:border-admin-blue cursor-pointer transition-all bg-white shadow-sm";
                            div.innerHTML = `<img src="${img.url}" class="w-full h-full object-cover">`;
                            div.onclick = () => {
                                editor.insert(`<div class="img-shine">\n  <img src="${img.url}" alt="${btn.dataset.name}">\n</div>\n`);
                                editor.focus();
                            };
                            strip.appendChild(div);
                        });
                    });

                const decoder = document.createElement('textarea');
                decoder.innerHTML = btn.dataset.content || '';
                editor.setValue(decoder.value, -1);

                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.add('active');
                    editor.resize();
                    editor.focus();
                }, 10);
            };
        });

        const saveBtn = document.getElementById('savePage');
        if (saveBtn) {
            saveBtn.onclick = () => {
                fetch(`/admin/pages/${currentPageId}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ content: editor.getValue() })
                }).then(() => Turbo.visit(window.location.href));
            };
        }
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