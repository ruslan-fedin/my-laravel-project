@extends('layouts.app')

@section('title', 'Выезды — Список табелей')

@section('content')
<style>
    /* БАЗОВАЯ СТРУКТУРА С ОТСТУПАМИ */
    .index-page-wrapper {
        padding: 20px 15px; /* Уменьшил для мобилок */
        background-color: #f8fafc;
        min-height: calc(100vh - 64px);
        font-family: 'Inter', sans-serif;
    }

    @media (min-width: 768px) {
        .index-page-wrapper { padding: 30px 40px; }
    }

    /* ЗАГОЛОВОК */
    .page-title {
        font-size: 24px;
        font-weight: 950;
        text-transform: uppercase;
        letter-spacing: -0.03em;
        color: #0f172a;
    }
    @media (min-width: 768px) { .page-title { font-size: 34px; } }

    /* КАРТОЧКА / ТАБЛИЦА */
    .card-container {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
        overflow: hidden;
        margin-top: 20px;
        width: 100%;
    }

    /* СТИЛИ ТАБЛИЦЫ ДЛЯ ПК */
    .table-custom {
        width: 100%;
        border-collapse: collapse;
        display: none; /* Скрыта на мобильных */
    }
    @media (min-width: 768px) { .table-custom { display: table; } }

    .table-custom thead th {
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.12em;
        padding: 16px 30px;
        background: #fbfcfd;
        border-bottom: 2px solid #f1f5f9;
    }

    .row-style { border-bottom: 1px solid #f1f5f9; transition: background 0.2s; }
    .row-style:hover { background: #f8fafc; }

    .num-cell { text-align: center; color: #cbd5e1; font-weight: 900; border-right: 1px solid #f1f5f9; width: 70px; }
    .month-cell { padding-left: 40px; font-weight: 950; text-transform: uppercase; color: #1e293b; font-size: 16px; }
    .dates-cell { text-align: center; color: #475569; font-weight: 800; border-left: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9; background-color: #fcfdfe; width: 300px; }
    .cell-py { padding-top: 14px; padding-bottom: 14px; }

    /* МОБИЛЬНЫЕ КАРТОЧКИ (вместо таблицы) */
    .mobile-card {
        display: block;
        padding: 20px;
        border-bottom: 1px solid #f1f5f9;
    }
    @media (min-width: 768px) { .mobile-card { display: none; } }

    /* КНОПКИ */
    .action-container { display: flex; align-items: center; justify-content: center; gap: 25px; }
    .btn-icon { color: #94a3b8; font-size: 24px; background: none; border: none; transition: all 0.2s; }
    .btn-icon:hover { transform: scale(1.15); }
    .btn-icon.view:hover { color: #2563eb; }
    .btn-icon.delete:hover { color: #ef4444; }

    /* ФОРМА СОЗДАНИЯ */
    .create-box {
        background: white;
        padding: 20px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .input-date {
        height: 48px;
        border: 2px solid #f1f5f9;
        border-radius: 10px;
        padding: 0 12px;
        font-size: 14px;
        font-weight: 700;
        width: 100%;
        outline: none;
        transition: border-color 0.2s;
    }
    .input-date:focus { border-color: #3b82f6; }

    .btn-submit {
        height: 48px;
        background: #0f172a;
        color: white;
        width: 100%;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
        margin-top: 10px;
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="index-page-wrapper">
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-end gap-6 mb-8">
        <div>
            <h1 class="page-title">Выездные табели</h1>
            <p class="text-slate-400 text-[11px] font-black uppercase tracking-widest mt-1">Реестр периодов</p>
        </div>

        {{-- ФОРМА СОЗДАНИЯ: Адаптивная сетка --}}
        <form action="{{ route('travel-timesheets.store') }}" method="POST" class="create-box w-full lg:w-auto">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:flex items-end gap-4">
                <div class="flex flex-col flex-1">
                    <span class="text-[10px] font-black text-slate-400 uppercase mb-1 ml-1">Начало:</span>
                    <input type="date" name="start_date" required class="input-date">
                </div>
                <div class="flex flex-col flex-1">
                    <span class="text-[10px] font-black text-slate-400 uppercase mb-1 ml-1">Конец:</span>
                    <input type="date" name="end_date" required class="input-date">
                </div>
                <div class="w-full lg:w-auto">
                    <button type="submit" class="btn-submit lg:px-8">Создать</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card-container">
        {{-- ТАБЛИЦА ДЛЯ ПК --}}
        <table class="table-custom">
            <thead>
                <tr>
                    <th class="col-num">№</th>
                    <th style="text-align: left; padding-left: 40px;">Отчетный период</th>
                    <th class="col-dates">Даты (от — до)</th>
                    <th class="col-actions">Управление</th>
                </tr>
            </thead>
            <tbody>
                @forelse($timesheets as $index => $ts)
                @php
                    $start = \Carbon\Carbon::parse($ts->start_date);
                    $end = \Carbon\Carbon::parse($ts->end_date);
                    $months = [
                        1 => 'ЯНВАРЬ', 2 => 'ФЕВРАЛЬ', 3 => 'МАРТ', 4 => 'АПРЕЛЬ', 5 => 'МАЙ', 6 => 'ИЮНЬ',
                        7 => 'ИЮЛЬ', 8 => 'АВГУСТ', 9 => 'СЕНТЯБРЬ', 10 => 'ОКТЯБРЬ', 11 => 'НОЯБРЬ', 12 => 'ДЕКАБРЬ'
                    ];
                @endphp
                {{-- Строка для ПК --}}
                <tr class="row-style">
                    <td class="num-cell cell-py">{{ $index + 1 }}</td>
                    <td class="month-cell cell-py">
                        {{ $months[$start->month] }} {{ $start->year }}
                    </td>
                    <td class="dates-cell cell-py">
                        {{ $start->format('d.m.Y') }} <span class="text-slate-300 mx-3">—</span> {{ $end->format('d.m.Y') }}
                    </td>
                    <td class="cell-py">
                        <div class="action-container">
                            <a href="{{ route('travel-timesheets.show', $ts) }}" class="btn-icon view" title="Открыть">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <form action="{{ route('travel-timesheets.destroy', $ts) }}" method="POST" onsubmit="return confirm('Удалить этот табель?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon delete" title="Удалить">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                {{-- Мобильная версия той же строки (через карточку) --}}
                <div class="mobile-card">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <span class="text-[10px] font-black text-slate-300 uppercase">#{{ $index + 1 }}</span>
                            <h3 class="text-lg font-black text-slate-800 uppercase leading-none">{{ $months[$start->month] }}</h3>
                            <p class="text-slate-400 font-bold text-xs mt-1">{{ $start->year }}</p>
                        </div>
                        <div class="flex gap-4">
                            <a href="{{ route('travel-timesheets.show', $ts) }}" class="text-blue-600 text-2xl">
                                <i class="fa-solid fa-circle-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-lg flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-500">{{ $start->format('d.m.Y') }} — {{ $end->format('d.m.Y') }}</span>
                        <form action="{{ route('travel-timesheets.destroy', $ts) }}" method="POST" onsubmit="return confirm('Удалить этот табель?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 text-lg ml-4">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>

                @empty
                <tr>
                    <td colspan="4" class="py-12 text-center text-slate-300 font-black uppercase text-xs tracking-widest">
                        Список пуст
                    </td>
                </tr>
                {{-- Для мобилки пустое состояние --}}
                <div class="mobile-card text-center py-10 text-slate-300 font-black uppercase text-[10px]">
                    Список пуст
                </div>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-8">
        @include('layouts.footer')
    </div>
</div>

@endsection
