@php
    $allMenus = \App\Models\Menu::where('is_active', 1)
        ->orderBy('order')
        ->get();

    if (!function_exists('sortMenusByTree')) {
        function sortMenusByTree($menus, $parentId = null)
        {
            $branch = collect();
            foreach ($menus->where('parent_id', $parentId) as $menu) {
                $branch->push($menu);
                $children = sortMenusByTree($menus, $menu->id);
                $branch = $branch->merge($children);
            }
            return $branch;
        }
    }

    $multifunctionalMenus = sortMenusByTree($allMenus)->filter(function ($menu) {
        return $menu->is_multifunctional == 1 && $menu->isEffectivelyActive();
    });
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: #f4f6f8;
            color: #333;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            height: 100%;
            background: #0a3d62;
            color: #fff;
            display: flex;
            flex-direction: column;
        }

        .sidebar h2 {
            text-align: center;
            padding: 20px 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 18px;
        }

        .sidebar ul {
            list-style: none;
            flex: 1;
            padding-top: 20px;
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-gutter: stable;
            -ms-overflow-style: none;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.1) transparent;
            transition: all 0.2s ease-in-out;
        }

        .sidebar ul::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar ul::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar ul::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            transition: background 0.2s ease;
        }

        .sidebar ul:hover::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
        }

        .sidebar ul::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.4) !important;
        }

        .sidebar ul li {
            margin-bottom: 10px;
        }

        .sidebar ul li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: #fff;
            text-decoration: none;
            font-size: 15px;
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }

        .sidebar ul li a i {
            width: 18px;
            text-align: center;
            font-size: 16px;
        }

        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: #07426e;
            border-left: 4px solid #fbc531;
        }

        .topbar {
            margin-left: 260px;
            background: #ffffff;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 25px;
            position: fixed;
            width: calc(100% - 260px);
            top: 0;
            z-index: 10;
        }

        .topbar .left {
            font-size: 18px;
            font-weight: 600;
        }

        .topbar .center {
            font-size: 18px;
            font-weight: 600;
            color: #555;
            font-family: monospace;
        }

        .topbar .right button:hover i {
            color: #dd0000;
        }

        .main-content {
            margin-left: 260px;
            margin-top: 65px;
            padding: 20px 25px;
            height: calc(100vh - 65px);
            display: flex;
            flex-direction: column;
        }

        .card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            max-height: 100%;
            display: flex;
            flex-direction: column;
        }

        .scrollable-content {
            overflow-y: auto;
            flex: 1;
        }

        @media(max-width: 768px) {
            .sidebar {
                width: 60px;
            }

            .sidebar h2 {
                display: none;
            }

            .sidebar ul li a span {
                display: none;
            }

            .topbar {
                margin-left: 60px;
                width: calc(100% - 60px);
            }

            .main-content {
                margin-left: 60px;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->is('admin') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.menus') }}" class="{{ request()->is('admin/menus*') ? 'active' : '' }}">
                    <i class="fas fa-bars"></i>
                    <span>Menus</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.pages') }}" class="{{ request()->is('admin/pages*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    <span>Pages</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.banners') }}" class="{{ request()->is('admin/banners*') ? 'active' : '' }}">
                    <i class="fas fa-images"></i>
                    <span>Banners</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.sliders.index') }}"
                    class="{{ request()->is('admin/sliders*') ? 'active' : '' }}">
                    <i class="fas fa-film"></i>
                    <span>Swiper Slider</span>
                </a>
            </li>
            @foreach($multifunctionalMenus as $mMenu)
                <li>
                    <a href="{{ url('admin/' . $mMenu->slug) }}"
                        class="{{ request()->is('admin/' . $mMenu->slug . '*') ? 'active' : '' }}">
                        <i class="fas fa-th-large"></i>
                        <span>{{ $mMenu->name }}</span>
                    </a>
                </li>
            @endforeach
            <li>
                <a href="{{ route('admin.footer') }}" class="{{ request()->is('admin/footer*') ? 'active' : '' }}">
                    <i class="fas fa-socks"></i>
                    <span>Footer</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.settings') }}" class="{{ request()->is('admin/settings*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="topbar">
        <div class="left" id="greetingText">
            <span class="greeting-text" id="greetingPart"></span>
            <span class="admin-name" id="adminNamePart"></span>
        </div>

        <div class="center" id="clock">
            00:00:00
        </div>

        <div class="right">
            <form action="{{ route('admin.logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" style="background:none;border:none;cursor:pointer;color:red;font-size:18px;"
                    title="Logout">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="main-content">
        @yield('content')
    </div>
    <script>
        function updateClock() {
            const now = new Date();
            const time = now.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            });
            document.getElementById('clock').innerText = time;
        }

        function setGreeting() {
            const hour = new Date().getHours();
            let greeting = 'Hello';

            if (hour < 12) greeting = 'Good Morning';
            else if (hour < 18) greeting = 'Good Afternoon';
            else greeting = 'Good Evening';

            const adminName = "{{ auth('admin')->user()->name }}";

            document.getElementById('greetingText').innerHTML =
                `<span style="color: #999">${greeting}, </span><span style="color: #07426e">${adminName}</span>`;
        }

        setGreeting();
        updateClock();
        setInterval(updateClock, 1000);
    </script>
    @stack('scripts')
</body>

</html>