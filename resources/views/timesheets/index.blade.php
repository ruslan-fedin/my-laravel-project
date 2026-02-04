@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

{{-- ФИКС ЦЕНТРОВКИ СТАТУСОВ (Я, В, К) --}}
<style>
    /* Настройка самого выпадающего списка */
    .status-select {
        width: 100% !important;
        height: 100% !important;
        border: none !important;
        background: transparent !important;
        margin: 0 !important;
        padding: 0 !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        text-align: center !important;
        text-align-last: center !important; /* Центровка для Chrome/Edge/Firefox */
        font-weight: 900 !important;
        font-size: 13px !important;
        outline: none !important;
        cursor: pointer !important;
        display: block !important;
    }

    /* Убираем стрелку в IE/Edge */
    .status-select::-ms-expand {
        display: none !important;
    }

    /* Убираем отступы у ячеек с днями */
    .day-col, td.day-cell {
        padding: 0 !important;
        vertical-align: middle !important;
        text-align: center !important;
        min-width: 35px !important;
        height: 44px !important;
    }
</style>

<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tighter">Архив табелей</h1>
        <p class="text-[11px] text-slate-400 font-bold uppercase tracking-widest mt-1">Реестр всех отчетных периодов системы</p>
    </div>

    {{-- Кнопка создания --}}
    <a href="{{ route('timesheets.create') }}" class="bg-slate-900 text-white px-6 py-3 rounded text-[11px] font-black uppercase tracking-widest hover:bg-black transition shadow-xl flex items-center gap-2">
        <i class="fas fa-plus text-[10px]"></i> Создать новый табель
    </a>
</div>

{{-- БЛОК УВЕДОМЛЕНИЙ --}}
@if(session('error'))
    <div class="mb-6 p-4 bg-rose-50 border-2 border-rose-200 rounded-lg flex items-center shadow-sm">
        <i class="fas fa-exclamation-triangle text-rose-600 mr-4 fa-lg"></i>
        <div class="text-rose-900 font-black text-[11px] uppercase tracking-wider">
            ОШИБКА: {{ session('error') }}
        </div>
    </div>
@endif

@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border-2 border-emerald-200 rounded-lg flex items-center shadow-sm">
        <i class="fas fa-check-circle text-emerald-600 mr-4 fa-lg"></i>
        <div class="text-emerald-900 font-black text-[11px] uppercase tracking-wider">
            УСПЕШНО: {{ session('success') }}
        </div>
    </div>
@endif

{{-- Таблица с данными --}}
<div class="bg-white shadow-sm rounded-lg overflow-hidden border border-slate-200">
    <table class="min-w-full border-collapse" id="mainTable">
        <thead class="bg-slate-50 border-b border-slate-200">
            <tr class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                <th onclick="sortTable(0)" class="px-6 py-5 text-center w-20 border-r cursor-pointer hover:bg-slate-100 transition">№</th>
                <th onclick="sortTable(1)" class="px-6 py-5 text-left border-r cursor-pointer hover:bg-slate-100 transition">Отчетный Период</th>
                <th onclick="sortTable(2)" class="px-6 py-5 text-center border-r cursor-pointer hover:bg-slate-100 transition">Даты (От — До)</th>
                <th class="px-6 py-5 text-right px-8">Управление</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-sm">
            @forelse($timesheets as $ts)
            <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-5 text-center text-slate-400 border-r font-medium text-xs">{{ $loop->iteration }}</td>
                <td class="px-6 py-5 font-black text-slate-700 uppercase border-r tracking-tight">
                    {{ \Carbon\Carbon::parse($ts->start_date ?? $ts->created_at)->translatedFormat('F Y') }}
                </td>
                <td class="px-6 py-5 text-center text-slate-500 border-r font-medium">
                    {{ \Carbon\Carbon::parse($ts->start_date)->format('d.m.Y') }} <span class="text-slate-300 mx-1">—</span> {{ \Carbon\Carbon::parse($ts->end_date)->format('d.m.Y') }}
                </td>
                <td class="px-6 py-5 text-right px-8">
                    <div class="flex justify-end items-center gap-5">
                        <a href="{{ route('timesheets.excel', $ts->id) }}" class="text-emerald-500 hover:text-emerald-700 transition" title="Выгрузить в Excel"><i class="fas fa-file-excel fa-lg"></i></a>
                        <a href="{{ route('timesheets.pdf', $ts->id) }}" class="text-rose-500 hover:text-rose-700 transition" title="Выгрузить в PDF"><i class="fas fa-file-pdf fa-lg"></i></a>

                        <span class="h-4 w-[1px] bg-slate-200 mx-1"></span>

                        <a href="{{ route('timesheets.show', $ts->id) }}" class="text-blue-500 hover:text-blue-700 transition" title="Открыть просмотр"><i class="fas fa-eye fa-lg"></i></a>
                        <a href="{{ route('timesheets.edit', $ts->id) }}" class="text-slate-400 hover:text-slate-900 transition" title="Редактировать данные"><i class="fas fa-edit fa-lg"></i></a>

                        <form action="{{ route('timesheets.destroy', $ts->id) }}" method="POST" class="inline" onsubmit="return confirm('ВЫ УВЕРЕНЫ, ЧТО ХОТИТЕ УДАЛИТЬ ЭТОТ ТАБЕЛЬ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-slate-300 hover:text-red-600 transition"><i class="fas fa-trash-alt fa-lg"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-24 text-center">
                    <div class="text-slate-300 font-black uppercase text-[10px] tracking-[0.2em]">Данные отсутствуют</div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Информация о текущем пользователе --}}
<div class="mt-6 text-[10px] font-bold uppercase text-slate-400">
    Запись внес: <span class="text-slate-900">{{ Auth::user()->name }}</span>
</div>

{{-- ПОДКЛЮЧЕНИЕ ПОДВАЛА --}}
<div class="mt-8">
    @include('partials.footer')
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
