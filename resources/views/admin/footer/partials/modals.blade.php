<div id="contactModal" class="modal-overlay" style="display:none">
    <div class="modal-content" style="width: 600px;">
        <button onclick="closeFooterModal('contactModal')" class="modal-close"><i class="fas fa-times"></i></button>
        <h3 style="color:#0a3d62; margin-bottom:25px; font-weight:800;">EDIT CONTACT INFO</h3>
        <form action="{{ route('admin.footer.update') }}" method="POST">
            @csrf
            <div style="margin-bottom:20px; position:relative;">
                <label class="modal-section-label">Company Name</label>
                <input type="text" name="company" value="{{ $footer->company }}" required maxlength="50"
                    oninput="updateCounter(this, 'cnt_company', 50)"
                    style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:10px; font-weight:bold;">
                <span class="char-limit-hint" id="cnt_company">0/50</span>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                @foreach(['address_1' => 'Address 1', 'address_2' => 'Address 2', 'address_3' => 'Address 3', 'phone_1' => 'Phone 1', 'phone_2' => 'Phone 2', 'fax' => 'Fax Number', 'email' => 'Email Address'] as $key => $label)
                    <div style="margin-bottom:15px; position:relative;">
                        <label class="modal-section-label">{{ $label }}</label>
                        <input type="text" name="{{ $key }}" value="{{ $footer->$key }}" required maxlength="50"
                            oninput="updateCounter(this, 'cnt_{{ $key }}', 50)"
                            style="width:100%; padding:11px; border:1px solid #e2e8f0; border-radius:10px; font-size:13px;">
                        <span class="char-limit-hint" id="cnt_{{ $key }}">0/50</span>
                    </div>
                @endforeach
            </div>
            <button type="submit"
                style="width:100%; background:#0a3d62; color:#fff; border:none; padding:15px; border-radius:12px; font-weight:bold; cursor:pointer; margin-top:10px;">UPDATE
                FOOTER CONTACT</button>
        </form>
    </div>
</div>

<div id="mapModal" class="modal-overlay" style="display:none">
    <div class="modal-content" style="width: 600px;">
        <button onclick="closeFooterModal('mapModal')" class="modal-close"><i class="fas fa-times"></i></button>
        <h3 style="color:#0a3d62; margin-bottom:20px;">LOCATION MAP CONFIG</h3>
        <form action="{{ route('admin.footer.update') }}" method="POST">
            @csrf
            <label class="modal-section-label">Google Maps iframe 'src' URL</label>
            <div style="display:flex; gap:10px; margin-bottom:15px;">
                <input type="text" name="map_url" id="map_input" value="{{ $footer->map_url }}"
                    placeholder="https://www.google.com/maps/embed?..." required
                    style="flex:1; padding:12px; border:1px solid #e2e8f0; border-radius:10px;">
                <button type="button" onclick="fetchMap()"
                    style="background:#0a3d62; color:#fff; border:none; padding:0 25px; border-radius:10px; cursor:pointer; font-weight:bold;">FETCH</button>
            </div>
            <iframe id="map_preview" src="{{ $footer->map_url }}" width="100%" height="220"
                style="border:1px solid #e2e8f0; border-radius:15px; margin-bottom:20px; background:#f8fafc;"></iframe>
            <button type="submit" id="mapSaveBtn" disabled
                style="width:100%; background:#1e7a43; color:#fff; border:none; padding:15px; border-radius:12px; font-weight:bold; opacity:0.5; cursor:not-allowed;">CONFIRM
                MAP UPDATE</button>
        </form>
    </div>
</div>

