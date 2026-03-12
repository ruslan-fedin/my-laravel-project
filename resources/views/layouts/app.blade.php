<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Учет времени</title>

    <script src="{{ asset('vendor/tailwind.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fonts/inter/inter.css') }}">
    <script src="{{ asset('vendor/xlsx.bundle.js') }}"></script>

    <style>
        /* Адаптивные отступы: мобильные — 1rem, ПК — 120px */
        .indent-area {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        @media (min-width: 1024px) {
            .indent-area {
                padding-left: 120px !important;
                padding-right: 120px !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans antialiased text-slate-900">
    <div class="min-h-screen flex flex-col">

        <header class="sticky top-0 z-50 bg-white shadow-sm">
            @include('layouts.navigation')
        </header>

        <main class="indent-area py-6 md:py-12 flex-grow">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>

        <footer class="mt-12">
            @include('layouts.footer')
        </footer>

    </div>
</body>
</html>
