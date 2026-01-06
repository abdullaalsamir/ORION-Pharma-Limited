<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
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
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 220px;
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
            margin-left: 220px;
            background: #ffffff;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            position: fixed;
            width: calc(100% - 220px);
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
            margin-left: 220px;
            margin-top: 60px;
            padding: 25px;
        }

        .card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
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
                <a href="{{ route('admin.products') }}" class="{{ request()->is('admin/products*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
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
                <button type="submit"
                    style="background:none;border:none;cursor:pointer;color:#ff0000;font-size:18px;"
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
            const time = now.toLocaleTimeString();
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

</body>
</html>
