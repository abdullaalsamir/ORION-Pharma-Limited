import Sortable from 'sortablejs';

async function handleResponse(res) {
    const contentType = res.headers.get("content-type");
    if (contentType && contentType.indexOf("application/json") !== -1) {
        const data = await res.json();
        if (res.ok) return data;
        const errorMsg = data.error || data.message || "Server validation failed.";
        alert("Error: " + errorMsg);
        throw new Error(errorMsg);
    } else {
        const text = await res.text();
        console.error("Server returned non-JSON:", text);
        alert("System Error: The server sent an invalid response.");
        throw new Error("Invalid JSON response");
    }
}

const fetchHeaders = () => ({
    'Accept': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
});

export function initGlobalHelpers() {
    window.closeModal = (id) => {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('active');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }
    };

    window.updateCount = (el, counterId, limit) => {
        if (!el) return;
        const counter = document.getElementById(counterId);
        if (counter) {
            const len = el.value.length;
            counter.innerText = `${len}/${limit}`;
            counter.classList.toggle('text-red-500', len >= limit);
            counter.classList.toggle('text-slate-300', len < limit);
        }
    };

    window.handlePreview = (input, containerId) => {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const container = document.getElementById(containerId);
                if (container) container.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    window.showInlineError = (inputId, errorId, message) => {
        const input = document.getElementById(inputId);
        const error = document.getElementById(errorId);
        if (input) input.classList.add('border-red-500', 'bg-red-50');
        if (error) { error.innerText = message; error.classList.remove('hidden'); }
    };

    window.clearInlineError = (inputId, errorId) => {
        const input = document.getElementById(inputId);
        const error = document.getElementById(errorId);
        if (input) input.classList.remove('border-red-500', 'bg-red-50');
        if (error) error.classList.add('hidden');
    };
}

export function initLayoutUI() {
    const clockEl = document.getElementById('clock');
    if (window.clockInterval) clearInterval(window.clockInterval);
    if (clockEl) {
        const updateClock = () => {
            const el = document.getElementById('clock');
            if (el) el.innerText = new Date().toLocaleTimeString('en-US', { hour:'2-digit', minute:'2-digit', second:'2-digit', hour12:true });
        };
        updateClock();
        window.clockInterval = setInterval(updateClock, 1000);
    }

    const body = document.body;
    const adminName = body.dataset.adminName;
    const greetingEl = document.getElementById('greetingText');
    if (greetingEl && adminName) {
        const hr = new Date().getHours();
        let txt = hr < 12 ? 'Good Morning' : (hr < 17 ? 'Good Afternoon' : 'Good Evening');
        greetingEl.innerHTML = `<span class="text-slate-400 font-normal">${txt}, </span><span class="text-slate-600 font-bold">${adminName}</span>`;
    }

    const nav = document.querySelector('.sidebar-nav');
    if (nav) nav.onscroll = () => sessionStorage.setItem('sidebar-scroll', nav.scrollTop);
}

export function restoreSidebarScroll() {
    const nav = document.querySelector('.sidebar-nav');
    if (nav && sessionStorage.getItem('sidebar-scroll')) nav.scrollTop = sessionStorage.getItem('sidebar-scroll');
}

export function initTreeLogic() {
    document.querySelectorAll('.collapse-toggle').forEach(btn => {
        btn.onclick = (e) => {
            e.preventDefault();
            const container = document.getElementById(btn.dataset.target);
            if (!container) return;
            if (container.classList.contains('hidden')) {
                container.classList.remove('hidden');
                requestAnimationFrame(() => container.classList.add('expanded'));
                btn.querySelector('i').style.transform = 'rotate(90deg)';
            } else {
                container.classList.remove('expanded');
                btn.querySelector('i').style.transform = 'rotate(0deg)';
                setTimeout(() => { if (!container.classList.contains('expanded')) container.classList.add('hidden'); }, 300);
            }
        };
    });
}

