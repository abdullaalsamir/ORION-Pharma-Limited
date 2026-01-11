@extends('admin.layouts.app')

@section('title', 'Pages')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 style="margin:0 0 4px 0;">Page Management</h3>
            <small style="color:#666">
                Edit content for menu, submenu and sub-submenu pages.
            </small>
        </div>

        <div class="card-body" style="margin-top:18px; flex: 1; overflow: hidden; display: flex; flex-direction: column;">
            <div class="menu-tree-wrapper">
                <ul class="menu-tree">
                    <li>
                        <div class="menu-card">
                            <div class="menu-left">
                                <div style="margin-left:33px">
                                    <div class="menu-title">Home</div>
                                </div>
                            </div>

                            @if(empty($homeMenu->content))
                                <div class="menu-right">
                                    <span class="content-status-badge status-empty">No Content</span>
                                </div>
                            @else
                                <div class="content-preview menu-right" title="{{ strip_tags($homeMenu->content) }}">
                                    {{ strip_tags($homeMenu->content) }}
                                </div>
                            @endif

                            <div class="menu-actions">
                                <button class="icon-btn edit-page" data-id="{{ $homeMenu->id }}" data-name="Home Page"
                                    data-content="{{ e($homeMenu->content) }}">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </div>
                        </div>
                    </li>

                    @foreach($menus as $menu)
                        @include('admin.pages.partials.page-item', ['menu' => $menu])
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div id="pageModal" class="modal-overlay">
        <div class="modal-content"
            style="width: 85%; max-width: 800px; height: 80vh; display: flex; flex-direction: column;">
            <button class="modal-close" onclick="closePageModal()">
                <i class="fas fa-times"></i>
            </button>

            <h3 id="modalTitle" style="margin:0 0 12px 0;color:#0a3d62"></h3>

            <div id="ace-editor"
                style="flex-grow: 1; width: 100%; border: 1px solid #e6e9ee; border-radius: 6px; font-size: 14px;"></div>

            <div style="display:flex;padding-top:15px;gap:8px;justify-content:flex-end;">
                <button id="savePage" type="submit"
                    style="background:#0a3d62;color:#fff;border:none;padding:9px 14px;border-radius:8px;cursor:pointer;transition:0.3s;"
                    onmouseover="this.style.background='#1e6091'" onmouseout="this.style.background='#0a3d62'"
                    onmousedown="this.style.background='#074173'" onmouseup="this.style.background='#1e6091'">
                    Update
                </button>
            </div>
        </div>
    </div>

    @include('admin.partials.menu-tree-css')

    @push('scripts')
        <script src="{{ asset('js/ace/src-min-noconflict/ace.js') }}" type="text/javascript" charset="utf-8"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {

                let currentPageId = null;

                ace.config.set('basePath', "{{ asset('js/ace/src-min-noconflict') }}");
                const editor = ace.edit("ace-editor");
                editor.setTheme("ace/theme/github_light_default");
                editor.session.setMode("ace/mode/php");
                editor.setShowPrintMargin(false);
                editor.setOptions({
                    enableBasicAutocompletion: true,
                    enableLiveAutocompletion: true,
                    useSoftTabs: true,
                    navigateWithinSoftTabs: true,
                    tabSize: 4
                });

                document.querySelectorAll('.collapse-toggle').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const target = document.querySelector(this.dataset.target);
                        if (!target) return;

                        if (target.classList.contains('expanded')) {
                            target.classList.remove('expanded');
                            this.innerHTML = '<i class="fas fa-chevron-right"></i>';
                        } else {
                            target.classList.add('expanded');
                            this.innerHTML = '<i class="fas fa-chevron-down"></i>';
                        }
                    });
                });

                document.querySelectorAll('.edit-page').forEach(btn => {
                    btn.addEventListener('click', () => {
                        currentPageId = btn.dataset.id;
                        document.getElementById('modalTitle').innerText = `Edit: ${btn.dataset.name}`;

                        const decoder = document.createElement('textarea');
                        decoder.innerHTML = btn.dataset.content || '';
                        const content = decoder.value;

                        editor.setValue(decoder.value, -1);
                        openPageModal();
                    });
                });

                const saveBtn = document.getElementById('savePage');
                if (saveBtn) {
                    saveBtn.addEventListener('click', () => {
                        const updatedContent = editor.getValue();

                        fetch(`/admin/pages/${currentPageId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                content: updatedContent
                            })
                        })
                            .then(response => {
                                if (!response.ok) throw new Error('Save failed');
                                return response.json();
                            })
                            .then(() => {
                                window.location.reload();
                            })
                            .catch(err => alert('Error: ' + err.message));
                    });
                }

                window.openPageModal = function () {
                    const modal = document.getElementById('pageModal');
                    modal.style.display = 'flex';
                    setTimeout(() => {
                        modal.classList.add('active');
                        editor.resize();
                        editor.focus();
                    }, 10);
                };

                window.closePageModal = function () {
                    const modal = document.getElementById('pageModal');
                    modal.classList.remove('active');
                    setTimeout(() => modal.style.display = 'none', 300);
                };
            });
        </script>
    @endpush
@endsection