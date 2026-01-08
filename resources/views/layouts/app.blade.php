<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ORION Pharma Limited</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            overflow-x: hidden;
        }

        .row {
            width: 90%;
            margin: 0 auto;
        }

        .content {
            padding: 40px 0;
            min-height: calc(100vh - 80px);
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