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
        alert("System Error: The server sent an invalid response. Please refresh and try again.");
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
        const counter = document.getElementById(counterId);
        if (counter) {
            const len = el.value.length;
            counter.innerText = `${len}/${limit}`;
            counter.classList.toggle('text-red-500', len >= limit);
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
            document.getElementById('editParent').value = d.parent || '';
            document.getElementById(d.multi == '1' ? 'edit-type-multi' : 'edit-type-functional').checked = true;
            document.getElementById('editActive').checked = d.active === '1';
            document.getElementById('toggleLabel').innerText = d.active === '1' ? 'Active' : 'Inactive';
            const modal = document.getElementById('editModal');
            modal.classList.remove('hidden');
            setTimeout(() => modal.classList.add('active'), 10);
        };
    });
}

export function initSlidersPage() {
    const list = document.getElementById('slider-list');
    const addF = document.querySelector('#addModal form');
    const editF = document.getElementById('editForm');
    if (!list || !window.location.pathname.includes('/admin/sliders')) return;

    window.openSliderAddModal = () => {
        addF.reset();
        document.getElementById('addPreview').innerHTML = `<i class="fas fa-cloud-arrow-up"></i>`;
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
        document.getElementById('editPreview').innerHTML = `<img src="/storage/${slider.image_path}" class="w-full h-full object-cover">`;
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
        const modal = document.getElementById('uploadModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    window.openBannerEditModal = (id, name, fullSlug, isActive) => {
        window.currentBannerId = id;
        document.getElementById('editPreviewContainer').innerHTML = `<img src="/${fullSlug}/${name}" class="w-full h-full object-cover">`;
        document.getElementById('editActiveToggle').checked = (isActive == 1);
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    document.getElementById('uploadForm').onsubmit = function(e) {
        e.preventDefault();
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
        if (confirm('Delete banner?')) fetch(`/admin/banners/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => loadBanners(window.currentMenuId, document.querySelector('.leaf-menu-item.active')));
    };
}

export function initProductsPage() {
    const gForm = document.getElementById('genForm');
    const pForm = document.getElementById('prodForm');
    if (!gForm || !pForm || !window.location.pathname.includes('/admin/products')) return;

    window.loadProducts = (id, el) => {
        document.querySelectorAll('.generic-list-item').forEach(i => i.classList.remove('active', 'border-admin-blue', 'bg-blue-50/50'));
        el.classList.add('active', 'border-admin-blue', 'bg-blue-50/50');
        window.currentGenId = id;
        fetch(`/admin/products-actions/fetch/${id}`, { headers: fetchHeaders() }).then(handleResponse).then(data => { document.getElementById('productArea').innerHTML = data.html; });
    };

    window.openGenericModal = () => {
        window.currentEditGenericId = null;
        gForm.reset();
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
        fetch(url, { method: 'POST', body: fd, headers: fetchHeaders() }).then(handleResponse).then(() => window.location.reload());
    };

    window.openAddProduct = () => {
        pForm.reset();
        window.currentEditProductId = null;
        document.getElementById('prodTitle').innerText = "Add Product";
        document.getElementById('productModal').classList.remove('hidden');
        setTimeout(() => document.getElementById('productModal').classList.add('active'), 10);
    };

    window.openEditProduct = (p) => {
        window.currentEditProductId = p.id;
        const fields = ['trade_name', 'preparation', 'therapeutic_class', 'indications', 'dosage_admin', 'use_children', 'use_pregnancy_lactation', 'contraindications', 'precautions', 'side_effects', 'drug_interactions', 'high_risk', 'overdosage', 'storage', 'presentation', 'how_supplied', 'commercial_pack', 'packaging', 'official_specification'];
        fields.forEach(f => { if(document.getElementById('p_' + f)) document.getElementById('p_' + f).value = p[f] || ''; });
        document.getElementById('p_active').checked = (p.is_active == 1);
        document.getElementById('prodPreview').innerHTML = `<img src="/storage/${p.image_path}" class="w-full h-full object-cover">`;
        document.getElementById('productModal').classList.remove('hidden');
        setTimeout(() => document.getElementById('productModal').classList.add('active'), 10);
    };

    pForm.onsubmit = (e) => {
        e.preventDefault();
        const url = window.currentEditProductId ? `/admin/products-actions/product-update/${window.currentEditProductId}` : `/admin/products-actions/product-store/${window.currentGenId}`;
        const fd = new FormData(pForm);
        if (window.currentEditProductId) fd.append('_method', 'PUT');
        fd.set('is_active', document.getElementById('p_active').checked ? 1 : 0);
        fetch(url, { method: 'POST', body: fd, headers: fetchHeaders() }).then(handleResponse).then(() => window.location.reload());
    };

    window.deleteGeneric = (id) => { if (confirm('Delete Generic?')) fetch(`/admin/products-actions/generic-delete/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => window.location.reload()); };
    window.deleteProduct = (id) => { if (confirm('Delete Product?')) fetch(`/admin/products-actions/product-delete/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => window.location.reload()); };
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
        fd.append('is_active', document.getElementById('editActive').checked ? 1 : 0);
        fetch(`${updateUrlPrefix}/${window[currentIdKey]}`, { method: 'POST', body: fd, headers: fetchHeaders() })
        .then(handleResponse).then(() => Turbo.visit(window.location.href));
    };
}

export function initScholarshipPage() {
    setupModule('scholarship', '/admin/scholarship-actions/store', '/admin/scholarship-actions', 'curScholarId');
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
        document.getElementById('editCollege').value = item.medical_college;
        document.getElementById('editActive').checked = item.is_active == 1;
        document.getElementById('editPreview').innerHTML = `<img src="/${slug}/${item.image_path.split('/').pop()}" class="w-full h-full object-cover">`;
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
}

export function initCSRPage() {
    setupModule('csr-list', '/admin/csr-actions/store', '/admin/csr-actions', 'curCsrId');
    window.openCsrAddModal = () => {
        document.querySelector('#addModal form').reset();
        const modal = document.getElementById('addModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    window.openCsrEditModal = (item, slug) => {
        window.curCsrId = item.id;
        document.getElementById('editTitle').value = item.title;
        document.getElementById('editDesc').value = item.description;
        document.getElementById('editDate').value = item.csr_date.split('T')[0];
        document.getElementById('editActive').checked = item.is_active == 1;
        document.getElementById('editPreview').innerHTML = `<img src="/${slug}/${item.image_path.split('/').pop()}" class="w-full h-full object-cover">`;
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    window.deleteCsr = (id) => { if(confirm('Delete?')) fetch(`/admin/csr-actions/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => Turbo.visit(window.location.href)); };
}

export function initNewsPage() {
    setupModule('news-and-announcements', '/admin/news-actions/store', '/admin/news-actions', 'curNewsId');
    window.openNewsAddModal = () => {
        document.querySelector('#addModal form').reset();
        const modal = document.getElementById('addModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    window.openNewsEditModal = (item, slug) => {
        window.curNewsId = item.id;
        document.getElementById('editTitle').value = item.title;
        document.getElementById('editDate').value = item.news_date.split('T')[0];
        document.getElementById('editActive').checked = item.is_active == 1;
        document.getElementById('editPreview').innerHTML = `<img src="/${slug}/${item.image_path.split('/').pop()}" class="w-full h-full object-cover">`;
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    window.deleteNews = (id) => { if(confirm('Delete?')) fetch(`/admin/news-actions/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => Turbo.visit(window.location.href)); };
}

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
        document.getElementById('editPreview').innerHTML = `<img src="/${slug}/${item.image_path.split('/').pop()}" class="w-full h-full object-cover">`;
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    window.deleteDirector = (id) => { if(confirm('Delete?')) fetch(`/admin/director-actions/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => Turbo.visit(window.location.href)); };
}

export function initJournalsPage() {
    setupModule('medical-journals', '/admin/journal-actions/store', '/admin/journal-actions', 'curJId');
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
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    window.deleteJournal = (id) => { if(confirm('Delete?')) fetch(`/admin/journal-actions/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => Turbo.visit(window.location.href)); };
}

export function initReportModule() {
    const map = { 'annual-reports': '/admin/annual-reports-actions', 'quarterly-reports': '/admin/quarterly-reports-actions', 'half-yearly-reports': '/admin/half-yearly-reports-actions', 'price-sensitive-information': '/admin/price-sensitive-information-actions', 'corporate-governance': '/admin/corporate-governance-actions' };
    const seg = window.location.pathname.split('/')[2];
    const base = map[seg];
    if (!base) return;

    setupModule(seg, `${base}/store`, base, 'curRepId');
    window.openReportAddModal = () => {
        document.querySelector('#addModal form').reset();
        const modal = document.getElementById('addModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    window.openReportEditModal = (item) => {
        window.curRepId = item.id;
        document.getElementById('editTitle').value = item.title;
        document.getElementById('editDate').value = item.publication_date ? item.publication_date.split('T')[0] : '';
        document.getElementById('editActive').checked = item.is_active == 1;
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    window.deleteReportItem = (id) => { if(confirm('Delete?')) fetch(`${base}/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => Turbo.visit(window.location.href)); };
}

export function initPagesPage() {
    const modal = document.getElementById('pageModal');
    if (!modal || !window.location.pathname.includes('/admin/pages')) return;

    if (typeof ace !== 'undefined') {
        const editor = ace.edit("ace-editor");
        let curPageId = null;
        document.querySelectorAll('.edit-page').forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                curPageId = btn.dataset.id;
                document.getElementById('modalTitle').innerText = `Edit: ${btn.dataset.name}`;
                fetch(`/admin/banners/get-for-editor/${curPageId}`, { headers: fetchHeaders() }).then(handleResponse).then(images => {
                    const strip = document.getElementById('imageStrip');
                    strip.innerHTML = images.length ? '' : 'No Banners';
                    images.forEach(img => {
                        const div = document.createElement('div');
                        div.className = "shrink-0 w-full h-20 rounded-xl overflow-hidden border-2 border-transparent hover:border-admin-blue cursor-pointer transition-all bg-white shadow-sm";
                        div.innerHTML = `<img src="${img.url}" class="w-full h-full object-cover">`;
                        div.onclick = () => { editor.insert(`<div class="img-shine">\n  <img src="${img.url}" alt="${btn.dataset.name}">\n</div>\n`); editor.focus(); };
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
    window.deleteComplaint = (id) => { if(confirm('Delete?')) fetch(`/admin/product-complaint-actions/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => Turbo.visit(window.location.href, { action: "replace" })); };
}

export function initFooterPage() {
    if (!window.location.pathname.includes('/admin/footer')) return;
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