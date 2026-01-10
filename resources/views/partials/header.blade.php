<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<header class="main-header">
    <div class="row header-inner">

        <div class="logo-area">
            <a href="{{ url('/') }}">
                <img src="{{ asset('images/logo.svg') }}" alt="ORION Pharma Limited Logo">
            </a>
        </div>

        <nav class="nav-container">
            <ul class="nav-menu">
                @foreach($menus as $menu)
                    @if($menu->isEffectivelyActive())
                        @php $hasChildren = $menu->children->count() > 0; @endphp

                        <li class="nav-item {{ $hasChildren ? 'has-dropdown' : '' }}">
                            <a
                                href="{{ !$hasChildren ? url($menu->full_slug) : 'javascript:void(0)' }}"
                                class="nav-link"
                            >
                                {{ $menu->name }}
                                @if($hasChildren)
                                    <i class="fas fa-chevron-down icon-sm"></i>
                                @endif
                            </a>

                            @if($hasChildren)
                                <ul class="dropdown-menu">
                                    @foreach($menu->children as $submenu)
                                        @if($submenu->isEffectivelyActive())
                                            @php $hasSub = $submenu->children->count() > 0; @endphp

                                            <li class="dropdown-item {{ $hasSub ? 'has-submenu' : '' }}">
                                                <a
                                                    href="{{ !$hasSub ? url($submenu->full_slug) : 'javascript:void(0)' }}"
                                                    class="dropdown-link"
                                                >
                                                    {{ $submenu->name }}
                                                    @if($hasSub)
                                                        <i class="fas fa-chevron-right icon-sm"></i>
                                                    @endif
                                                </a>

                                                @if($hasSub)
                                                    <ul class="sub-submenu">
                                                        @foreach($submenu->children as $subsubmenu)
                                                            @if($subsubmenu->isEffectivelyActive())
                                                                <li>
                                                                    <a
                                                                        href="{{ url($subsubmenu->full_slug) }}"
                                                                        class="dropdown-link"
                                                                    >
                                                                        {{ $subsubmenu->name }}
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endif
                @endforeach
            </ul>
        </nav>

    </div>
</header>

<style>
    .main-header {
        height: 90px;
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        position: sticky;
        top: 0;
        z-index: 1000;
        display: flex;
        align-items: center;
    }

    .header-inner {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        height: 100%;
    }

    .logo-area img {
        height: 70px;
        display: block;
    }

    .nav-container {
        margin-left: 20px;
        height: 100%;
    }

    .nav-menu {
        display: flex;
        align-items: center;
        list-style: none;
        margin: 0;
        padding: 0;
        height: 100%;
    }

    .nav-item {
        position: relative;
        height: 100%;
        display: flex;
        align-items: center;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 0 10px;
        height: 100%;
        text-decoration: none;
        color: #08519e;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .nav-item:hover>.nav-link {
        background-color: #08519e;
        color: #ffffff !important;
    }

    .icon-sm {
        font-size: 11px;
    }

    .dropdown-menu,
    .sub-submenu {
        position: absolute;
        background-color: #08519e;
        list-style: none;
        margin: 0;
        padding: 0;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        min-width: 220px;
        z-index: 10;
    }

    .dropdown-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px;
        text-decoration: none;
        color: #ffffff;
        font-size: 14px;
        font-weight: 400;
        transition: all 0.2s ease;
    }

    .dropdown-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        padding-left: 25px;
    }

    .dropdown-menu li:not(:last-child),
    .sub-submenu li:not(:last-child) {
        border-bottom: 1px solid #2163a8;
    }

    .dropdown-menu {
        top: 90px;
        left: 0;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-30px);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .nav-item:hover>.dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-item {
        position: relative;
    }

    .sub-submenu {
        top: 0;
        left: 100%;
        opacity: 0;
        visibility: hidden;
        transform: translateX(-30px);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .has-submenu:hover>.sub-submenu {
        opacity: 1;
        visibility: visible;
        transform: translateX(0);
    }

    .dropdown-link .icon-sm {
        color: #ffffff;
        opacity: 0.8;
    }
</style>