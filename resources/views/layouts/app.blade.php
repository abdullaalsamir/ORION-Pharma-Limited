<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ORION Pharma Limited</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        /* ===== Global Row (80% width) ===== */
        .row {
            width: 80%;
            margin: 0 auto;
        }

        /* ===== HEADER ===== */
        header {
            background-color: #0a3d62;
            padding: 15px 0;
        }

        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-area svg {
            width: 42px;
            height: 42px;
        }

        .logo-text {
            color: #ffffff;
            font-size: 20px;
            font-weight: 600;
        }

        nav a {
            color: #ffffff;
            text-decoration: none;
            margin-left: 25px;
            font-size: 15px;
            font-weight: 500;
            position: relative;
        }

        nav a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: #ffffff;
            left: 0;
            bottom: -5px;
            transition: width 0.3s;
        }

        nav a:hover::after {
            width: 100%;
        }

        /* ===== CONTENT ===== */
        .content {
            padding: 40px 0;
            min-height: 60vh;
        }

        /* ===== FOOTER ===== */
        footer {
            background-color: #0a3d62;
            color: #ffffff;
            margin-top: 50px;
        }

        .footer-top {
            display: flex;
            padding: 40px 0;
            gap: 40px;
        }

        .footer-contact { width: 60%; }
        .footer-links { width: 40%; }

        .footer h3 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .footer-links ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links ul li {
            margin-bottom: 8px;
        }

        .footer-links ul li a {
            color: #ffffff;
            text-decoration: none;
            font-size: 14px;
        }

        .footer-links ul li a:hover {
            text-decoration: underline;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.2);
            text-align: center;
            padding: 15px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>

@include('partials.header')

<div class="row content">
    @yield('content')
</div>

@include('partials.footer')

</body>
</html>