export function initMenuPage() {
    const rootList = document.getElementById('root-menu-list');
    const editForm = document.getElementById('editForm');
    if (!rootList || !editForm || !window.location.pathname.includes('/admin/menus')) return;

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
            editActive.parentElement.style.opacity = '1';
            lbl.className = 'ml-3 font-bold text-slate-600';
        }
    };

    document.querySelectorAll('.menu-sortable-list').forEach(list => {
        new Sortable(list, { animation: 150, handle: '.drag-handle', onEnd: () => {
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
            process(rootList, null);
            fetch('/admin/menus/update-order', { method: 'POST', headers: { ...fetchHeaders(), 'Content-Type': 'application/json' }, body: JSON.stringify({ menus }) }).then(handleResponse);
        }});
    });

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.onclick = (e) => {
            e.preventDefault();
            const d = btn.dataset;
            editForm.action = `/admin/menus/${d.id}`;
            document.getElementById('editName').value = d.name;
            editParent.value = d.parent || '';
            document.getElementById(d.multi == '1' ? 'edit-type-multi' : 'edit-type-functional').checked = true;
            
            if (d.parentActive === '0') checkParentStatus('0');
            else {
                editActive.disabled = false;
                editActive.parentElement.style.opacity = '1';
                editActive.checked = d.active === '1';
                lbl.innerText = d.active === '1' ? 'Active' : 'Inactive';
                lbl.className = 'ml-3 font-bold text-slate-600';
            }

            const modal = document.getElementById('editModal');
            modal.classList.remove('hidden');
            setTimeout(() => modal.classList.add('active'), 10);
        };
    });

    if(editParent) editParent.onchange = () => checkParentStatus(editParent.options[editParent.selectedIndex].dataset.active);
    if(editActive) editActive.onchange = () => lbl.innerText = editActive.checked ? 'Active' : 'Inactive';
}

