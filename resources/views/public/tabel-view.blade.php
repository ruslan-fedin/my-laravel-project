<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Табель: {{ $timesheet->note ?? 'Просмотр' }}</title>
    <script src="{{ asset('vendor/tailwind.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fonts/inter/inter.css') }}">
    <style>
        body {
            background-color: #f1f5f9;
            padding: 40px 120px;
            font-family: 'Inter', sans-serif;
        }

        @media (max-width: 1024px) {
            body { padding: 10px 0px; }
            .card { border-radius: 0; border: none; }
            .header-section { padding: 0 15px; }
        }

        .table-container {
            overflow-x: auto;
            width: 100%;
            background: white;
            position: relative;
            -webkit-overflow-scrolling: touch;
        }

        table { border-collapse: separate; border-spacing: 0; width: 100%; }

        th, td {
            border-bottom: 1px solid #e2e8f0;
            border-right: 1px solid #f1f5f9;
            height: 60px;
            padding: 0;
            text-align: center;
        }

        /* ФИКСАЦИЯ */
        .col-sticky-num {
            position: sticky; left: 0; z-index: 50;
            width: 40px; min-width: 40px;
            background: #f8fafc !important;
            border-right: 1px solid #e2e8f0;
            font-size: 10px; font-weight: 900; color: #94a3b8;
        }

        .col-sticky-user {
            position: sticky; left: 40px; z-index: 40;
            width: 250px; min-width: 250px;
            background: white !important;
            text-align: left;
            padding: 8px 15px !important;
            box-shadow: 4px 0 10px -2px rgba(0,0,0,0.1);
            border-right: 3px solid #3b82f6 !important;
        }

        th.col-sticky-num, th.col-sticky-user { z-index: 60; background: #f8fafc !important; }

        /* ТЕКСТ */
        .fio-full { font-size: 13px; font-weight: 800; color: #1e293b; text-transform: uppercase; line-height: 1.1; display: block; }
        .post-label { font-size: 9px; font-weight: 700; color: #3b82f6; text-transform: uppercase; margin-top: 3px; display: block; opacity: 0.8; }

        /* ЯЧЕЙКИ ДНЕЙ */
        .day-cell { width: 48px; min-width: 48px; }
        .day-val { font-size: 15px; font-weight: 900; }

        /* Цвета дней недели */
        .weekday-name { font-size: 8px; font-weight: 800; text-transform: uppercase; color: #94a3b8; }
        .weekend-name { color: #e11d48; }
        .weekend-bg { background: #fff1f2 !important; }

        /* ЛЕГЕНДА */
        .status-pill {
            display: flex; align-items: center; gap: 8px;
            background: white; border: 1px solid #e2e8f0;
            padding: 6px 12px; border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<div class="header-section mb-6">
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tighter leading-none">Табель</h1>
            <p class="text-sm font-bold text-slate-400 uppercase mt-2">
                {{ \Carbon\Carbon::parse($timesheet->start_date)->translatedFormat('F Y') }}
            </p>
        </div>
        <div class="hidden md:block text-right">
            <p class="text-[9px] font-black text-slate-400 uppercase">Ответственный</p>
            <p class="text-xs font-bold text-slate-900 uppercase">{{ $timesheet->responsible_name ?? 'Федин Руслан Анатольевич' }}</p>
        </div>
    </div>
</div>

<div class="card bg-white border border-slate-200 shadow-sm overflow-hidden">
    <div class="table-container custom-scroll">
        <table>
            <thead>
                <tr>
                    <th class="col-sticky-num">№</th>
                    <th class="col-sticky-user">Сотрудник / Должность</th>
                    @foreach($dates as $date)
                        @php
                            $isWknd = $date->isWeekend();
                            $dayName = mb_substr($date->translatedFormat('D'), 0, 2);
                        @endphp
                        <th class="day-cell {{ $isWknd ? 'weekend-bg' : '' }}">
                            <div class="text-[13px] font-black {{ $isWknd ? 'text-rose-600' : 'text-slate-700' }}">{{ $date->format('d') }}</div>
                            <div class="weekday-name {{ $isWknd ? 'weekend-name' : '' }}">{{ $dayName }}</div>
                        </th>
                    @endforeach
                </tr>
            </thead>

<tbody>
    @foreach($employees as $emp)
    @php
        // Проверяем должность на "Бригадир" (без учета регистра и лишних пробелов)
        $isBrigadier = trim(mb_strtolower($emp->position->name ?? '')) === 'бригадир';
    @endphp
    <tr>
        {{-- Номер --}}
        <td class="col-sticky-num {{ $isBrigadier ? '!bg-rose-100' : '' }}">{{ $loop->iteration }}</td>

        {{-- Липкая колонка с ФИО и Должностью --}}
        <td class="col-sticky-user {{ $isBrigadier ? '!bg-rose-50' : '' }}">
            <span class="fio-full">{{ $emp->last_name }} {{ $emp->first_name }} {{ $emp->middle_name }}</span>

            {{-- Если бригадир — текст красный и жирный, иначе стандартный синий --}}
            <span class="post-label {{ $isBrigadier ? '!text-rose-600 font-black' : '' }}">
                {{ $emp->position->name ?? 'РАБОЧИЙ ОЗХ' }}
            </span>
        </td>

        @foreach($dates as $date)
            @php
                $dateStr = $date->format('Y-m-d');
                $item = $items[$emp->id][$dateStr] ?? null;
                $st = $item ? $statuses->where('id', $item->status_id)->first() : null;
            @endphp
            {{-- Ячейки дней --}}
            <td class="day-cell {{ $date->isWeekend() ? 'bg-rose-50/30' : '' }}"
                style="background-color: {{ $st->color ?? ($isBrigadier ? '#fff1f2' : '') }};
                       color: {{ $st ? 'white' : ($date->isWeekend() ? '#fda4af' : '#e2e8f0') }}">
                <span class="day-val">{{ $st->short_name ?? '' }}</span>
            </td>
        @endforeach
    </tr>
    @endforeach
</tbody>
        </table>
    </div>
</div>

{{-- ВОЗВРАЩЕННАЯ ЛЕГЕНДА --}}
<div class="header-section mt-8">
    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic">Условные обозначения:</h3>
    <div class="flex flex-wrap gap-3">
        @foreach($statuses as $s)
            <div class="status-pill">
                <div class="w-6 h-6 rounded flex items-center justify-center text-[11px] font-black text-white shadow-sm" style="background: {{ $s->color }}">
                    {{ $s->short_name }}
                </div>
                <div class="text-[10px] font-black text-slate-600 uppercase tracking-tight">{{ $s->name }}</div>
            </div>
        @endforeach
    </div>
</div>

</body>
</html>
