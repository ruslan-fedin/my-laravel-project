@extends('layouts.app')

@section('title', 'Выезды — Список табелей')

@section('content')
<style>
    /* МАКСИМАЛЬНАЯ ШИРИНА */
    .index-page-wrapper {
        padding: 30px 40px;
        background-color: #f8fafc;
        min-height: calc(100vh - 64px);
        font-family: 'Inter', sans-serif;
    }

    /* ЗАГОЛОВОК */
    .page-title {
        font-size: 34px;
        font-weight: 950;
        text-transform: uppercase;
        letter-spacing: -0.03em;
        color: #0f172a;
    }

    /* ТАБЛИЦА НА ВСЮ ШИРИНУ */
    .card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
        overflow: hidden;
        margin-top: 30px;
        width: 100%;
    }

    .table-custom {
        width: 100%;
        border-collapse: collapse;
    }

    .table-custom thead th {
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.12em;
        padding: 16px 30px; /* Уменьшена высота шапки */
        background: #fbfcfd;
        border-bottom: 2px solid #f1f5f9;
    }

    /* КОЛОНКИ */
    .col-num { width: 70px; text-align: center; }
    .col-dates { width: 300px; text-align: center; }
    .col-actions { width: 180px; text-align: center; }

    .num-cell {
        text-align: center;
        color: #cbd5e1;
        font-weight: 900;
        font-size: 14px;
        border-right: 1px solid #f1f5f9;
    }

    .month-cell {
        padding-left: 40px;
        font-weight: 950;
        text-transform: uppercase;
        color: #1e293b;
        font-size: 16px;
    }

    .dates-cell {
        text-align: center;
        color: #475569;
        font-weight: 800;
        font-size: 14px;
        border-left: 1px solid #f1f5f9;
        border-right: 1px solid #f1f5f9;
        background-color: #fcfdfe;
    }

    /* КНОПКИ УПРАВЛЕНИЯ (Чуть меньше размер) */
    .action-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 25px;
    }

    .btn-icon {
        color: #94a3b8;
        font-size: 22px;
        background: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-icon:hover { transform: scale(1.15); }
    .btn-icon.view:hover { color: #2563eb; }
    .btn-icon.delete:hover { color: #ef4444; }

    /* КОМПАКТНАЯ ВЫСОТА СТРОК */
    .row-style { border-bottom: 1px solid #f1f5f9; }
    .row-style:hover { background: #f8fafc; }

    /* Ячейка с уменьшенным padding (высота строки) */
    .cell-py { padding-top: 14px; padding-bottom: 14px; }

    /* ФОРМА СОЗДАНИЯ */
    .create-box {
        background: white;
        padding: 16px 24px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        display: flex;
        gap: 15px;
        align-items: flex-end;
    }

    .input-date {
        height: 40px;
        border: 2px solid #f1f5f9;
        border-radius: 8px;
        padding: 0 12px;
        font-size: 13px;
        font-weight: 800;
        color: #1e293b;
        outline: none;
    }

    .btn-submit {
        height: 40px;
        background: #0f172a;
        color: white;
        padding: 0 25px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        transition: all 0.2s;
    }
    .btn-submit:hover { background: #000; }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="page-title">Выездные табели</h1>
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mt-1">Реестр периодов</p>
        </div>

        <form action="{{ route('travel-timesheets.store') }}" method="POST" class="create-box">
            @csrf
            <div class="flex flex-col">
                <span class="text-[9px] font-black text-slate-400 uppercase mb-1 ml-1">Начало:</span>
                <input type="date" name="start_date" required class="input-date">
            </div>
            <div class="flex flex-col">
                <span class="text-[9px] font-black text-slate-400 uppercase mb-1 ml-1">Конец:</span>
                <input type="date" name="end_date" required class="input-date">
            </div>
            <button type="submit" class="btn-submit">Создать</button>
        </form>
    </div>

    <div class="card">
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
                <tr class="row-style">
                    {{-- Высота строк регулируется через класс cell-py --}}
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
                @empty
                <tr>
                    <td colspan="4" class="py-12 text-center text-slate-300 font-black uppercase text-xs tracking-widest">
                        Список пуст
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

{{-- ПОДКЛЮЧЕНИЕ ПОДВАЛА ЧЕРЕЗ BLADE ШАБЛОН --}}
    @include('partials.footer')

</div> {{-- Закрытие index-page-wrapper --}}

@endsection

