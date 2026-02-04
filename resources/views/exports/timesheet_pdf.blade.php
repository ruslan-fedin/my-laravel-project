<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        /* Принудительная установка шрифта для всего документа */
        @page { margin: 1cm; }

        * {
            font-family: 'DejaVu Sans', sans-serif;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            color: #000;
        }

        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { text-transform: uppercase; font-size: 14px; margin: 0 0 5px 0; }
        .header p { font-size: 10px; margin: 0; }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* Фиксирует ширину колонок */
        }

        /* ВАЖНО: Устанавливаем font-size для ВСЕХ элементов таблицы.
           Убираем любые вложенные стили.
        */
        th, td {
            border: 0.5pt solid #000;
            padding: 4px 2px;
            vertical-align: middle;
            font-size: 7pt !important; /* Жестко заданный размер в пунктах */
            line-height: 9pt;
            overflow: hidden;
        }

        thead { background-color: #f0f0f0; }
        th { font-weight: bold; text-transform: uppercase; }

        /* Четкие размеры колонок в процентах или пунктах для предсказуемости */
        .col-num { width: 25pt; text-align: center; }
        .col-fio { width: 140pt; text-align: left; padding-left: 4pt; }
        .col-pos { width: 90pt; text-align: left; padding-left: 4pt; }
        .col-date { width: 20pt; text-align: center; font-weight: bold; }
        .col-note { width: auto; text-align: left; padding-left: 4pt; }

        .weekend { background-color: #fca5a5; }

        /* Убираем любые стили у переносов строк */
        br { line-height: 0; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Табель учета рабочего времени</h2>
        <p>Период: {{ \Carbon\Carbon::parse($timesheet->start_date)->format('d.m.Y') }} — {{ \Carbon\Carbon::parse($timesheet->end_date)->format('d.m.Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-num">№</th>
                <th class="col-fio">Фамилия Имя Отчество</th>
                <th class="col-pos">Должность</th>
                @foreach($dates as $date)
                    <th class="col-date {{ $date->isWeekend() ? 'weekend' : '' }}">
                        {{ $date->format('d') }}
                    </th>
                @endforeach
                <th class="col-note">Примечание</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $emp)
                @php $empItems = $items->get($emp->id); @endphp
                <tr>
                    <td class="col-num">{{ $loop->iteration }}</td>
                    <td class="col-fio">
                        {{-- Выводим текст напрямую в td, без div и span --}}
                        {{ $emp->last_name }} {{ $emp->first_name }} {{ $emp->middle_name }}
                    </td>
                    <td class="col-pos">
                        {{ $emp->position->name }}
                    </td>
                    @foreach($dates as $date)
                        @php
                            $dateStr = $date->format('Y-m-d');
                            $item = $empItems?->where('date', $dateStr)->first();
                            $st = $item ? $statuses->where('id', $item->status_id)->first() : null;
                        @endphp
                        <td class="col-date" style="background-color: {{ $st ? $st->color : '#ffffff' }}; color: {{ $st ? '#ffffff' : '#000000' }};">
                            {{ $st ? $st->short_name : '' }}
                        </td>
                    @endforeach
                    <td class="col-note">
                        {{ $empItems?->first()?->comment ?? '' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
