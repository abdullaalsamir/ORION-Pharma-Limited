@extends('admin.layouts.app')
@section('title', 'Footer Management')

@section('content')
    <style>
        .footer-admin-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            padding-bottom: 20px;
        }

        .footer-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #f1f5f9;
            position: relative;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .footer-card h4 {
            margin: 0 0 20px 0;
            color: #0a3d62;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 15px;
        }

        .info-label {
            font-size: 10px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 13px;
            color: #334155;
            font-weight: 600;
            line-height: 1.5;
            margin-bottom: 12px;
        }

        .ql-display-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 15px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 6px;
            min-height: 44px;
        }

        .ql-status-empty {
            background: #f8fafc;
            color: #cbd5e1;
            border: 1px dashed #e2e8f0;
        }

        .ql-status-active {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #d1fae5;
        }

        .ql-status-inactive {
            background: #fff1f2;
            color: #e11d48;
            border: 1px solid #ffe4e6;
        }

        .char-limit-hint {
            font-size: 10px;
            font-weight: bold;
            color: #94a3b8;
            position: absolute;
            bottom: 10px;
            right: 12px;
        }

        .modal-section-label {
            display: block;
            font-size: 11px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
    </style>

    <div class="scrollable-content menu-tree-wrapper" style="height: calc(100vh - 100px); padding: 5px;">
        <div class="footer-admin-grid">

            <div class="footer-card">
                <h4><i class="fas fa-address-book"></i> Contact Us</h4>
                <button class="icon-btn" style="position:absolute; top:20px; right:20px;"
                    onclick="openFooterModal('contactModal')"><i class="fas fa-pen-to-square"></i></button>
                <div class="info-label">Company</div>
                <div class="info-value text-orion-blue text-base">{{ $footer->company ?: '---' }}</div>
                <div class="info-label">Address</div>
                <div class="info-value">{{ $footer->address_1 }}<br>{{ $footer->address_2 }}<br>{{ $footer->address_3 }}
                </div>
                <div class="info-label">Phone Numbers</div>
                <div class="info-value">{{ $footer->phone_1 }}<br>{{ $footer->phone_2 }}</div>
                <div class="grid grid-cols-2 gap-4 mt-2">
                    <div>
                        <div class="info-label">Fax</div>
                        <div class="info-value">{{ $footer->fax ?: '---' }}</div>
                    </div>
                    <div>
                        <div class="info-label">Email</div>
                        <div class="info-value text-blue-600">{{ $footer->email ?: '---' }}</div>
                    </div>
                </div>
            </div>

            <div class="footer-card">
                <h4><i class="fas fa-map-location-dot"></i> Map Location</h4>
                <button class="icon-btn" style="position:absolute; top:20px; right:20px;"
                    onclick="openFooterModal('mapModal')"><i class="fas fa-pen-to-square"></i></button>
                <div
                    style="flex:1; border-radius: 12px; overflow: hidden; border: 1px solid #f1f5f9; min-height: 200px; background: #f8fafc;">
                    @if($footer->map_url)
                        <iframe src="{{ $footer->map_url }}" width="100%" height="100%" style="border:0;"></iframe>
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-300 italic text-sm">No Map URL
                            Configured</div>
                    @endif
                </div>
            </div>

            <div class="footer-card">
                <h4><i class="fas fa-link"></i> Quick Links Display</h4>
                <button class="icon-btn" style="position:absolute; top:20px; right:20px;"
                    onclick="openFooterModal('qlModal')"><i class="fas fa-pen-to-square"></i></button>
                <div style="display:flex; flex-direction:column;">
                    @for($i = 0; $i < 7; $i++)
                        @php 
                            $link = $footer->quick_links[$i] ?? null;

                            $m = ($link && !empty($link['menu_id']))
                                ? $menus->firstWhere('id', $link['menu_id'])
                                : null;

                            $state = $m ? 'active' : 'empty';
                        @endphp

                        <div class="ql-display-row ql-status-{{ $state }}">
                            <span>
                                {{ $m ? ($i + 1) . '. ' . $m->name : ($i + 1) . '. ' }}
                            </span>

                            @if($m)
                                <span style="font-size:9px; letter-spacing:1px;">
                                    ACTIVE
                                </span>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>

            <div class="footer-card">
                <h4><i class="fas fa-share-nodes"></i> Follow Us & Description</h4>
                <button class="icon-btn" style="position:absolute; top:20px; right:20px;"
                    onclick="openFooterModal('followModal')"><i class="fas fa-pen-to-square"></i></button>
                <div class="info-label">Footer Brand Description</div>
                <p class="info-value italic mb-6"
                    style="background:#f8fafc; padding:15px; border-radius:12px; border: 1px solid #f1f5f9;">
                    {{ $footer->follow_us_desc ?: '---' }}</p>

                <div class="space-y-3">
                    @foreach($footer->social_links ?? [] as $social)
                        <div class="flex items-center gap-4 p-2 rounded-lg border border-slate-50">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500"><i
                                    class="{{ $social['icon'] == 'fa-globe' ? 'fas' : 'fab' }} {{ $social['icon'] }}"></i></div>
                            <div style="flex:1">
                                <div class="info-label">{{ $social['platform'] }}</div>
                                <div class="info-value truncate text-blue-600 text-xs mb-0">
                                    {{ $social['url'] ?: 'No link set' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    @include('admin.footer.partials.modals')
    @include('admin.partials.css')

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        <script>
            function openFooterModal(id) {
                const m = document.getElementById(id);
                m.style.display = 'flex';
                setTimeout(() => m.classList.add('active'), 10);
            }
            function closeFooterModal(id) {
                const m = document.getElementById(id);
                m.classList.remove('active');
                setTimeout(() => m.style.display = 'none', 300);
            }

            function updateCounter(el, id, max) {
                document.getElementById(id).innerText = el.value.length + '/' + max;
            }

            function fetchMap() {
                const url = document.getElementById('map_input').value;
                if (url.includes('google.com/maps')) {
                    document.getElementById('map_preview').src = url;
                    const btn = document.getElementById('mapSaveBtn');
                    btn.disabled = false; btn.style.opacity = 1; btn.style.cursor = 'pointer';
                } else { alert('Invalid URL'); }
            }

            new Sortable(document.getElementById('ql-sortable'), {
                handle: '.drag-handle',
                animation: 150,
                filter: '.toggle-switch',
                preventOnFilter: false
            });
            new Sortable(document.getElementById('social-sortable'), { handle: '.drag-handle', animation: 150 });
        </script>
    @endpush
@endsection