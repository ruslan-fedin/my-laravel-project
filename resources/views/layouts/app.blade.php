<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Учет времени</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .indent-area {
            padding-left: 120px !important;
            padding-right: 120px !important;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased">
    <div class="min-h-screen">
        @include('layouts.navigation')

        <main class="indent-area py-12">
            @yield('content')
        </main>
    </div>
</body>
</html>
