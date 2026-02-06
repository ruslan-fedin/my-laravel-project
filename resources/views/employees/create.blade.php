@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">

<style>
    /* Адаптивные отступы, как в редакторе табеля */
    .content-wrapper {
        padding: 0 120px 40px 120px;
        font-family: 'Inter', sans-serif;
    }
    @media (max-width: 1024px) {
        .content-wrapper { padding: 0 10px 20px 10px; }
    }

    .card { background: white; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); overflow: hidden; }
    .filter-input { height: 44px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0 12px 0 40px; font-size: 11px; font-weight: 800; outline: none; text-transform: uppercase; letter-spacing: 0.05em; }
    .filter-input:focus { border-color: #0f172a; }

    table { border-collapse: separate; border-spacing: 0; width: 100%; }
    th { background: #f8fafc; font-weight: 900; text-transform: uppercase; color: #475569; font-size: 10px; letter-spacing: 0.05em; padding: 12px 16px; border-bottom: 2px solid #e2e8f0; }
    td { border-bottom: 1px solid #f1f5f9; padding: 12px 16px; vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background-color: #f8fafc; transition: background-color 0.2s; }
</style>

<div class="content-wrapper mt-6">
    {{-- ЗАГОЛОВОК И КНОПКА ДОБАВЛЕНИЯ --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight">
                {{ request()->routeIs('employees.archive') ? 'Архив сотрудников' : 'Сотрудники' }}
            </h1>
            <p class="text-slate-500 font-bold text-xs uppercase mt-1">
                {{ request()->routeIs('employees.archive') ? 'Список удаленных из основной базы' : 'Управление кадровым составом' }}
            </p>
        </div>

        <a href="{{ route('employees.create') }}" class="bg-slate-900 text-white px-6 h-11 rounded-lg font-black text-[10px] uppercase tracking-widest hover:bg-black transition shadow-lg flex items-center justify-center gap-2 w-full md:w-auto">
            <i class="fas fa-plus"></i> <span>Добавить сотрудника</span>
        </a>
    </div>

    {{-- ПАНЕЛЬ УПРАВЛЕНИЯ (ВКЛАДКИ И ПОИСК) --}}
    <div class="card p-4 mb-6 bg-slate-50/50">
        <div class="flex flex-col xl:flex-row gap-4 justify-between items-center">
            {{-- Вкладки --}}
            <div class="flex gap-2 w-full xl:w-auto bg-white p-1 rounded-lg border border-slate-200">
                <a href="{{ route('employees.index') }}"
                   class="flex-1 xl:flex-none px-5 py-2.5 rounded-md text-[10px] font-black uppercase tracking-wider transition-all flex justify-center items-center gap-2 {{ request()->routeIs('employees.index') ? 'bg-slate-900 text-white shadow-md' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600' }}">
                    <i class="fas fa-users"></i> Активный состав
                </a>
                <a href="{{ route('employees.archive') }}"
                   class="flex-1 xl:flex-none px-5 py-2.5 rounded-md text-[10px] font-black uppercase tracking-wider transition-all flex justify-center items-center gap-2 {{ request()->routeIs('employees.archive') ? 'bg-rose-600 text-white shadow-md' : 'text-slate-400 hover:bg-rose-50 hover:text-rose-600' }}">
                    <i class="fas fa-archive"></i> Архив
                </a>
            </div>

            {{-- Живой поиск --}}
            <div class="relative w-full xl:w-1/3">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input type="text"
                       id="live-search"
                       placeholder="Поиск (Фамилия Имя Отчество)..."
                       class="filter-input w-full placeholder:text-slate-300">
                <div id="search-loader" class="hidden absolute right-3 top-3">
                    <i class="fas fa-circle-notch fa-spin text-slate-400 text-xs"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ТАБЛИЦА --}}
    <div class="card shadow-sm" id="table-container">
        @if(session('success'))
            <div class="bg-emerald-50 border-b border-emerald-100 text-emerald-600 px-6 py-4 text-[11px] font-black uppercase tracking-wide flex items-center gap-2">
                <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table id="employees-table">
                <thead>
                    <tr>
                        <th class="w-16 text-center border-r border-slate-200">№</th>
                        <th class="w-20 text-center border-r border-slate-200">Фото</th>
                        <th class="text-left border-r border-slate-200">Фамилия Имя Отчество полностью</th>
                        <th class="text-left border-r border-slate-200">Должность</th>
                        <th class="text-center w-32 border-r border-slate-200">Статус</th>
                        <th class="text-right w-48">Управление</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-[13px] text-slate-700 font-semibold">
                    @include('employees.partials.table_rows', ['employees' => $employees])
                </tbody>
            </table>
        </div>
    </div>

    <div id="pagination-container" class="mt-8">
        {{ $employees->links() }}
    </div>

    <div class="mt-8 border-t border-slate-200 pt-6">
        @include('layouts.footer')
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

            // Обновляем тело таблицы
            const newTbody = htmlDoc.querySelector('#employees-table tbody');
            const currentTbody = document.querySelector('#employees-table tbody');
            if (newTbody && currentTbody) {
                currentTbody.innerHTML = newTbody.innerHTML;
            }

            // Обновляем пагинацию
            const newPagination = htmlDoc.querySelector('#pagination-container');
            const currentPagination = document.querySelector('#pagination-container');
            if (newPagination && currentPagination) {
                currentPagination.innerHTML = newPagination.innerHTML;
            }

            loader.classList.add('hidden');
        })
        .catch(error => {
            console.error('Ошибка поиска:', error);
            loader.classList.add('hidden');
        });
    });
</script>
@endsection
