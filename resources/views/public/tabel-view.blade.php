<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Табель: {{ $timesheet->note ?? 'Просмотр' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
            padding: 20px 120px 40px 120px;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
        }
        @media (max-width: 1024px) { body { padding: 15px 12px; } }

        .card { background: white; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); margin-bottom: 1rem; overflow: hidden; }
        .table-container { overflow-x: auto; width: 100%; border-radius: 8px; background: white; -webkit-overflow-scrolling: touch; }

        table { border-collapse: separate; border-spacing: 0; width: 100%; }

        /* УМЕНЬШЕННАЯ ВЫСОТА СТРОК */
        th, td { border: 1px solid #e2e8f0; height: 44px; text-align: center; vertical-align: middle; }
        th { background: #f8fafc; font-weight: 800; text-transform: uppercase; color: #64748b; font-size: 9px; height: 48px; }

        .col-sticky-user {
            width: 170px; min-width: 170px; text-align: left; padding: 4px 10px;
            position: sticky; left: 0; z-index: 30; background: #fff;
            border-right: 3px solid #3b82f6;
        }
        th.col-sticky-user { background: #f8fafc; z-index: 40; }

        /* ТИПОГРАФИКА ФИО (ПОЛНОСТЬЮ И ЖИРНО) */
        .emp-f { font-size: 13px; font-weight: 900; color: #0f172a; text-transform: uppercase; line-height: 1; letter-spacing: -0.01em; }
        .emp-io { font-size: 10px; font-weight: 700; color: #475569; text-transform: uppercase; line-height: 1; margin-top: 1px; }
        .pos-text { font-size: 7px; font-weight: 800; color: #3b82f6; text-transform: uppercase; line-height: 1; margin-top: 2px; }

        .day-col { width: 38px; min-width: 38px; font-weight: 900; font-size: 13px; color: white; text-shadow: 0 1px 1px rgba(0,0,0,0.2); }

        .weekend-h { background-color: #fee2e2 !important; color: #991b1b !important; }
        .weekend-cell { background-color: #fff1f2 !important; }

        .summary-pill { display: flex; align-items: center; border-radius: 9999px; padding: 4px 12px; color: white; margin-bottom: 4px; }
        .summary-lab { font-size: 9px; font-weight: 900; text-transform: uppercase; }

        .custom-scroll::-webkit-scrollbar { height: 5px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body>

<div class="mb-5 px-1">
    <div class="flex flex-row justify-between items-center">
        <h1 class="text-4xl font-900 text-slate-900 uppercase tracking-tighter leading-none">ТАБЕЛЬ</h1>
        <div class="bg-slate-900 text-white px-3 py-2 rounded text-[10px] font-900 uppercase tracking-wider">
            {{ $timesheet->responsible_name ?? 'Федин Руслан Анатольевич' }}
        </div>
    </div>
    <p class="text-blue-600 font-900 text-[11px] uppercase tracking-widest mt-2">
        за {{ \Carbon\Carbon::parse($timesheet->start_date)->translatedFormat('F') }}
        с {{ \Carbon\Carbon::parse($timesheet->start_date)->format('d.m.y') }}
        по {{ \Carbon\Carbon::parse($timesheet->end_date)->format('d.m.y') }}
    </p>
</div>

{{-- ТАБЛИЦА --}}
<div class="card shadow-md">
    <div class="table-container custom-scroll">
        <table>
            <thead>
                <tr>
                    <th class="col-sticky-user">Сотрудник / Должность</th>
                    @foreach($dates as $date)
                        @php $dayName = mb_substr($date->translatedFormat('D'), 0, 2); @endphp
                        <th class="{{ $date->isWeekend() ? 'weekend-h' : '' }}">
                            <span class="text-base leading-none">{{ $date->format('d') }}</span><br>
                            <span class="text-[7px] font-black opacity-60 uppercase">{{ $dayName }}</span>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $emp)
                <tr>
                    <td class="col-sticky-user">
                        <div class="emp-f">{{ $emp->last_name }}</div>
                        <div class="emp-io">{{ $emp->first_name }} {{ $emp->middle_name }}</div>
                        <div class="pos-text">
                            @php
                                $pos = $emp->position;
                                if (is_string($pos) && str_contains($pos, '{')) {
                                    $posData = json_decode($pos); echo $posData->NAME ?? $posData->name ?? $pos;
                                } elseif (is_object($pos)) {
                                    echo $pos->NAME ?? $pos->name ?? '---';
                                } else { echo $pos->name ?? $pos ?? '---'; }
                            @endphp
                        </div>
                    </td>
                    @foreach($dates as $date)
                        @php
                            $dateStr = $date->format('Y-m-d');
                            $item = $items[$emp->id][$dateStr] ?? null;
                            $st = $item ? $statuses->where('id', $item->status_id)->first() : null;
                        @endphp
                        <td class="day-col {{ $date->isWeekend() ? 'weekend-cell' : '' }}"
                            style="background-color: {{ $st->color ?? '' }}">
                            {{ $st->short_name ?? '' }}
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- НИЖНЯЯ СВОДКА --}}
<div class="mt-4">
    <div class="card">
        <div class="bg-slate-900 text-white px-4 py-2 text-[10px] font-black uppercase tracking-widest">
            Условные обозначения
        </div>
        <div class="p-4 flex flex-wrap gap-2">
            @foreach($statuses as $s)
                <div class="summary-pill shadow-sm" style="background: {{ $s->color }}">
                    <div class="summary-lab">{{ $s->name }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>

</body>
</html>
