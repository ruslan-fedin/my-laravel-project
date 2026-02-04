<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Учёт времени - Главная</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-900">

    <div class="min-h-screen flex flex-col">
  <header class="bg-blue-700 text-white shadow-lg p-6">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-bold flex items-center gap-2">
            <i class="fas fa-user-clock"></i> Система "Табель"
        </h1>

        <div class="flex items-center gap-6">
            @if (Route::has('login'))
                <nav class="flex gap-4">
                    @auth
                        <div class="flex items-center gap-4">
                            <span class="text-sm border-r pr-4 border-blue-500 italic">
                                <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
                            </span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm bg-blue-800 hover:bg-blue-900 px-3 py-1 rounded transition">
                                    Выйти
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold hover:underline">Вход</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-sm font-semibold bg-white text-blue-700 px-3 py-1 rounded hover:bg-gray-100 transition">Регистрация</a>
                        @endif
                    @endauth
                </nav>
            @endif
        </div>
    </div>
</header>

        <main class="flex-grow max-w-7xl mx-auto w-full p-8">
            <h2 class="text-3xl font-light mb-8 text-gray-700 text-center">Добро пожаловать в систему управления кадрами</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <a href="{{ route('employees.index') }}" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-blue-300 transition-all">
                    <div class="text-blue-600 mb-4">
                        <i class="fas fa-users text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 group-hover:text-blue-600 transition">Сотрудники</h3>
                    <p class="text-gray-500 text-sm mb-4">Управление персоналом, ФИО, должности и стаж.</p>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                        Всего: {{ $employees_count }} ({{ $active_employees }} акт.)
                    </div>
                </a>

                <a href="{{ route('timesheets.index') }}" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-green-300 transition-all">
                    <div class="text-green-600 mb-4">
                        <i class="fas fa-calendar-check text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 group-hover:text-green-600 transition">Табеля</h3>
                    <p class="text-gray-500 text-sm mb-4">Создание и ведение графиков учета времени.</p>
                    <div class="text-xs font-semibold uppercase tracking-wider text-green-500">
                        Перейти к заполнению →
                    </div>
                </a>

                <a href="{{ route('positions.index') }}" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-yellow-300 transition-all">
                    <div class="text-yellow-500 mb-4">
                        <i class="fas fa-briefcase text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 group-hover:text-yellow-600 transition">Должности</h3>
                    <p class="text-gray-500 text-sm mb-4">Список всех должностей организации.</p>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                        Всего записей: {{ $positions_count }}
                    </div>
                </a>

                <a href="{{ route('statuses.index') }}" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-purple-300 transition-all">
                    <div class="text-purple-600 mb-4">
                        <i class="fas fa-tags text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 group-hover:text-purple-600 transition">Статусы</h3>
                    <p class="text-gray-500 text-sm mb-4">Явка, больничный, отпуск. Цвета и коды.</p>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                        Настроено: {{ $statuses_count }}
                    </div>
                </a>

            </div>
        </main>

        <footer class="bg-gray-100 border-t p-6 text-center text-gray-500 text-sm">
            &copy; 2026 Система управления персоналом. Работает на Laravel 12.
        </footer>
    </div>

</body>
</html>
