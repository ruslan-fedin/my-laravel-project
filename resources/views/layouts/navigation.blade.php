<style>
    .nav-link {
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #64748b;
        transition: all 0.2s;
        position: relative;
        padding: 4px 0;
        white-space: nowrap;
    }
    .nav-link:hover { color: #0f172a; }
    .nav-link.active { color: #3b82f6; }

    /* Ваши отступы: 12px мобильные / 120px ПК */
    .indent-area {
        padding-left: 12px;
        padding-right: 12px;
    }

    @media (min-width: 1024px) {
        .indent-area {
            padding-left: 120px !important;
            padding-right: 120px !important;
        }
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

    #mobile-menu {
        max-height: 0;
        overflow: hidden;
        transition: all 0.3s ease-in-out;
        opacity: 0;
    }
    #mobile-menu.open {
        max-height: 1000px;
        opacity: 1;
        border-top: 1px solid #e2e8f0;
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<nav class="bg-white border-b border-slate-200 indent-area relative z-50">
    <div class="flex justify-between h-16 items-center">

        <div class="flex gap-10 items-center">
            <div class="font-black text-xl tracking-tighter text-slate-900 uppercase">Система</div>

            @auth
            <div class="hidden lg:flex gap-6 items-center">
                <a href="{{ route('timesheets.index') }}" class="nav-link {{ request()->routeIs('timesheets.*') ? 'active' : '' }}">Табели</a>
                <a href="{{ route('travel-timesheets.index') }}" class="nav-link {{ request()->is('travel-timesheets*') ? 'active' : '' }}">Выезды (Центр)</a>
                <a href="/employees" class="nav-link {{ request()->is('employees*') ? 'active' : '' }}">Сотрудники</a>
                <a href="{{ route('employees.import.form') }}" class="nav-link {{ request()->routeIs('employees.import.form') ? 'active' : '' }}">Импорт</a>
                <a href="{{ route('brigades.index') }}" class="nav-link {{ request()->is('brigades*') ? 'active' : '' }}">Структура</a>
                <a href="/positions" class="nav-link {{ request()->is('positions*') ? 'active' : '' }}">Должности</a>
                <a href="/statuses" class="nav-link {{ request()->is('statuses*') ? 'active' : '' }}">Статусы</a>
            </div>
            @endauth
        </div>

        <div class="flex items-center gap-4">
            @auth
    @php
        $parts = explode(' ', trim(Auth::user()->name));
        $lastName = $parts[0] ?? '';
        $firstName = $parts[1] ?? '';
        $middleName = $parts[2] ?? '';
    @endphp

    <div class="flex items-center gap-4">
        {{-- Текстовый блок ФИО --}}
        <div class="flex flex-col items-end leading-none py-1">
            {{-- Фамилия: максимально жирная, глубокий черный цвет --}}
            <span class="text-[13px] font-black text-slate-950 uppercase tracking-wider mb-1">
                {{ $lastName }}
            </span>

            {{-- Имя Отчество: более легкий шрифт, акцентный серо-синий цвет --}}
            <span class="text-[10px] font-extrabold text-blue-600/80 uppercase tracking-tight">
                {{ $firstName }} {{ $middleName }}
            </span>
        </div>

        {{-- Разделитель с легким градиентом --}}
        <div class="hidden sm:block h-10 w-[2px] bg-gradient-to-b from-slate-100 via-slate-300 to-slate-100 mx-1"></div>

        {{-- Кнопка выхода с эффектом при наведении --}}
        <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
            @csrf
            <button type="submit" class="group flex items-center justify-center w-10 h-10 rounded-xl hover:bg-red-50 transition-all duration-300">
                <i class="fa-solid fa-right-from-bracket text-slate-400 group-hover:text-red-500 text-lg"></i>
            </button>
        </form>

        {{-- Мобильная кнопка меню --}}
        <button onclick="toggleMenu()" class="lg:hidden w-11 h-11 flex items-center justify-center rounded-xl bg-slate-900 text-white shadow-lg shadow-slate-200 active:scale-95 transition-transform">
            <i id="menu-icon" class="fa-solid fa-bars text-lg"></i>
        </button>
    </div>
@endauth
        </div>
    </div>

    @auth
    <div id="mobile-menu" class="lg:hidden bg-white">
        <div class="flex flex-col py-4 space-y-1">
            <a href="{{ route('timesheets.index') }}" class="px-4 py-3 text-[11px] font-black uppercase {{ request()->routeIs('timesheets.*') ? 'text-blue-600 bg-blue-50' : 'text-slate-600' }}">Табели</a>
            <a href="{{ route('travel-timesheets.index') }}" class="px-4 py-3 text-[11px] font-black uppercase {{ request()->is('travel-timesheets*') ? 'text-blue-600 bg-blue-50' : 'text-slate-600' }}">Выезды (Центр)</a>
            <a href="/employees" class="px-4 py-3 text-[11px] font-black uppercase {{ request()->is('employees*') ? 'text-blue-600 bg-blue-50' : 'text-slate-600' }}">Сотрудники</a>
            <a href="{{ route('employees.import.form') }}" class="px-4 py-3 text-[11px] font-black uppercase {{ request()->routeIs('employees.import.form') ? 'text-blue-600 bg-blue-50' : 'text-slate-600' }}">Импорт</a>
            <a href="{{ route('brigades.index') }}" class="px-4 py-3 text-[11px] font-black uppercase {{ request()->is('brigades*') ? 'text-blue-600 bg-blue-50' : 'text-slate-600' }}">Структура</a>

            <div class="h-px bg-slate-100 my-2 mx-4"></div>

            <a href="/positions" class="px-4 py-2 text-[11px] font-black uppercase text-slate-500">Должности</a>
            <a href="/statuses" class="px-4 py-2 text-[11px] font-black uppercase text-slate-500">Статусы</a>

            <div class="h-px bg-slate-100 my-2 mx-4"></div>

            <form method="POST" action="{{ route('logout') }}" class="px-4 py-2">
                @csrf
                <button type="submit" class="w-full text-left text-[11px] font-black uppercase text-red-500 flex items-center gap-2">
                    <i class="fa-solid fa-right-from-bracket"></i> Выйти
                </button>
            </form>
        </div>
    </div>
    @endauth
</nav>

<script>
function toggleMenu() {
    const menu = document.getElementById('mobile-menu');
    const icon = document.getElementById('menu-icon');
    if(!menu) return;

    menu.classList.toggle('open');
    if (menu.classList.contains('open')) {
        icon.classList.replace('fa-bars', 'fa-xmark');
    } else {
        icon.classList.replace('fa-xmark', 'fa-bars');
    }
}
</script>
