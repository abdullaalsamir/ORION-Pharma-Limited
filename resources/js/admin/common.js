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

export function initProductsPage() {
    const genForm = document.getElementById('genForm');
    const prodForm = document.getElementById('prodForm');
    if (!genForm && !prodForm) return;

    window.closeModal = (id) => {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('active');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }
    };

    const showInlineError = (inputId, errorId, message) => {
        const input = document.getElementById(inputId);
        const error = document.getElementById(errorId);
        if (input) {
            input.classList.add('border-red-500', 'bg-red-50');
            input.classList.remove('border-slate-200', 'bg-white');
        }
        if (error) {
            error.innerText = message;
            error.classList.remove('hidden');
        }
    };

    const clearInlineError = (inputId, errorId) => {
        const input = document.getElementById(inputId);
        const error = document.getElementById(errorId);
        if (input) {
            input.classList.remove('border-red-500', 'bg-red-50');
            input.classList.add('border-slate-200', 'bg-white');
        }
        if (error) error.classList.add('hidden');
    };

    window.handlePreview = (input, containerId) => {
        if (input.files && input.files[0]) {
            const previewBox = document.getElementById('prodPreview');
            const errorMsg = document.getElementById('prodImageError');
            if (previewBox) {
                previewBox.classList.remove('border-red-500', 'bg-red-50');
                previewBox.classList.add('border-slate-200', 'bg-slate-50');
            }
            if (errorMsg) errorMsg.classList.add('hidden');

            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById(containerId).innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    const updateSidebarSelection = (el) => {
        document.querySelectorAll('.generic-list-item').forEach(item => {
            item.classList.remove('active', 'border-admin-blue', 'bg-blue-50/50', 'border-red-500', 'bg-red-50', 'shadow-inner');
            
            if (item.classList.contains('archived-item')) {
                item.classList.add('bg-red-50/50', 'border-red-100');
            } else {
                item.classList.add('bg-white', 'border-slate-200');
            }

            const nameSpan = item.querySelector('.generic-name');
            if (nameSpan) nameSpan.classList.remove('text-admin-blue', 'text-red-700');
        });

        if (el.classList.contains('archived-item')) {
            el.classList.add('active', 'border-red-500', 'bg-red-50', 'shadow-inner');
            el.classList.remove('bg-red-50/50', 'border-red-100');
            el.querySelector('.generic-name').classList.add('text-red-700');
        } else {
            el.classList.add('active', 'border-admin-blue', 'bg-blue-50/50', 'shadow-inner');
            el.classList.remove('bg-white', 'border-slate-200');
            el.querySelector('.generic-name').classList.add('text-admin-blue');
        }
    };

    const genNameInput = document.getElementById('genName');
    if (genNameInput) genNameInput.oninput = () => clearInlineError('genName', 'genNameError');

    window.loadProducts = (id, el) => {
        updateSidebarSelection(el);
        window.currentGenId = id;
        fetch(`/admin/products-actions/fetch/${id}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('productArea').innerHTML = data.html;
            });
    };

    window.openGenericModal = () => {
        window.currentEditGenericId = null;
        genForm.reset();
        clearInlineError('genName', 'genNameError');
        document.getElementById('genTitle').innerText = "Add Generic";
        document.getElementById('genActiveWrapper').classList.add('hidden');
        document.getElementById('genSubmitBtn').innerText = "Save Generic";
        const modal = document.getElementById('genericModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    window.openEditGeneric = (g) => {
        window.currentEditGenericId = g.id;
        document.getElementById('genTitle').innerText = "Edit Generic";
        document.getElementById('genName').value = g.name;
        
        const toggle = document.getElementById('genActive');
        const lbl = document.getElementById('genStatusLabel');
        toggle.checked = (g.is_active == 1);
        lbl.innerText = toggle.checked ? 'Active' : 'Inactive';

        document.getElementById('genActiveWrapper').classList.remove('hidden');
        document.getElementById('genSubmitBtn').innerText = "Save Changes";
        const modal = document.getElementById('genericModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    const genActive = document.getElementById('genActive');
    if(genActive) {
        genActive.onchange = () => {
            document.getElementById('genStatusLabel').innerText = genActive.checked ? 'Active' : 'Inactive';
        };
    }

    if (genForm) {
        genForm.onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const url = window.currentEditGenericId ? `/admin/products-actions/generic-update/${window.currentEditGenericId}` : `/admin/products-actions/generic-store`;
            if (window.currentEditGenericId) formData.append('_method', 'PUT');
            formData.set('is_active', document.getElementById('genActive').checked ? 1 : 0);

            fetch(url, { method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }})
                .then(async res => {
                    const data = await res.json();
                    if (res.ok) window.location.reload();
                    else {
                        showInlineError('genName', 'genNameError', data.error || "Name already exists.");
                    }
                });
        };
    }

    window.deleteGeneric = (id) => {
        if (confirm('Delete this Generic? All products will move to "Archived Products".')) {
            fetch(`/admin/products-actions/generic-delete/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }})
                .then(res => res.json()).then(data => { if (data.success) window.location.reload(); });
        }
    };

    const prodNameInput = document.getElementById('p_trade_name');
    if (prodNameInput) prodNameInput.oninput = () => clearInlineError('p_trade_name', 'prodNameError');

    window.openAddProduct = () => {
        prodForm.reset();
        prodForm.dataset.id = "";
        clearInlineError('p_trade_name', 'prodNameError');

        document.getElementById('p_generic_id_wrapper').classList.add('hidden');
        
        const previewBox = document.getElementById('prodPreview');
        const errorMsg = document.getElementById('prodImageError');
        if (previewBox) {
            previewBox.classList.remove('border-red-500', 'bg-red-50');
            previewBox.classList.add('border-slate-200', 'bg-slate-50');
        }
        if (errorMsg) errorMsg.classList.add('hidden');
        
        document.getElementById('prodTitle').innerText = "Add Product";
        document.getElementById('prodReplaceOverlay').classList.add('hidden');
        document.getElementById('prodPreview').innerHTML = `<i class="fas fa-cloud-arrow-up text-2xl text-slate-300 mb-2"></i><span class="text-slate-400 font-bold text-[10px] uppercase tracking-widest text-center px-4">Select 16:9 Image</span>`;
        document.getElementById('prodActiveWrapper').classList.add('opacity-0', 'pointer-events-none');
        
        const modal = document.getElementById('productModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    window.openEditProduct = (p) => {
        prodForm.reset();
        prodForm.dataset.id = p.id;
        clearInlineError('p_trade_name', 'prodNameError');

        const genWrapper = document.getElementById('p_generic_id_wrapper');
        if (!p.generic_id) {
            genWrapper.classList.remove('hidden');
        } else {
            genWrapper.classList.add('hidden');
        }

        document.getElementById('prodTitle').innerText = "Edit Product";
        document.getElementById('prodReplaceOverlay').classList.remove('hidden');
        
        const fields = ['trade_name', 'preparation', 'therapeutic_class', 'indications', 'dosage_admin', 'use_children', 'use_pregnancy_lactation', 'contraindications', 'precautions', 'side_effects', 'drug_interactions', 'high_risk', 'overdosage', 'storage', 'presentation', 'how_supplied', 'commercial_pack', 'packaging', 'official_specification'];
        fields.forEach(f => {
            const el = document.getElementById('p_' + f);
            if (el) el.value = p[f] || '';
        });

        const genSelect = document.getElementById('p_generic_id');
        if(genSelect) genSelect.value = p.generic_id || '';

        const toggle = document.getElementById('p_active');
        const lbl = document.getElementById('prodStatusLabel');
        toggle.checked = (p.is_active == 1);
        lbl.innerText = toggle.checked ? 'Active' : 'Inactive';
        document.getElementById('prodPreview').innerHTML = `<img src="/storage/${p.image_path}" class="w-full h-full object-cover">`;
        document.getElementById('prodActiveWrapper').classList.remove('opacity-0', 'pointer-events-none');
        
        const modal = document.getElementById('productModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    const pActive = document.getElementById('p_active');
    if(pActive) {
        pActive.onchange = () => {
            document.getElementById('prodStatusLabel').innerText = pActive.checked ? 'Active' : 'Inactive';
        };
    }

    if (prodForm) {
        prodForm.onsubmit = function(e) {
            e.preventDefault();
            
            const id = this.dataset.id;
            const fileInput = document.getElementById('prodInput');
            const previewBox = document.getElementById('prodPreview');
            const errorMsg = document.getElementById('prodImageError');

            if (!id && fileInput.files.length === 0) {
                previewBox.classList.add('border-red-500', 'bg-red-50');
                previewBox.classList.remove('border-slate-200', 'bg-slate-50');
                if (errorMsg) errorMsg.classList.remove('hidden');
                prodForm.scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }

            const url = id ? `/admin/products-actions/product-update/${id}` : `/admin/products-actions/product-store/${window.currentGenId}`;
            const formData = new FormData(this);
            if (id) {
                formData.append('_method', 'PUT');
                formData.append('is_active', document.getElementById('p_active').checked ? 1 : 0);
            }

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(async res => {
                const data = await res.json();
                if (res.ok) {
                    closeModal('productModal'); 
                    window.location.reload();
                } else {
                    showInlineError('p_trade_name', 'prodNameError', data.error || "Trade name already exists in this generic.");
                    prodForm.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
        };
    }

    window.deleteProduct = (id) => {
        if (confirm('Delete product?')) fetch(`/admin/products-actions/product-delete/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }})
            .then(() => window.location.reload());
    };
}

export function initScholarshipPage() {
    const list = document.getElementById('scholar-sortable-list');
    const addForm = document.querySelector('#addModal form');
    const editForm = document.getElementById('editForm');
    if (!list && !addForm && !editForm) return;

    if (list) {
        new Sortable(list, {
            animation: 150, handle: '.drag-handle', ghostClass: 'bg-slate-50',
            onEnd: () => {
                let orders = [];
                document.querySelectorAll('#scholar-sortable-list tr').forEach((el, index) => {
                    orders.push({ id: el.dataset.id, order: index + 1 });
                });
                fetch('/admin/scholarship-actions/update-order', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ orders })
                });
            }
        });
    }

    window.openAddModal = () => {
        const modal = document.getElementById('addModal');
        addForm.reset();
        addForm.scrollTop = 0;
        document.getElementById('addPreview').innerHTML = `<i class="fas fa-camera text-3xl text-slate-300 mb-2"></i><span class="text-slate-400 font-bold text-[9px] uppercase tracking-widest text-center px-4 opacity-60">Upload Portrait</span>`;
        document.getElementById('addImgError').classList.add('hidden');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    window.openEditModal = (item, fullSlug) => {
        window.currentScholarId = item.id;
        const form = document.getElementById('editForm');
        form.scrollTop = 0;

        document.getElementById('editName').value = item.name;
        document.getElementById('editCollege').value = item.medical_college;
        
        document.getElementById('editSession').value = item.session ? item.session.replace('Session: ', '') : '';
        document.getElementById('editRoll').value = item.roll_no ? item.roll_no.replace('Roll No: ', '') : '';
        
        const toggle = document.getElementById('editActive');
        const lbl = document.getElementById('scholarStatusLabel');
        toggle.checked = item.is_active == 1;
        lbl.innerText = toggle.checked ? 'Active' : 'Inactive';
        
        const filename = item.image_path.split('/').pop();
        const imageUrl = `/${fullSlug}/${filename}?t=${Date.now()}`;
        
        document.getElementById('editPreview').innerHTML = `<img src="${imageUrl}" class="w-full h-full object-cover">`;

        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    const handleScholarSubmit = (e, url, isUpdate = false) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        if (isUpdate) {
            formData.append('_method', 'PUT');
            formData.append('is_active', document.getElementById('editActive').checked ? 1 : 0);
        } else {
            if (document.getElementById('addInput').files.length === 0) {
                document.getElementById('addImgError').classList.remove('hidden');
                e.target.scrollTop = 0;
                return;
            }
        }

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        }).then(res => res.json()).then(data => {
            if (data.success) Turbo.visit(window.location.href);
            else alert(data.error || "Operation failed.");
        });
    };

    if (addForm) addForm.onsubmit = (e) => handleScholarSubmit(e, '/admin/scholarship-actions/store', false);
    if (editForm) {
        editForm.onsubmit = (e) => handleScholarSubmit(e, `/admin/scholarship-actions/${window.currentScholarId}`, true);
        document.getElementById('editActive').onchange = function() {
            document.getElementById('scholarStatusLabel').innerText = this.checked ? 'Active' : 'Inactive';
        };
    }
}

export function initCSRPage() {
    const editForm = document.getElementById('editForm');
    const addForm = document.querySelector('#addModal form');
    if (!editForm && !addForm) return;

    document.querySelectorAll('.csr-sortable-list').forEach(list => {
        new Sortable(list, {
            animation: 150,
            handle: '.drag-handle',
            draggable: '.sortable-item',
            ghostClass: 'bg-slate-50',
            onEnd: () => {
                let orders = [];
                list.querySelectorAll('.sortable-item').forEach((row, index) => {
                    orders.push({ id: row.dataset.id, order: index + 1 });
                });

                fetch('/admin/csr-actions/update-order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ orders })
                });
            }
        });
    });

    window.openAddModal = () => {
        const modal = document.getElementById('addModal');

        addForm.reset();
        addForm.scrollTop = 0;

        document.getElementById('addPreview').innerHTML = `
            <i class="fas fa-camera text-3xl text-slate-300 mb-2"></i>
            <span class="text-slate-400 font-bold text-[10px] uppercase tracking-widest text-center px-4 opacity-60">
                Select Image
            </span>
        `;

        document.getElementById('addImgError').classList.add('hidden');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    window.openEditModal = (item, fullSlug) => {
        window.currentCsrId = item.id;

        const form = document.getElementById('editForm');
        form.scrollTop = 0;

        document.getElementById('editTitle').value = item.title;
        document.getElementById('editDesc').value = item.description;
        document.getElementById('editDate').value = item.csr_date.split('T')[0];

        const toggle = document.getElementById('editActive');
        const lbl = document.getElementById('csrStatusLabel');

        toggle.checked = item.is_active == 1;
        lbl.innerText = toggle.checked ? 'Active' : 'Inactive';

        const filename = item.image_path.split('/').pop();
        document.getElementById('editPreview').innerHTML = `
            <img src="/${fullSlug}/${filename}?t=${Date.now()}" class="w-full h-full object-cover">
        `;

        updateCount(document.getElementById('editTitle'), 'editC1', 100);
        updateCount(document.getElementById('editDesc'), 'editCD', 500);

        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    const handleCsrSubmit = (e, url, isUpdate = false) => {
        e.preventDefault();
        const formData = new FormData(e.target);

        if (isUpdate) {
            formData.append('_method', 'PUT');
            formData.append(
                'is_active',
                document.getElementById('editActive').checked ? 1 : 0
            );
        } else {
            if (document.getElementById('addInput').files.length === 0) {
                document.getElementById('addImgError').classList.remove('hidden');
                document.getElementById('addPreview')
                    .classList.add('border-red-500', 'bg-red-50');
                return;
            }
        }

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) Turbo.visit(window.location.href);
            else alert(data.error || "Validation failed.");
        })
        .catch(err => {
            console.error(err);
            alert("A server error occurred.");
        });
    };

    if (addForm) {
        addForm.onsubmit = (e) =>
            handleCsrSubmit(e, '/admin/csr-actions/store', false);
    }

    if (editForm) {
        editForm.onsubmit = (e) =>
            handleCsrSubmit(e, `/admin/csr-actions/${window.currentCsrId}`, true);

        document.getElementById('editActive').onchange = function () {
            document.getElementById('csrStatusLabel').innerText =
                this.checked ? 'Active' : 'Inactive';
        };
    }

    window.deleteCsr = (id) => {
        if (confirm('Delete this CSR item permanently?')) {
            fetch(`/admin/csr-actions/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Turbo.visit(window.location.href, { action: "replace" });
                } else {
                    alert("Error: " + data.error);
                }
            })
            .catch(err => {
                console.error(err);
                alert("A system error occurred.");
            });
        }
    };
}

export function initNewsPage() {
    const editForm = document.getElementById('editForm');
    const addForm = document.querySelector('#addModal form');
    if (!editForm && !addForm) return;

    document.querySelectorAll('.news-sortable-list').forEach(list => {
        new Sortable(list, {
            animation: 150,
            handle: '.drag-handle',
            draggable: '.sortable-item',
            ghostClass: 'bg-slate-50',
            onEnd: () => {
                let orders = [];
                list.querySelectorAll('.sortable-item').forEach((row, index) => {
                    orders.push({ id: row.dataset.id, order: index + 1 });
                });
                fetch('/admin/news-actions/update-order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ orders })
                });
            }
        });
    });

    window.openAddModal = () => {
        const modal = document.getElementById('addModal');
        addForm.reset();
        addForm.scrollTop = 0;
        document.getElementById('addPreview').innerHTML = `
            <i class="fas fa-camera text-3xl text-slate-300 mb-2"></i>
            <span class="text-slate-400 font-bold text-[10px] uppercase tracking-widest text-center px-4 opacity-60">
                Select Image
            </span>`;
        document.getElementById('addImgError').classList.add('hidden');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    window.openEditModal = (item, fullSlug) => {
        window.currentNewsId = item.id;
        const form = document.getElementById('editForm');
        form.scrollTop = 0;

        document.getElementById('editTitle').value = item.title;
        document.getElementById('editDesc').value = item.description;
        document.getElementById('editDate').value = item.news_date.split('T')[0];

        const toggle = document.getElementById('editActive');
        const lbl = document.getElementById('newsStatusLabel');
        toggle.checked = item.is_active == 1;
        lbl.innerText = toggle.checked ? 'Active' : 'Inactive';

        const filename = item.image_path.split('/').pop();
        document.getElementById('editPreview').innerHTML = `
            <img src="/${fullSlug}/${filename}?t=${Date.now()}" class="w-full h-full object-cover">
        `;

        updateCount(document.getElementById('editTitle'), 'editC1', 100);
        updateCount(document.getElementById('editDesc'), 'editCD', 500);

        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    const handleNewsSubmit = (e, url, isUpdate = false) => {
        e.preventDefault();
        const formData = new FormData(e.target);

        if (isUpdate) {
            formData.append('_method', 'PUT');
            formData.append(
                'is_active',
                document.getElementById('editActive').checked ? 1 : 0
            );
        } else {
            if (document.getElementById('addInput').files.length === 0) {
                document.getElementById('addImgError').classList.remove('hidden');
                document.getElementById('addPreview')
                    .classList.add('border-red-500', 'bg-red-50');
                return;
            }
        }

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) Turbo.visit(window.location.href);
            else alert(data.error || "Validation failed.");
        })
        .catch(err => {
            console.error(err);
            alert("A server error occurred.");
        });
    };

    if (addForm) {
        addForm.onsubmit = (e) =>
            handleNewsSubmit(e, '/admin/news-actions/store', false);
    }

    if (editForm) {
        editForm.onsubmit = (e) =>
            handleNewsSubmit(e, `/admin/news-actions/${window.currentNewsId}`, true);

        document.getElementById('editActive').onchange = function () {
            document.getElementById('newsStatusLabel').innerText =
                this.checked ? 'Active' : 'Inactive';
        };
    }

    window.deleteNews = (id) => {
        if (confirm('Delete this News item permanently?')) {
            fetch(`/admin/news-actions/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Turbo.visit(window.location.href, { action: "replace" });
                } else {
                    alert("Error: " + data.error);
                }
            })
            .catch(err => {
                console.error(err);
                alert("A system error occurred.");
            });
        }
    };
}

export function initDirectorsPage() {
    const list = document.getElementById('directors-sortable-list');
    const addForm = document.querySelector('#addModal form');
    const editForm = document.getElementById('editForm');
    if (!list && !addForm && !editForm) return;

    if (list) {
        new Sortable(list, {
            animation: 150, handle: '.drag-handle', ghostClass: 'bg-slate-50',
            onEnd: () => {
                let orders = [];
                list.querySelectorAll('.sortable-item').forEach((el, index) => orders.push({ id: el.dataset.id, order: index + 1 }));
                fetch('/admin/director-actions/update-order', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ orders })
                });
            }
        });
    }

    window.openAddModal = () => {
        const modal = document.getElementById('addModal');
        addForm.reset();
        addForm.scrollTop = 0;
        document.getElementById('addPreview').innerHTML = `<i class="fas fa-camera text-3xl text-slate-300 mb-2"></i><span class="text-slate-400 font-bold text-[9px] uppercase tracking-widest text-center px-4 opacity-60">Upload Portrait</span>`;
        document.getElementById('addImgError').classList.add('hidden');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    window.openEditModal = (item, fullSlug) => {
        window.currentDirectorId = item.id;
        const form = document.getElementById('editForm');
        form.scrollTop = 0;

        document.getElementById('editName').value = item.name;
        document.getElementById('editDesignation').value = item.designation;
        document.getElementById('editDesc').value = item.description;
        
        const toggle = document.getElementById('editActive');
        const lbl = document.getElementById('directorStatusLabel');
        toggle.checked = item.is_active == 1;
        lbl.innerText = toggle.checked ? 'Active' : 'Inactive';
        
        const filename = item.image_path.split('/').pop();
        document.getElementById('editPreview').innerHTML = `<img src="/${fullSlug}/${filename}?t=${Date.now()}" class="w-full h-full object-cover">`;

        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    const handleDirectorSubmit = (e, url, isUpdate = false) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        if (isUpdate) {
            formData.append('_method', 'PUT');
            formData.append('is_active', document.getElementById('editActive').checked ? 1 : 0);
        } else {
            if (document.getElementById('addInput').files.length === 0) {
                document.getElementById('addImgError').classList.remove('hidden');
                document.getElementById('addPreview').classList.add('border-red-500', 'bg-red-50');
                return;
            }
        }

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        }).then(res => res.json()).then(data => {
            if (data.success) Turbo.visit(window.location.href);
            else alert(data.error || "Operation failed.");
        });
    };

    if (addForm) addForm.onsubmit = (e) => handleDirectorSubmit(e, '/admin/director-actions/store', false);
    if (editForm) {
        editForm.onsubmit = (e) => handleDirectorSubmit(e, `/admin/director-actions/${window.currentDirectorId}`, true);
        document.getElementById('editActive').onchange = function() {
            document.getElementById('directorStatusLabel').innerText = this.checked ? 'Active' : 'Inactive';
        };
    }

    window.deleteDirector = (id) => {
        if (confirm('Delete this director profile permanently?')) {
            fetch(`/admin/director-actions/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }).then(() => Turbo.visit(window.location.href));
        }
    };
}