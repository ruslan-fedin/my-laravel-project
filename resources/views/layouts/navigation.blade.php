<style>
    .nav-link {
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #64748b; /* slate-500 */
        transition: all 0.2s;
        position: relative;
        padding: 4px 0;
    }

    .nav-link:hover {
        color: #0f172a; /* slate-900 */
    }

    .nav-link.active {
        color: #3b82f6; /* blue-600 */
    }

    /* Подчеркивание только для ПК */
    @media (min-width: 1024px) {
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: #3b82f6;
            border-radius: 3px 3px 0 0;
        }
    }

    .indent-area {
        padding-left: 12px;
        padding-right: 12px;
    }

    @media (min-width: 768px) {
        .indent-area {
            padding-left: 40px;
            padding-right: 40px;
        }
    }

    /* Мобильное меню анимация */
    #mobile-menu {
        transition: all 0.3s ease-in-out;
        max-height: 0;
        overflow: hidden;
    }

    #mobile-menu.open {
        max-height: 500px;
        border-t: 1px solid #e2e8f0;
        padding-bottom: 20px;
    }
</style>

<nav class="bg-white border-b border-slate-200 indent-area relative z-50">
    <div class="flex justify-between h-16 items-center">

        {{-- ЛЕВАЯ ЧАСТЬ: ЛОГО И ПК-МЕНЮ --}}
        <div class="flex gap-10 items-center">
            <div class="font-black text-xl tracking-tighter text-slate-900 uppercase">Система</div>

            {{-- Ссылки для ПК (от 1024px) --}}
            <div class="hidden lg:flex gap-6 items-center">
                <a href="{{ route('timesheets.index') }}" class="nav-link {{ request()->routeIs('timesheets.*') ? 'active' : '' }}">Табели</a>
                <a href="{{ route('travel-timesheets.index') }}" class="nav-link {{ request()->is('travel-timesheets*') ? 'active' : '' }}">Выезды (Центр)</a>
                <a href="/employees" class="nav-link {{ request()->is('employees') ? 'active' : '' }}">Сотрудники</a>
                <a href="{{ route('employees.import.form') }}" class="nav-link {{ request()->routeIs('employees.import.form') ? 'active' : '' }}">Импорт</a>
                <a href="{{ route('brigades.index') }}" class="nav-link {{ request()->is('brigades*') ? 'active' : '' }}">Структура</a>
                <a href="/positions" class="nav-link {{ request()->is('positions*') ? 'active' : '' }}">Должности</a>
                <a href="/statuses" class="nav-link {{ request()->is('statuses*') ? 'active' : '' }}">Статусы</a>
            </div>
        </div>

        {{-- ПРАВАЯ ЧАСТЬ: ПРОФИЛЬ И ВЫХОД --}}
        <div class="flex items-center gap-4 md:gap-6">
            {{-- Данные пользователя (скрываем мелкие детали на очень маленьких экранах) --}}
            <div class="hidden sm:flex flex-col items-end">
                <span class="text-[8px] font-black uppercase text-slate-400 leading-none mb-1 text-right">Пользователь</span>
                <span class="text-[10px] md:text-[11px] font-black text-slate-900 uppercase leading-none text-right">
                    {{ Auth::user()->last_name ?? '' }}<br class="md:hidden">
                    {{ Auth::user()->first_name ?? Auth::user()->name }}
                    {{ Auth::user()->middle_name ?? '' }}
                </span>
            </div>

            <div class="hidden sm:block h-8 w-px bg-slate-200"></div>

            {{-- Кнопка выхода для ПК --}}
            <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                @csrf
                <button type="submit" class="text-[10px] font-black uppercase text-red-500 hover:text-red-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-right-from-bracket"></i> Выйти
                </button>
            </form>

            {{-- Кнопка гамбургера для мобильных --}}
            <button onclick="toggleMenu()" class="lg:hidden text-slate-900 text-2xl p-2">
                <i id="menu-icon" class="fa-solid fa-bars"></i>
            </button>
        </div>
    </div>

    {{-- МОБИЛЬНОЕ МЕНЮ --}}
    <div id="mobile-menu" class="lg:hidden bg-white border-t border-slate-50">
        <div class="flex flex-col py-4 space-y-1">
            <a href="{{ route('timesheets.index') }}" class="px-4 py-3 text-[11px] font-black uppercase {{ request()->routeIs('timesheets.*') ? 'text-blue-600 bg-blue-50' : 'text-slate-600' }}">Табели</a>
            <a href="{{ route('travel-timesheets.index') }}" class="px-4 py-3 text-[11px] font-black uppercase {{ request()->is('travel-timesheets*') ? 'text-blue-600 bg-blue-50' : 'text-slate-600' }}">Выезды (Центр)</a>
            <a href="/employees" class="px-4 py-3 text-[11px] font-black uppercase {{ request()->is('employees') ? 'text-blue-600 bg-blue-50' : 'text-slate-600' }}">Сотрудники</a>
            <a href="{{ route('employees.import.form') }}" class="px-4 py-3 text-[11px] font-black uppercase {{ request()->routeIs('employees.import.form') ? 'text-blue-600 bg-blue-50' : 'text-slate-600' }}">Импорт</a>
            <a href="{{ route('brigades.index') }}" class="px-4 py-3 text-[11px] font-black uppercase {{ request()->is('brigades*') ? 'text-blue-600 bg-blue-50' : 'text-slate-600' }}">Структура</a>

            <div class="h-px bg-slate-100 my-2"></div>

            <a href="/positions" class="px-4 py-3 text-[11px] font-black uppercase text-slate-500">Должности</a>
            <a href="/statuses" class="px-4 py-3 text-[11px] font-black uppercase text-slate-500">Статусы</a>

            <div class="h-px bg-slate-100 my-2"></div>

            <form method="POST" action="{{ route('logout') }}" class="px-4 py-3">
                @csrf
                <button type="submit" class="w-full text-left text-[11px] font-black uppercase text-red-500 flex items-center gap-2">
                    <i class="fa-solid fa-right-from-bracket"></i> Выход из системы
                </button>
            </form>
        </div>
    </div>
</nav>

<script>
function toggleMenu() {
    const menu = document.getElementById('mobile-menu');
    const icon = document.getElementById('menu-icon');

    if (menu.classList.contains('open')) {
        menu.classList.remove('open');
        icon.classList.replace('fa-xmark', 'fa-bars');
    } else {
        menu.classList.add('open');
        icon.classList.replace('fa-bars', 'fa-xmark');
    }
}
</script>
