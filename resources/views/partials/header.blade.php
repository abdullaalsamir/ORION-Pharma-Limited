<!-- ===== HEADER ===== -->
<header>
    <div class="row header-inner">
        <div class="logo-area">
            <!-- SVG LOGO FILE (Clickable) -->
            <a href="{{ url('/') }}">
                <img src="{{ asset('images/logo.svg') }}" alt="ORION Pharma Logo" style="height:70px;">
            </a>
        </div>

        <!-- MENU (STATIC NOW, DYNAMIC LATER) -->
        <nav>
            <a href="/">Home</a>

            @foreach($menus as $menu)
                <div class="dropdown">
                    <a href="#">{{ $menu->name }}</a>

                    @if($menu->children->count())
                        <div class="dropdown-menu">
                            @foreach($menu->children as $submenu)
                                <div class="submenu">
                                    <a href="#">{{ $submenu->name }}</a>

                                    @if($submenu->children->count())
                                        <div class="submenu-menu">
                                            @foreach($submenu->children as $subsubmenu)
                                                <a href="#">{{ $subsubmenu->name }}</a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </nav>

    </div>
</header>