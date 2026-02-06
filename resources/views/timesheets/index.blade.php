@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    /* БАЗОВЫЕ ОТСТУПЫ СТРАНИЦЫ */
    .archive-wrapper {
        padding: 20px 15px;
    }
    @media (min-width: 768px) {
        .archive-wrapper { padding: 30px 40px; }
    }

    /* ФИКС ЦЕНТРОВКИ СТАТУСОВ */
    .status-select {
        width: 100% !important;
        height: 100% !important;
        border: none !important;
        background: transparent !important;
        text-align: center !important;
        text-align-last: center !important;
        font-weight: 900 !important;
        font-size: 13px !important;
        outline: none !important;
        cursor: pointer !important;
        display: block !important;
    }

    .day-col, td.day-cell {
        padding: 0 !important;
        vertical-align: middle !important;
        text-align: center !important;
        min-width: 35px !important;
        height: 44px !important;
    }

    /* СКРЫТИЕ ТАБЛИЦЫ НА МОБИЛЬНЫХ */
    .desktop-table-container {
        display: none;
    }
    @media (min-width: 1024px) {
        .desktop-table-container { display: block; }
    }

    /* МОБИЛЬНЫЕ КАРТОЧКИ */
    .mobile-card-list {
        display: block;
    }
    @media (min-width: 1024px) {
        .mobile-card-list { display: none; }
    }

    .m-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
</style>

