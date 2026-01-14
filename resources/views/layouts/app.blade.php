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

<body class="flex flex-col min-h-screen overflow-x-hidden bg-slate-50">

    @include('partials.header')

    <main class="flex-grow w-full pt-[90px] pb-10">
        <div class="container mx-auto w-[90%] max-w-[1400px]">
            @isset($menu)
                <div class="flex flex-col">
                    <div class="w-full">
                        <h1 class="w-full">
                            <span class="px-4 {{ request()->is('/') ? 'text-4xl' : '' }}">
                                {{ request()->is('/') ? 'Welcome to Orion Pharma' : $menu->name }}
                            </span>
                        </h1>
                    </div>

                    <div class="page-content prose max-w-none text-slate-700 leading-relaxed">
                        {!! $menu->content !!}
                    </div>
                </div>
            @else
                @yield('content')
            @endisset
        </div>
    </main>

    @include('partials.footer')
</body>

</html>