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
            
            const toggleTag = (text, tagName) => {
                const startTag = `<${tagName}>`;
                const endTag = `</${tagName}>`;
                
                if (text.startsWith(startTag) && text.endsWith(endTag)) {
                    return text.substring(startTag.length, text.length - endTag.length);
                } else {
                    return `${startTag}${text}${endTag}`;
                }
            };

            switch (type) {
                case 'b': editor.insert(toggleTag(selectedText, 'b')); break;
                case 'i': editor.insert(toggleTag(selectedText, 'i')); break;
                case 'p': editor.insert(toggleTag(selectedText, 'p')); break;
                case 'h1': editor.insert(toggleTag(selectedText, 'h1')); break;
                case 'h2': editor.insert(toggleTag(selectedText, 'h2')); break;
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

export function initBannersPage() {
    const uploadForm = document.getElementById('uploadForm');
    const editForm = document.getElementById('editForm');
    
    if (!document.querySelector('.leaf-menu-item')) return;

    window.loadBanners = (menuId, el) => {
        document.querySelectorAll('.leaf-menu-item').forEach(i => i.classList.remove('active', 'border-admin-blue', 'bg-slate-100'));
        el.classList.add('active', 'border-admin-blue', 'bg-slate-100');
        
        window.currentMenuId = menuId;

        fetch(`/admin/banners/fetch/${menuId}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('imageArea').innerHTML = data.html;
            });
    };

    window.openUploadModal = () => {
        if (!window.currentMenuId) { alert("Please select a page first"); return; }
        uploadForm.reset();
        document.getElementById('uploadPreviewContainer').innerHTML = `
            <i class="fas fa-cloud-arrow-up text-2xl text-slate-300 mb-2"></i>
            <span class="text-slate-400 font-bold text-[10px] uppercase tracking-widest">Click to select 48:9 image</span>
        `;
        const modal = document.getElementById('uploadModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    window.openEditModal = (id, name, fullSlug, isActive) => {
        window.currentBannerId = id;
        editForm.reset();
        document.getElementById('editPreviewContainer').innerHTML = `<img src="/${fullSlug}/${name}?t=${Date.now()}" class="w-full h-full object-cover">`;
        
        const toggle = document.getElementById('editActiveToggle');
        const lbl = document.getElementById('editStatusLabel');
        toggle.checked = (isActive == 1);
        lbl.innerText = toggle.checked ? 'Active' : 'Inactive';

        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    window.closeModal = (id) => {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('active');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }
    };

    window.handlePreview = (input, containerId) => {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById(containerId).innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    window.deleteImage = (id) => {
        if (confirm('Delete this banner permanently?')) {
            fetch(`/admin/banners/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }).then(() => {
                const activeItem = document.querySelector('.leaf-menu-item.active');
                if (activeItem) loadBanners(window.currentMenuId, activeItem);
            });
        }
    };

    if (uploadForm) {
        uploadForm.onsubmit = function(e) {
            e.preventDefault();
            fetch(`/admin/banners/upload/${window.currentMenuId}`, {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }).then(() => { 
                closeModal('uploadModal'); 
                loadBanners(window.currentMenuId, document.querySelector('.leaf-menu-item.active')); 
            });
        };
    }

    if (editForm) {
        editForm.onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('is_active', document.getElementById('editActiveToggle').checked ? 1 : 0);
            
            fetch(`/admin/banners/${window.currentBannerId}`, {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }).then(() => { 
                closeModal('editModal'); 
                loadBanners(window.currentMenuId, document.querySelector('.leaf-menu-item.active')); 
            });
        };
        
        document.getElementById('editActiveToggle').onchange = function() {
            document.getElementById('editStatusLabel').innerText = this.checked ? 'Active' : 'Inactive';
        };
    }
}

export function initSlidersPage() {
    const list = document.getElementById('slider-list');
    const addForm = document.querySelector('#addModal form');
    const editForm = document.getElementById('editForm');
    if (!list && !addForm && !editForm) return;

    window.closeModal = (id) => {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('active');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }
    };

    window.handlePreview = (input, containerId) => {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById(containerId).innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    window.updateCount = (el, counterId, limit) => {
        const counter = document.getElementById(counterId);
        if (counter) {
            const len = el.value.length;
            counter.innerText = `${len}/${limit}`;
            if (len >= limit) {
                counter.className = 'absolute right-3 bottom-2.5 text-[9px] text-red-500 font-bold';
            } else {
                counter.className = 'absolute right-3 bottom-2.5 text-[9px] text-slate-300 font-bold';
            }
        }
    };

    window.openAddModal = () => {
        addForm.reset();
        document.getElementById('addPreview').innerHTML = `<i class="fas fa-cloud-arrow-up text-2xl text-slate-300 mb-2"></i><span class="text-slate-400 font-bold text-[10px] uppercase tracking-widest">Click to select 23:9 image</span>`;
        const modal = document.getElementById('addModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    window.openEditModal = (slider) => {
        window.currentSliderId = slider.id;
        document.getElementById('editH1').value = slider.header_1;
        document.getElementById('editH2').value = slider.header_2;
        document.getElementById('editDesc').value = slider.description;
        document.getElementById('editBT').value = slider.button_text || 'Explore More';
        document.getElementById('editLink').value = slider.link_url || '';
        document.getElementById('editActive').checked = slider.is_active == 1;
        document.getElementById('sliderStatusLabel').innerText = slider.is_active == 1 ? 'Active' : 'Inactive';
        document.getElementById('editPreview').innerHTML = `<img src="/storage/${slider.image_path}" class="w-full h-full object-cover">`;

        ['editH1', 'editH2', 'editDesc', 'editBT'].forEach(id => {
            const el = document.getElementById(id);
            const counterId = id.replace('edit', 'editC').replace('H1', '1').replace('H2', '2').replace('Desc', 'D').replace('BT', 'BT');
            updateCount(el, counterId, el.getAttribute('maxlength'));
        });

        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    const submitForm = (form, url, isUpdate = false) => {
        const formData = new FormData(form);
        if (isUpdate) {
            formData.append('_method', 'PUT');
            formData.append('is_active', document.getElementById('editActive').checked ? 1 : 0);
        }

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(async res => {
            const data = await res.json();
            if (res.ok) Turbo.visit(window.location.href);
            else alert("Error: " + (data.error || "Validation failed"));
        })
        .catch(() => alert("Server error occurred"));
    };

    if (addForm) {
        addForm.onsubmit = (e) => {
            e.preventDefault();
            submitForm(addForm, '/admin/sliders', false);
        };
    }

    if (editForm) {
        editForm.onsubmit = (e) => {
            e.preventDefault();
            submitForm(editForm, `/admin/sliders/${window.currentSliderId}`, true);
        };
        document.getElementById('editActive').onchange = function() {
            document.getElementById('sliderStatusLabel').innerText = this.checked ? 'Active' : 'Inactive';
        };
    }

    if (list) {
        new Sortable(list, {
            animation: 150, handle: '.drag-handle', ghostClass: 'bg-slate-50',
            onEnd: () => {
                let orders = [];
                document.querySelectorAll('.sortable-item').forEach((el, index) => orders.push({ id: el.dataset.id, order: index + 1 }));
                fetch('/admin/sliders/update-order', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ orders })
                });
            }
        });
    }
}