<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Учет времени</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Адаптивные отступы согласно вашему требованию */
        .indent-area {
            /* На мобильных — минимальные отступы (16px) */
            padding-left: 1rem;
            padding-right: 1rem;
        }

        /* На устройствах шире 1024px (планшеты/ПК) включаем ваши 120px */
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

        <footer class="indent-area py-4 text-center text-sm text-slate-500 border-t bg-white">
            &copy; {{ date('Y') }} Учет времени — Федин Руслан Александрович
        </footer>
    </div>
</body>
</html>