<div id="qlModal" class="modal-overlay" style="display:none">
    <div class="modal-content" style="width: 550px;">
        <button onclick="closeFooterModal('qlModal')" class="modal-close">
            <i class="fas fa-times"></i>
        </button>

        <h3 style="color:#0a3d62; margin-bottom:25px; font-weight:800;">
            QUICK LINKS SLOTS
        </h3>

        <form action="{{ route('admin.footer.update') }}" method="POST">
            @csrf

            <div id="ql-sortable">
                @for($i = 0; $i < 7; $i++)
                    @php $current = $footer->quick_links[$i] ?? null; @endphp

                    <div class="ql-row"
                        style="background:#f8fafc; border:1px solid #e2e8f0; padding:12px; border-radius:12px; display:flex; align-items:center; gap:15px; margin-bottom:10px;">

                        <i class="fas fa-bars drag-handle" style="cursor:move; color:#cbd5e0; font-size:18px;"></i>


                        <select name="ql_menu_id[]"
                            style="flex:1; padding:11px; border:1px solid #e2e8f0; border-radius:10px; font-size:13px; color:#334155; font-weight:bold; outline:none;">
                            <option value="">-- No Page Selected --</option>
                            @foreach($menus as $menu)
                                <option value="{{ $menu->id }}" {{ ($current && $current['menu_id'] == $menu->id) ? 'selected' : '' }}>{{ $menu->name }}</option>
                                @foreach($menu->children as $child)
                                    <option value="{{ $child->id }}" {{ ($current && $current['menu_id'] == $child->id) ? 'selected' : '' }} style="color:gray;">— {{ $child->name }}</option>
                                    @foreach($child->children as $sub)
                                        <option value="{{ $sub->id }}" {{ ($current && $current['menu_id'] == $sub->id) ? 'selected' : '' }} style="color:silver;">&nbsp;&nbsp;&nbsp;&nbsp;— {{ $sub->name }}</option>
                                    @endforeach
                                @endforeach
                            @endforeach

                        </select>
                    </div>
                @endfor
            </div>

            <button type="submit"
                style="width:100%; background:#0a3d62; color:#fff; border:none; padding:16px; border-radius:12px; font-weight:bold; margin-top:15px;">
                SAVE FOOTER LINKS
            </button>
        </form>
    </div>
</div>

<div id="followModal" class="modal-overlay" style="display:none">
    <div class="modal-content" style="width: 550px;">
        <button onclick="closeFooterModal('followModal')" class="modal-close"><i class="fas fa-times"></i></button>
        <h3 style="color:#0a3d62; margin-bottom:25px; font-weight:800;">FOLLOW US & BRANDING</h3>
        <form action="{{ route('admin.footer.update') }}" method="POST">
            @csrf
            <div style="margin-bottom:30px; position:relative;">
                <label class="modal-section-label">Footer Description (200 Chars)</label>
                <textarea name="follow_us_desc" required maxlength="200" oninput="updateCounter(this, 'cnt_desc', 200)"
                    style="width:100%; height:110px; padding:15px; border:1px solid #e2e8f0; border-radius:12px; resize:none; font-size:14px; font-weight:500;">{{ $footer->follow_us_desc }}</textarea>
                <span class="char-limit-hint" id="cnt_desc">0/200</span>
            </div>

            <div id="social-sortable">
                @foreach($footer->social_links ?? [] as $index => $social)
                    <div class="ql-row"
                        style="background:#fff; border:1px solid #e2e8f0; padding:12px; border-radius:12px; display:flex; align-items:center; gap:15px; margin-bottom:10px;">
                        <i class="fas fa-bars drag-handle" style="cursor:move; color:#cbd5e0;"></i>
                        <div style="width:100px; font-size:11px; font-weight:900; color:#0a3d62; text-transform:uppercase;">
                            {{ $social['platform'] }}
                        </div>
                        <input type="hidden" name="social_platform[]" value="{{ $social['platform'] }}">
                        <input type="hidden" name="social_icon[]" value="{{ $social['icon'] }}">
                        <input type="text" name="social_url[]" value="{{ $social['url'] }}" placeholder="https://..."
                            style="flex:1; padding:10px; border:1px solid #f1f5f9; border-radius:8px; font-size:13px;">
                    </div>
                @endforeach
            </div>
            <button type="submit"
                style="width:100%; background:#0a3d62; color:#fff; border:none; padding:16px; border-radius:12px; font-weight:bold; margin-top:20px;">UPDATE
                SOCIAL CHANNELS</button>
        </form>
    </div>
</div>