export function initSlidersPage() {
    const list = document.getElementById('slider-list');
    const addF = document.querySelector('#addModal form');
    const editF = document.getElementById('editForm');
    if (!list || !window.location.pathname.includes('/admin/sliders')) return;

    window.openSliderAddModal = () => {
        addF.reset();
        document.getElementById('addPreview').innerHTML = `<i class="fas fa-cloud-arrow-up text-2xl text-slate-300 mb-2"></i>`;
        const modal = document.getElementById('addModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    window.openSliderEditModal = (slider) => {
        window.currentSliderId = slider.id;
        document.getElementById('editH1').value = slider.header_1;
        document.getElementById('editH2').value = slider.header_2;
        document.getElementById('editDesc').value = slider.description;
        document.getElementById('editActive').checked = slider.is_active == 1;
        document.getElementById('editPreview').innerHTML = `<img src="/storage/${slider.image_path}?t=${Date.now()}" class="w-full h-full object-cover">`;
        
        ['editH1', 'editH2', 'editDesc'].forEach(id => {
            const el = document.getElementById(id);
            const counterId = id.replace('edit', 'editC').replace('H1', '1').replace('H2', '2').replace('Desc', 'D');
            updateCount(el, counterId, el?.getAttribute('maxlength'));
        });

        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    addF.onsubmit = (e) => {
        e.preventDefault();
        fetch('/admin/sliders', { method: 'POST', body: new FormData(addF), headers: fetchHeaders() })
        .then(handleResponse).then(() => Turbo.visit(window.location.href));
    };

    editF.onsubmit = (e) => {
        e.preventDefault();
        const fd = new FormData(editF);
        fd.append('_method', 'PUT');
        fd.append('is_active', document.getElementById('editActive').checked ? 1 : 0);
        fetch(`/admin/sliders/${window.currentSliderId}`, { method: 'POST', body: fd, headers: fetchHeaders() })
        .then(handleResponse).then(() => Turbo.visit(window.location.href));
    };

    new Sortable(list, { animation: 150, handle: '.drag-handle', onEnd: () => {
        let orders = [];
        document.querySelectorAll('.sortable-item').forEach((el, index) => orders.push({ id: el.dataset.id, order: index + 1 }));
        fetch('/admin/sliders/update-order', { method: 'POST', headers: { ...fetchHeaders(), 'Content-Type': 'application/json' }, body: JSON.stringify({ orders }) }).then(handleResponse);
    }});
}

export function initBannersPage() {
    if (!document.querySelector('.leaf-menu-item') || !window.location.pathname.includes('/admin/banners')) return;

    window.loadBanners = (menuId, el) => {
        document.querySelectorAll('.leaf-menu-item').forEach(i => i.classList.remove('active', 'border-admin-blue', 'bg-slate-100'));
        el.classList.add('active', 'border-admin-blue', 'bg-slate-100');
        window.currentMenuId = menuId;
        fetch(`/admin/banners/fetch/${menuId}`, { headers: fetchHeaders() }).then(handleResponse).then(data => { document.getElementById('imageArea').innerHTML = data.html; });
    };

    window.openBannerUploadModal = () => {
        if (!window.currentMenuId) { alert("Please select a page first"); return; }
        document.getElementById('uploadForm').reset();
        
        const previewContainer = document.getElementById('uploadPreviewContainer');
        previewContainer.style.aspectRatio = '48/9';
        previewContainer.innerHTML = `<i class="fas fa-cloud-arrow-up text-2xl text-slate-300 mb-2 group-hover:text-admin-blue transition-colors"></i><span id="uploadPlaceholderText" class="text-slate-400 text-[10px] uppercase tracking-widest text-center px-2">Click to select 48:9 image</span>`;
        
        document.getElementById('ratioSlider').value = 0;
        
        const mwInput = document.getElementById('maxWidthInput');
        if(mwInput) {
            mwInput.value = 2000;
            mwInput.classList.remove('border-red-500');
            document.getElementById('maxWidthError').classList.add('hidden');
        }

        const modal = document.getElementById('uploadModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    const ratioSlider = document.getElementById('ratioSlider');
    if (ratioSlider) {
        ratioSlider.addEventListener('input', function() {
            const container = document.getElementById('uploadPreviewContainer');
            const text = document.getElementById('uploadPlaceholderText');
            const ratios = ['48/9', '23/9', '16/9'];
            const ratioText = ['48:9', '23:9', '16:9'];
            
            container.style.aspectRatio = ratios[this.value];
            if (text) text.innerText = `Click to select ${ratioText[this.value]} image`;
        });
    }

    window.openBannerEditModal = (id, name, fullSlug, isActive) => {
        window.currentBannerId = id;
        document.getElementById('editPreviewContainer').innerHTML = `<img src="/${fullSlug}/${name}?t=${Date.now()}" class="w-full h-full object-contain">`;
        document.getElementById('editActiveToggle').checked = (isActive == 1);
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    document.getElementById('uploadForm').onsubmit = function(e) {
        e.preventDefault();
        
        const mwInput = document.getElementById('maxWidthInput');
        const mwError = document.getElementById('maxWidthError');
        const mwValue = parseInt(mwInput.value, 10);

        if (isNaN(mwValue) || mwValue < 500 || mwValue > 2000) {
            mwInput.classList.add('border-red-500');
            mwError.classList.remove('hidden');
            return;
        } else {
            mwInput.classList.remove('border-red-500');
            mwError.classList.add('hidden');
        }

        fetch(`/admin/banners/upload/${window.currentMenuId}`, { method: 'POST', body: new FormData(this), headers: fetchHeaders() })
        .then(handleResponse).then(() => { closeModal('uploadModal'); loadBanners(window.currentMenuId, document.querySelector('.leaf-menu-item.active')); });
    };

    document.getElementById('editForm').onsubmit = function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        fd.append('is_active', document.getElementById('editActiveToggle').checked ? 1 : 0);
        fetch(`/admin/banners/${window.currentBannerId}`, { method: 'POST', body: fd, headers: fetchHeaders() })
        .then(handleResponse).then(() => { closeModal('editModal'); loadBanners(window.currentMenuId, document.querySelector('.leaf-menu-item.active')); });
    };

    window.deleteBannerImage = (id) => {
        if (confirm('Delete this Banner Image?')) fetch(`/admin/banners/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => loadBanners(window.currentMenuId, document.querySelector('.leaf-menu-item.active')));
    };
}

export function initProductsPage() {
    const gForm = document.getElementById('genForm');
    const pForm = document.getElementById('prodForm');
    if (!gForm || !pForm || !window.location.pathname.includes('/admin/products')) return;

    const toolbar = document.getElementById('product-editor-toolbar');
    let activeTextarea = null;

    if (toolbar) {
        document.querySelectorAll('#prodForm textarea').forEach(ta => {
            ta.addEventListener('focus', () => {
                activeTextarea = ta;
            });
        });

        const applyFormatting = (type) => {
            if (!activeTextarea) return;

            const textarea = activeTextarea;
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const selectedText = textarea.value.substring(start, end);

            const toggleTag = (text, tagName) => {
                const open = `<${tagName}>`;
                const close = `</${tagName}>`;
                return (text.startsWith(open) && text.endsWith(close))
                    ? text.slice(open.length, -close.length)
                    : `${open}${text}${close}`;
            };

            let insertText = '';

            switch (type) {
                case 'b': insertText = toggleTag(selectedText, 'b'); break;
                case 'i': insertText = toggleTag(selectedText, 'i'); break;
                case 'p': insertText = toggleTag(selectedText, 'p'); break;
                case 'h1': insertText = toggleTag(selectedText, 'h1'); break;
                case 'h2': insertText = toggleTag(selectedText, 'h2'); break;
                case 'br': insertText = `<br>\n`; break;

                case 'ul':
                case 'ol':
                    const items = selectedText
                        .split('\n')
                        .map(line => `  <li>${line}</li>`)
                        .join('\n');
                    insertText = `<${type}>\n${items}\n</${type}>`;
                    break;
            }

            textarea.setRangeText(insertText, start, end, 'end');
            textarea.focus();
        };

        toolbar.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('mousedown', e => e.preventDefault());
            btn.onclick = () => applyFormatting(btn.dataset.format);
        });
    }

    window.loadProducts = (id, el) => {
        document.querySelectorAll('.generic-list-item').forEach(item => {
            item.classList.remove('active', 'border-admin-blue', 'bg-blue-50/50', 'border-red-500', 'bg-red-50');
            if (item.classList.contains('archived-item')) item.classList.add('bg-red-50/50', 'border-red-100');
            else item.classList.add('bg-white', 'border-slate-200');
        });
        if (el.classList.contains('archived-item')) el.classList.add('active', 'border-red-500', 'bg-red-50');
        else el.classList.add('active', 'border-admin-blue', 'bg-blue-50/50');

        window.currentGenId = id;
        window.isArchivedMode = (id === 0);

        fetch(`/admin/products-actions/fetch/${id}`, {
            headers: fetchHeaders()
        })
        .then(handleResponse)
        .then(data => {
            document.getElementById('productArea').innerHTML = data.html;
        });
    };

    window.openGenericModal = () => {
        window.currentEditGenericId = null;
        gForm.reset();
        clearInlineError('genName', 'genNameError');
        document.getElementById('genTitle').innerText = "Add Generic";
        document.getElementById('genericModal').classList.remove('hidden');
        setTimeout(() => document.getElementById('genericModal').classList.add('active'), 10);
    };

    window.openEditGeneric = (g) => {
        window.currentEditGenericId = g.id;
        document.getElementById('genName').value = g.name;
        document.getElementById('genActive').checked = (g.is_active == 1);
        document.getElementById('genTitle').innerText = "Edit Generic";
        document.getElementById('genericModal').classList.remove('hidden');
        setTimeout(() => document.getElementById('genericModal').classList.add('active'), 10);
    };

    gForm.onsubmit = (e) => {
        e.preventDefault();
        const url = window.currentEditGenericId ? `/admin/products-actions/generic-update/${window.currentEditGenericId}` : `/admin/products-actions/generic-store`;
        const fd = new FormData(gForm);
        if (window.currentEditGenericId) fd.append('_method', 'PUT');
        fd.set('is_active', document.getElementById('genActive').checked ? 1 : 0);
        fetch(url, { method: 'POST', body: fd, headers: fetchHeaders() })
            .then(handleResponse).then(() => window.location.reload())
            .catch(() => showInlineError('genName', 'genNameError', "Generic name already exists."));
    };

    window.openAddProduct = () => {
        pForm.reset();
        window.currentEditProductId = null;

        document.getElementById('p_generic_id_wrapper').classList.add('hidden');

        document.getElementById('prodTitle').innerText = "Add Product";

        const btn = document.getElementById('prodSubmitBtn');
        btn.innerText = "Add Product";
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-success');

        document.getElementById('prodPreview').innerHTML =
            `<i class="fas fa-cloud-arrow-up text-2xl text-slate-300 mb-2"></i>`;

        document.getElementById('productModal').classList.remove('hidden');
        setTimeout(() => document.getElementById('productModal').classList.add('active'), 10);
    };

    window.openEditProduct = (p) => {
        window.currentEditProductId = p.id;

        clearInlineError('p_trade_name', 'prodNameError');
        document.getElementById('prodTitle').innerText = "Edit Product";

        const btn = document.getElementById('prodSubmitBtn');
        btn.innerText = "Update Product";
        btn.classList.remove('btn-success');
        btn.classList.add('btn-primary');

        const wrapper = document.getElementById('p_generic_id_wrapper');
        const select = document.getElementById('p_generic_id');

        if (window.isArchivedMode) {
            wrapper.classList.remove('hidden');
            select.disabled = false;
            select.value = p.generic_id || '';
        } else {
            wrapper.classList.add('hidden');
            select.disabled = true;
            select.value = p.generic_id || '';
        }

        const fields = [
            'trade_name','preparation','therapeutic_class','indications',
            'dosage_admin','use_children','use_pregnancy_lactation',
            'contraindications','precautions','side_effects',
            'drug_interactions','high_risk','overdosage','storage',
            'presentation','how_supplied','commercial_pack',
            'packaging','official_specification'
        ];

        fields.forEach(f => {
            const el = document.getElementById('p_' + f);
            if (el) el.value = p[f] || '';
        });

        document.getElementById('p_active').checked = (p.is_active == 1);

        document.getElementById('prodPreview').innerHTML =
            `<img src="/storage/${p.image_path}?t=${Date.now()}" class="w-full h-full object-cover">`;

        document.getElementById('productModal').classList.remove('hidden');
        setTimeout(() => document.getElementById('productModal').classList.add('active'), 10);

        document.querySelector('#prodForm button[type="submit"]').innerText = "Update Product";
    };

    pForm.onsubmit = (e) => {
        e.preventDefault();
        const fileInput = document.getElementById('prodInput');
        if (!window.currentEditProductId && fileInput.files.length === 0) { alert("Please select a product image."); return; }
        const url = window.currentEditProductId ? `/admin/products-actions/product-update/${window.currentEditProductId}` : `/admin/products-actions/product-store/${window.currentGenId}`;
        const fd = new FormData(pForm);
        if (window.currentEditProductId) fd.append('_method', 'PUT');
        fd.set('is_active', document.getElementById('p_active').checked ? 1 : 0);
        fetch(url, { method: 'POST', body: fd, headers: fetchHeaders() })
            .then(handleResponse).then(() => window.location.reload())
            .catch(() => showInlineError('p_trade_name', 'prodNameError', "Trade name already exists."));
    };

    window.deleteGeneric = (id) => { if (confirm('Delete this Generic?')) fetch(`/admin/products-actions/generic-delete/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => window.location.reload()); };
    window.deleteProduct = (id) => { if (confirm('Delete this Product?')) fetch(`/admin/products-actions/product-delete/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => window.location.reload()); };
}

function setupModule(pathPart, storeUrl, updateUrlPrefix, currentIdKey) {
    if (!window.location.pathname.includes(pathPart)) return;
    const addF = document.querySelector('#addModal form');
    const editF = document.getElementById('editForm');

    if (addF) addF.onsubmit = (e) => {
        e.preventDefault();
        fetch(storeUrl, { method: 'POST', body: new FormData(addF), headers: fetchHeaders() })
        .then(handleResponse).then(() => Turbo.visit(window.location.href));
    };

    if (editF) editF.onsubmit = (e) => {
        e.preventDefault();
        const fd = new FormData(editF);
        fd.append('_method', 'PUT');
        if(document.getElementById('editPin')) fd.append('is_pin', document.getElementById('editPin').checked ? 1 : 0);
        fd.append('is_active', document.getElementById('editActive').checked ? 1 : 0);
        fetch(`${updateUrlPrefix}/${window[currentIdKey]}`, { method: 'POST', body: fd, headers: fetchHeaders() })
        .then(handleResponse).then(() => Turbo.visit(window.location.href));
    };
}

export function initScholarshipPage() {
    setupModule('scholarship', '/admin/scholarship-actions/store', '/admin/scholarship-actions', 'curScholarId');
    
    const list = document.getElementById('scholar-sortable-list');
    if (list) {
        new Sortable(list, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'bg-slate-50',
            onEnd: () => {
                let orders = [];
                list.querySelectorAll('.sortable-item').forEach((el, index) => {
                    orders.push({ id: el.dataset.id, order: index + 1 });
                });
                fetch('/admin/scholarship-actions/update-order', {
                    method: 'POST',
                    headers: { 
                        ...fetchHeaders(), 
                        'Content-Type': 'application/json' 
                    },
                    body: JSON.stringify({ orders })
                })
                .then(handleResponse)
                .catch(err => console.error("Sort order update failed:", err));
            }
        });
    }

    window.openScholarAddModal = () => {
        document.querySelector('#addModal form').reset();
        document.getElementById('addPreview').innerHTML = `<i class="fas fa-camera"></i>`;
        const modal = document.getElementById('addModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    
    window.openScholarEditModal = (item, slug) => {
        window.curScholarId = item.id;
        document.getElementById('editName').value = item.name;
        document.getElementById('editDegree').value = item.degree;
        document.getElementById('editSession').value = item.session ? item.session.replace('Session: ', '') : '';
        document.getElementById('editCollege').value = item.medical_college;
        document.getElementById('editActive').checked = item.is_active == 1;
        document.getElementById('editPreview').innerHTML = `<img src="/${slug}/${item.image_path.split('/').pop()}?t=${Date.now()}" class="w-full h-full object-cover">`;
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    window.deleteScholarship = (id) => { if (confirm('Delete this recipient?')) { fetch(`/admin/scholarship-actions/${id}`, { method: 'DELETE', headers: fetchHeaders() }) .then(handleResponse) .then(() => Turbo.visit(window.location.href)); } };
}

export function initCSRPage() {
    setupModule('csr-list', '/admin/csr-actions/store', '/admin/csr-actions', 'curCsrId');
    window.openCsrAddModal = () => {
        const form = document.querySelector('#addModal form');
        form.reset();
        const modal = document.getElementById('addModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    window.openCsrEditModal = (item, slug) => {
        window.curCsrId = item.id;
        document.getElementById('editTitle').value = item.title;
        document.getElementById('editDate').value = item.csr_date.split('T')[0];
        document.getElementById('editDesc').value = item.description;
        document.getElementById('editActive').checked = item.is_active == 1;
        document.getElementById('editPreview').innerHTML = `<img src="/${slug}/${item.image_path.split('/').pop()}?t=${Date.now()}" class="w-full h-full object-cover">`;
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    window.deleteCsr = (id) => { if(confirm('Delete this CSR?')) fetch(`/admin/csr-actions/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => Turbo.visit(window.location.href)); };
}

export function initNewsPage() {
    setupModule('news-and-announcements', '/admin/news-actions/store', '/admin/news-actions', 'curNewsId');
    window.openNewsAddModal = () => {
        const form = document.querySelector('#addModal form');
        form.reset();

        const pin = form.querySelector('input[name="is_pin"][type="checkbox"]');
        const label = document.getElementById('addPinLabel');

        if (pin) {
            pin.checked = true;
            togglePinText(pin, 'addPinLabel');
        }

        const modal = document.getElementById('addModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    window.openNewsEditModal = (item, slug) => {
        window.curNewsId = item.id;
        document.getElementById('editTitle').value = item.title;
        document.getElementById('editDate').value = item.news_date.split('T')[0];
        document.getElementById('editDesc').value = item.description;
        document.getElementById('editActive').checked = item.is_active == 1;
        if(document.getElementById('editPin')) {
            document.getElementById('editPin').checked = item.is_pin == 1;
            togglePinText(document.getElementById('editPin'), 'editPinLabel');
        }
        const preview = document.getElementById('editPreview');
        preview.classList.remove('p-6');
        if (item.file_type === 'pdf') {
            preview.classList.add('p-6');
            preview.innerHTML = `<div class="flex flex-col items-center justify-center text-center"><i class="fas fa-file-pdf text-red-600 text-5xl mb-3"></i><span class="text-[11px] font-bold text-slate-600 uppercase">PDF Notice</span></div>`;
        } else {
            preview.innerHTML = `<img src="/${slug}/${item.file_path.split('/').pop()}?t=${Date.now()}" class="w-full h-full object-cover">`;
        }
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    window.deleteNews = (id) => { if(confirm('Delete this News?')) fetch(`/admin/news-actions/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => Turbo.visit(window.location.href)); };
}

window.handleNewsPreview = function (input, previewId, fileNameId) {
    const preview = document.getElementById(previewId);
    const fileNameEl = document.getElementById(fileNameId);
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    
    const form = input.closest('form');
    const titleInp = form.querySelector('input[name="title"]');
    if (titleInp && !titleInp.value) {
        titleInp.value = file.name.replace(/\.[^/.]+$/, "").replace(/[-_]/g, ' ');
        const counterId = titleInp.getAttribute('oninput')?.match(/'([^']+)'/)?.[1];
        if(counterId) updateCount(titleInp, counterId, titleInp.getAttribute('maxlength'));
    }

    if (fileNameEl) { fileNameEl.textContent = file.name; fileNameEl.classList.remove('hidden'); }
    preview.innerHTML = ''; preview.classList.remove('p-6');
    if (file.type === 'application/pdf') {
        preview.classList.add('p-6');
        preview.innerHTML = `<div class="flex flex-col items-center justify-center text-center"><i class="fas fa-file-pdf text-red-600 text-5xl mb-3"></i><span class="text-[11px] font-bold text-slate-600 uppercase font-sans">PDF Notice</span></div>`;
    } else {
        const reader = new FileReader();
        reader.onload = (e) => preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
        reader.readAsDataURL(file);
    }
};

window.togglePinText = (el, labelId) => {
    const label = document.getElementById(labelId);
    if (!label) return;
    label.innerText = el.checked ? "Pin Yes" : "Pin No";
    label.classList.toggle('text-admin-blue', el.checked);
};

export function initDirectorsPage() {
    setupModule('board-of-directors', '/admin/director-actions/store', '/admin/director-actions', 'curDirId');
    window.openDirectorAddModal = () => {
        document.querySelector('#addModal form').reset();
        const modal = document.getElementById('addModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    window.openDirectorEditModal = (item, slug) => {
        window.curDirId = item.id;
        document.getElementById('editName').value = item.name;
        document.getElementById('editDesignation').value = item.designation;
        document.getElementById('editActive').checked = item.is_active == 1;
        document.getElementById('editPreview').innerHTML = `<img src="/${slug}/${item.image_path.split('/').pop()}?t=${Date.now()}" class="w-full h-full object-cover">`;
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    window.deleteDirector = (id) => { if(confirm('Delete this Profile?')) fetch(`/admin/director-actions/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => Turbo.visit(window.location.href)); };
}

export function initMedicalJournalsPage() {
    setupModule('medical-journals', '/admin/journal-actions/store', '/admin/journal-actions', 'curJId');
    document.querySelectorAll('.journal-sortable-list').forEach(list => {
        new Sortable(list, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'bg-slate-50',
            onEnd: () => {
                let orders = [];
                list.querySelectorAll('.sortable-item').forEach((el, index) => {
                    orders.push({ id: el.dataset.id, order: index + 1 });
                });
                fetch('/admin/journal-actions/update-order', {
                    method: 'POST',
                    headers: { 
                        ...fetchHeaders(), 
                        'Content-Type': 'application/json' 
                    },
                    body: JSON.stringify({ orders })
                })
                .then(handleResponse)
                .catch(err => console.error("Sort order update failed:", err));
            }
        });
    });
    window.handlePdfSelect = (input, isEdit = false) => {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const cleanName = file.name.replace(/\.[^/.]+$/, "").replace(/_/g, ' ');
            if (isEdit) {
                const statusEl = document.getElementById('editPdfStatus');
                if (statusEl) {
                    statusEl.innerText = file.name;
                    statusEl.classList.add('text-admin-blue');
                }
            } else {
                const statusEl = document.getElementById('pdfStatusText');
                const titleInput = document.getElementById('titleInput');
                if (statusEl) statusEl.innerText = file.name;
                if (titleInput && !titleInput.value) titleInput.value = cleanName;
            }
        }
    };
    window.openJournalAddModal = () => {
        document.querySelector('#addModal form').reset();
        document.getElementById('pdfStatusText').innerText = "Click to select PDF";
        const modal = document.getElementById('addModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    window.openJournalEditModal = (item) => {
        window.curJId = item.id;
        document.getElementById('editTitle').value = item.title;
        document.getElementById('editYear').value = item.year;
        document.getElementById('editActive').checked = item.is_active == 1;
        
        const editStatus = document.getElementById('editPdfStatus');
        if (editStatus) {
            editStatus.innerText = "Click to replace PDF";
            editStatus.classList.remove('text-admin-blue');
        }

        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    window.deleteJournal = (id) => { if(confirm('Delete this Medical Journal?')) fetch(`/admin/journal-actions/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => Turbo.visit(window.location.href)); };
}

export function initReportModule() {
    const map = { 'annual-reports': '/admin/annual-reports-actions', 'quarterly-reports': '/admin/quarterly-reports-actions', 'half-yearly-reports': '/admin/half-yearly-reports-actions', 'price-sensitive-information': '/admin/price-sensitive-information-actions', 'corporate-governance': '/admin/corporate-governance-actions' };
    const seg = window.location.pathname.split('/')[2];
    const base = map[seg];
    if (!base) return;
    setupModule(seg, `${base}/store`, base, 'curRepId');
    window.openReportAddModal = () => {
        document.querySelector('#addModal form').reset();
        updateCount(document.getElementById('addDesc'), 'addCD', 500);
        const modal = document.getElementById('addModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    window.openReportEditModal = (item) => {
        window.curRepId = item.id;
        document.getElementById('editTitle').value = item.title;
        document.getElementById('editDate').value = item.publication_date ? item.publication_date.split('T')[0] : '';
        document.getElementById('editDesc').value = item.description || '';
        document.getElementById('editActive').checked = item.is_active == 1;
        updateCount(document.getElementById('editDesc'), 'editCD', 500);
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    window.deleteReportItem = (id) => { if(confirm('Delete this Report?')) fetch(`${base}/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => Turbo.visit(window.location.href)); };
}

export function initPagesPage() {
    const modal = document.getElementById('pageModal');
    if (!modal || !window.location.pathname.includes('/admin/pages')) return;
    if (typeof ace !== 'undefined') {
        const editor = ace.edit("ace-editor");
        const applyFormatting = (type) => {
            const selectedText = editor.getSelectedText();
            const toggleTag = (text, tagName) => {
                const start = `<${tagName}>`, end = `</${tagName}>`;
                return (text.startsWith(start) && text.endsWith(end)) ? text.substring(start.length, text.length - end.length) : `${start}${text}${end}`;
            };
            switch (type) {
                case 'b': editor.insert(toggleTag(selectedText, 'b')); break;
                case 'i': editor.insert(toggleTag(selectedText, 'i')); break;
                case 'p': editor.insert(toggleTag(selectedText, 'p')); break;
                case 'h1': editor.insert(toggleTag(selectedText, 'h1')); break;
                case 'h2': editor.insert(toggleTag(selectedText, 'h2')); break;
                case 'br': editor.insert(`<br>\n`); break;
                case 'ul':
                case 'ol':
                    const items = selectedText.split('\n').map(line => `  <li>${line}</li>`).join('\n');
                    editor.insert(`<${type}>\n${items}\n</${type}>`);
                    break;
            }
            editor.focus();
        };
        document.querySelectorAll('#editor-toolbar button').forEach(btn => btn.onclick = () => applyFormatting(btn.dataset.format));
        let curPageId = null;
        document.querySelectorAll('.edit-page').forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                curPageId = btn.dataset.id;
                document.getElementById('modalTitle').innerText = `Edit: ${btn.dataset.name}`;
                
                fetch(`/admin/banners/get-for-editor/${curPageId}`, { headers: fetchHeaders() })
                .then(handleResponse)
                .then(images => {
                    const strip = document.getElementById('imageStrip');
                    strip.innerHTML = images.length ? '' : 'No Banners';
                    images.forEach(img => {
                        const div = document.createElement('div');
                        div.className = "shrink-0 w-full h-auto rounded-xl overflow-hidden border-1 border-transparent hover:border-admin-blue cursor-pointer transition-all duration-300";
                        div.innerHTML = `<img src="${img.url}" class="w-full h-full object-cover">`;
                        div.onclick = () => { 
                            const widthAttr = img.width ? ` width="${img.width}"` : '';
                            const heightAttr = img.height ? ` height="${img.height}"` : '';
                            
                            const aspectStyle = (img.width && img.height) ? ` style="aspect-ratio: ${img.width} / ${img.height};"` : '';
                            
                            const htmlString = `<div class="banner rounded-xl shimmer">\n  <img src="${img.url}"${widthAttr}${heightAttr}${aspectStyle} alt="${btn.dataset.name}" class="w-full h-auto block rounded-xl object-cover" onload="this.parentElement.classList.remove('shimmer')">\n</div>\n`;
                            
                            editor.insert(htmlString); 
                            editor.focus(); 
                        };
                        strip.appendChild(div);
                    });
                });
                const dec = document.createElement('textarea'); dec.innerHTML = btn.dataset.content || '';
                editor.setValue(dec.value, -1);
                modal.classList.remove('hidden');
                setTimeout(() => { modal.classList.add('active'); editor.resize(); }, 10);
            };
        });
        const save = document.getElementById('savePage');
        if (save) save.onclick = () => {
            fetch(`/admin/pages/${curPageId}`, { method: 'PUT', headers: { ...fetchHeaders(), 'Content-Type': 'application/json' }, body: JSON.stringify({ content: editor.getValue() }) })
            .then(handleResponse).then(() => Turbo.visit(window.location.href));
        };
    }
}

export function initComplaintsPage() {
    if (!window.location.pathname.includes('product-complaint')) return;
    window.deleteComplaint = (id) => { if(confirm('Delete this Complaint?')) fetch(`/admin/product-complaint-actions/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => Turbo.visit(window.location.href, { action: "replace" })); };
}

export function initFooterPage() {
    if (!window.location.pathname.includes('/admin/footer')) return;
    window.openFooterModal = (id) => {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
        modal.querySelectorAll('input[maxlength], textarea[maxlength]').forEach(el => {
            const match = el.getAttribute('oninput')?.match(/'([^']+)'/);
            if (match) updateCount(el, match[1], el.getAttribute('maxlength'));
        });
    };
    window.fetchFooterMap = () => {
        const url = document.getElementById('map_input').value;
        if (url.includes('google.com/maps')) { document.getElementById('map_preview').src = url; document.getElementById('mapSaveBtn').disabled = false; }
    };
    document.querySelectorAll('.modal-overlay form').forEach(form => {
        form.onsubmit = (e) => {
            e.preventDefault();
            fetch(form.action, { method: 'POST', body: new FormData(form), headers: fetchHeaders() }).then(handleResponse).then(() => Turbo.visit(window.location.href));
        };
    });
}