<div class="archive-wrapper">
    {{-- ШАПКА --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-slate-900 uppercase tracking-tighter">Архив табелей</h1>
            <p class="text-[10px] md:text-[11px] text-slate-400 font-bold uppercase tracking-widest mt-1">Реестр всех отчетных периодов системы</p>
        </div>

        <a href="{{ route('timesheets.create') }}" class="w-full md:w-auto bg-slate-900 text-white px-6 py-4 md:py-3 rounded-lg text-[11px] font-black uppercase tracking-widest hover:bg-black transition shadow-xl flex justify-center items-center gap-2 active:scale-95">
            <i class="fas fa-plus text-[10px]"></i> Создать табель
        </a>
    </div>

    {{-- УВЕДОМЛЕНИЯ --}}
    @if(session('error'))
        <div class="mb-6 p-4 bg-rose-50 border-2 border-rose-200 rounded-xl flex items-center shadow-sm">
            <i class="fas fa-exclamation-triangle text-rose-600 mr-4"></i>
            <div class="text-rose-900 font-black text-[10px] uppercase tracking-wider">ОШИБКА: {{ session('error') }}</div>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border-2 border-emerald-200 rounded-xl flex items-center shadow-sm">
            <i class="fas fa-check-circle text-emerald-600 mr-4"></i>
            <div class="text-emerald-900 font-black text-[10px] uppercase tracking-wider">УСПЕШНО: {{ session('success') }}</div>
        </div>
    @endif

    {{-- ВЕРСИЯ ДЛЯ ПК (ТАБЛИЦА) --}}
    <div class="desktop-table-container bg-white shadow-sm rounded-xl overflow-hidden border border-slate-200">
        <table class="min-w-full border-collapse" id="mainTable">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                    <th onclick="sortTable(0)" class="px-6 py-5 text-center w-20 border-r cursor-pointer hover:bg-slate-100 transition">№</th>
                    <th onclick="sortTable(1)" class="px-6 py-5 text-left border-r cursor-pointer hover:bg-slate-100 transition">Отчетный Период</th>
                    <th onclick="sortTable(2)" class="px-6 py-5 text-center border-r cursor-pointer hover:bg-slate-100 transition">Даты (От — До)</th>
                    <th class="px-6 py-5 text-right px-8">Управление</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($timesheets as $ts)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-5 text-center text-slate-400 border-r font-medium text-xs">{{ $loop->iteration }}</td>
                    <td class="px-6 py-5 font-black text-slate-700 uppercase border-r tracking-tight">
                        {{ \Carbon\Carbon::parse($ts->start_date ?? $ts->created_at)->translatedFormat('F Y') }}
                    </td>
                    <td class="px-6 py-5 text-center text-slate-500 border-r font-medium text-xs">
                        {{ \Carbon\Carbon::parse($ts->start_date)->format('d.m.Y') }} <span class="text-slate-300 mx-1">—</span> {{ \Carbon\Carbon::parse($ts->end_date)->format('d.m.Y') }}
                    </td>
                    <td class="px-6 py-5 text-right px-8">
                        <div class="flex justify-end items-center gap-5">
                            <a href="{{ route('timesheets.excel', $ts->id) }}" class="text-emerald-500 hover:text-emerald-700 transition" title="Excel"><i class="fas fa-file-excel fa-lg"></i></a>
                            <a href="{{ route('timesheets.pdf', $ts->id) }}" class="text-rose-500 hover:text-rose-700 transition" title="PDF"><i class="fas fa-file-pdf fa-lg"></i></a>
                            <span class="h-4 w-[1px] bg-slate-200"></span>
                            <a href="{{ route('timesheets.show', $ts->id) }}" class="text-blue-500 hover:text-blue-700 transition"><i class="fas fa-eye fa-lg"></i></a>
                            <a href="{{ route('timesheets.edit', $ts->id) }}" class="text-slate-400 hover:text-slate-900 transition"><i class="fas fa-edit fa-lg"></i></a>
                            <form action="{{ route('timesheets.destroy', $ts->id) }}" method="POST" class="inline" onsubmit="return confirm('ВЫ УВЕРЕНЫ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-300 hover:text-red-600 transition"><i class="fas fa-trash-alt fa-lg"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="py-20 text-center text-slate-300 font-black uppercase text-[10px]">Данные отсутствуют</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ВЕРСИЯ ДЛЯ МОБИЛЬНЫХ (КАРТОЧКИ) --}}
    <div class="mobile-card-list">
        @forelse($timesheets as $ts)
        <div class="m-card">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <span class="text-[9px] font-black text-slate-300 uppercase">#{{ $loop->iteration }}</span>
                    <h3 class="text-lg font-black text-slate-800 uppercase leading-none">
                        {{ \Carbon\Carbon::parse($ts->start_date ?? $ts->created_at)->translatedFormat('F Y') }}
                    </h3>
                    <p class="text-[11px] text-slate-400 font-bold mt-1">
                        {{ \Carbon\Carbon::parse($ts->start_date)->format('d.m.Y') }} — {{ \Carbon\Carbon::parse($ts->end_date)->format('d.m.Y') }}
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('timesheets.show', $ts->id) }}" class="w-10 h-10 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 mb-4">
                <a href="{{ route('timesheets.excel', $ts->id) }}" class="flex items-center justify-center gap-2 py-2 bg-emerald-50 text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-widest">
                    <i class="fas fa-file-excel"></i> EXCEL
                </a>
                <a href="{{ route('timesheets.pdf', $ts->id) }}" class="flex items-center justify-center gap-2 py-2 bg-rose-50 text-rose-600 rounded-lg text-[10px] font-black uppercase tracking-widest">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
            </div>

            <div class="flex justify-between items-center pt-3 border-t border-slate-100">
                <a href="{{ route('timesheets.edit', $ts->id) }}" class="text-slate-400 text-[10px] font-black uppercase flex items-center gap-2">
                    <i class="fas fa-edit"></i> Редактировать
                </a>
                <form action="{{ route('timesheets.destroy', $ts->id) }}" method="POST" onsubmit="return confirm('УДАЛИТЬ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-300 text-[10px] font-black uppercase flex items-center gap-2">
                        <i class="fas fa-trash-alt"></i> Удалить
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center py-20 text-slate-300 font-black uppercase text-[10px]">Архив пуст</div>
        @endforelse
    </div>

    <div class="mt-6 text-[9px] md:text-[10px] font-bold uppercase text-slate-400">
        Запись внес: <span class="text-slate-900">{{ Auth::user()->name }}</span>
    </div>

    <div class="mt-8">
        @include('layouts.footer')
    </div>
</div>

<script>
function sortTable(n) {
    var table = document.getElementById("mainTable"), rows, switching = true, i, x, y, shouldSwitch, dir = "asc", switchcount = 0;
    while (switching) {
        switching = false; rows = table.rows;
        for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];
            if (dir == "asc") {
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) { shouldSwitch = true; break; }
            } else if (dir == "desc") {
                if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) { shouldSwitch = true; break; }
            }
        }
        if (shouldSwitch) { rows[i].parentNode.insertBefore(rows[i + 1], rows[i]); switching = true; switchcount++; }
        else {
            if (switchcount == 0 && dir == "asc") { dir = "desc"; switching = true; }
        }
    }
}
</script>

@endsection
