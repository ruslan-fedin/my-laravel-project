<style>
    .nav-link {
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        tracking-widest: 0.1em;
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

    /* Подчеркивание для активной ссылки */
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

    .indent-area {
        padding-left: 40px;
        padding-right: 40px;
    }

    @media (max-width: 768px) {
        .indent-area {
            padding-left: 12px;
            padding-right: 12px;
        }
    }
</style>

<nav class="bg-white border-b border-slate-200 indent-area">
    <div class="flex justify-between h-16 items-center">
        <div class="flex gap-10 items-center">
            <div class="font-black text-xl tracking-tighter text-slate-900 mr-2 uppercase">Система</div>

            <div class="flex gap-6 items-center">
                <a href="{{ route('timesheets.index') }}"
                   class="nav-link {{ request()->routeIs('timesheets.*') ? 'active' : '' }}">
                    Табели
                </a>

                <a href="{{ route('travel-timesheets.index') }}"
                   class="nav-link {{ request()->is('travel-timesheets*') ? 'active' : '' }}">
                    Выезды (Центр)
                </a>

                <a href="/employees"
                   class="nav-link {{ request()->is('employees*') ? 'active' : '' }}">
                    Сотрудники
                </a>

                <a href="/positions"
                   class="nav-link {{ request()->is('positions*') ? 'active' : '' }}">
                    Должности
                </a>

                <a href="/statuses"
                   class="nav-link {{ request()->is('statuses*') ? 'active' : '' }}">
                    Статусы
                </a>
            </div>
        </div>

        <div class="flex items-center gap-6">
            <div class="flex flex-col items-end">
                <span class="text-[9px] font-black uppercase text-slate-400 leading-none mb-1">Пользователь</span>
                <span class="text-[11px] font-black text-slate-900 uppercase leading-none">
                    {{ Auth::user()->last_name ?? '' }} {{ Auth::user()->name }} {{ Auth::user()->middle_name ?? '' }}
                </span>
            </div>

            <div class="h-8 w-px bg-slate-200"></div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-[10px] font-black uppercase text-red-500 hover:text-red-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Выйти
                </button>
            </form>
        </div>
    </div>
</nav>
