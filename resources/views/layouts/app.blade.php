<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ORION Pharma Limited</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Simple CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
        }

        header,
        footer {
            background: #0a3d62;
            color: white;
            padding: 15px;
        }

        nav a {
            color: white;
            margin-right: 15px;
            text-decoration: none;
        }

        .container {
            padding: 20px;
        }
    </style>
</head>

<body>

    <header>
        <h2>ORION Pharma Limited</h2>
        <nav>
            <a href="/">Home</a>
            <a href="/about">About</a>
            <a href="/products">Products</a>
            <a href="/contact">Contact</a>
        </nav>
    </header>

    <div class="container">
        @yield('content')
    </div>

    <footer>
        <p>© {{ date('Y') }} ORION Pharma Limited</p>
    </footer>

</body>

</html>