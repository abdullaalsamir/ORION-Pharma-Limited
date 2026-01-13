<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ORION Pharma Limited</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex flex-col min-h-full overflow-x-hidden">

    @include('partials.header')

    <main class="flex-grow w-full">
        <div class="container-custom py-10">
            @yield('content')
        </div>
    </main>

    @include('partials.footer')

</body>

</html>