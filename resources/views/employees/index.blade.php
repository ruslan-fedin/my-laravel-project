@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">

<style>
    /* Глобальная обертка для центрирования */
    .main-wrapper {
        display: flex;
        justify-content: center;
        width: 100%;
        background-color: #f8fafc;
        min-height: 100vh;
        font-family: 'Inter', sans-serif;
    }

    /* Основной контейнер */
    .content-container {
        width: 100%;
        /* На десктопах ограничиваем ширину и даем ваши 120px отступа */
        max-width: 100%;
        padding: 0 120px 40px 120px;
    }

    /* Мобильная адаптация: убираем огромные отступы */
    @media (max-width: 1024px) {
        .content-container {
            /* Минимальные отступы для мобильных (в т.ч. в альбомном режиме) */
            padding: 0 12px 30px 12px;
        }
    }

    /* Стили элементов */
    .card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden; }

    .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    table { width: 100%; border-collapse: separate; border-spacing: 0; }

    th {
        background: #f8fafc;
        font-weight: 800;
        text-transform: uppercase;
        color: #64748b;
        font-size: 10px;
        letter-spacing: 0.05em;
        padding: 16px;
        border-bottom: 2px solid #f1f5f9;
        white-space: nowrap;
    }

    td { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

    .search-input {
        height: 48px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding-left: 42px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        width: 100%;
    }
</style>

<div class="main-wrapper">
    <div class="content-container">
        {{-- ЗАГОЛОВОК --}}
        <div class="pt-8 mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-black text-slate-900 uppercase tracking-tighter leading-none">
                    {{ request()->routeIs('employees.archive') ? 'Архив сотрудников' : 'Сотрудники' }}
                </h1>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-2">
                    {{ request()->routeIs('employees.archive') ? 'База удаленных записей' : 'Управление кадровым составом организации' }}
                </p>
            </div>

            <a href="{{ route('employees.create') }}" class="bg-slate-900 text-white px-5 h-11 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-md flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-plus"></i> Добавить сотрудника
            </a>
        </div>

        {{-- ПАНЕЛЬ ФИЛЬТРОВ --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-6">
            <div class="lg:col-span-4 flex p-1 bg-slate-200/50 rounded-xl w-full sm:w-fit">
                <a href="{{ route('employees.index') }}"
                   class="flex-1 sm:flex-none text-center px-4 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all {{ request()->routeIs('employees.index') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500' }}">
                    Актив
                </a>
                <a href="{{ route('employees.archive') }}"
                   class="flex-1 sm:flex-none text-center px-4 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all {{ request()->routeIs('employees.archive') ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-500' }}">
                    Архив
                </a>
            </div>

            <div class="lg:col-span-8 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <i class="fas fa-search text-sm"></i>
                </span>
                <input type="text" id="live-search" placeholder="Поиск (Фамилия Имя Отчество полностью)..." class="search-input focus:ring-2 focus:ring-slate-200 outline-none">
                <div id="search-loader" class="hidden absolute right-4 top-3.5">
                    <i class="fas fa-circle-notch fa-spin text-slate-900"></i>
                </div>
            </div>
        </div>

        {{-- ТАБЛИЦА --}}
        <div class="card" id="table-container">
            @if(session('success'))
                <div class="bg-emerald-500 text-white px-6 py-3 text-[10px] font-black uppercase tracking-widest flex items-center">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table id="employees-table">
                    <thead>
                        <tr>
                            <th class="text-center w-16">№</th>
                            <th class="text-center w-20">Фото</th>
                            <th class="text-left">Фамилия Имя Отчество полностью</th>
                            <th class="text-left">Должность</th>
                            <th class="text-center w-32">Статус</th>
                            <th class="text-right px-8 w-40">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-[13px] font-semibold text-slate-700">
                        @include('employees.partials.table_rows', ['employees' => $employees])
                    </tbody>
                </table>
            </div>
        </div>

        <div id="pagination-container" class="mt-6">
            {{ $employees->links() }}
        </div>

        <div class="mt-10 border-t border-slate-200 pt-6">
            @include('layouts.footer')
        </div>
    </div>
</div>

<script>
document.getElementById('live-search').addEventListener('input', function(e) {
    const query = e.target.value;
    const loader = document.getElementById('search-loader');
    loader.classList.remove('hidden');

    fetch(`{{ url()->current() }}?search=${query}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.text())
    .then(data => {
        const parser = new DOMParser();
        const htmlDoc = parser.parseFromString(data, 'text/html');
        document.querySelector('#employees-table tbody').innerHTML = htmlDoc.querySelector('#employees-table tbody').innerHTML;
        document.querySelector('#pagination-container').innerHTML = htmlDoc.querySelector('#pagination-container').innerHTML;
        loader.classList.add('hidden');
    });
});
</script>
@endsection
