<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Табель — {{ $timesheet->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f1f5f9;
            padding: 20px 120px 40px 120px;
            font-family: 'Inter', system-ui, sans-serif;
            color: #1e293b;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            padding: 15px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .nav-links { display: flex; gap: 20px; }
        .nav-link {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            color: #64748b;
            transition: color 0.2s;
        }
        .nav-link:hover { color: #0f172a; }
        .nav-link.active { color: #3b82f6; }

        .table-container {
            width: 100%;
            border: 1px solid #e2e8f0;
            background: white;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            border-radius: 4px;
            overflow: hidden;
        }

        table { width: 100%; table-layout: fixed; border-collapse: collapse; }

        .col-num { width: 45px; cursor: pointer; }
        .col-fio { width: 350px; cursor: pointer; }
        .col-date { width: auto; }
        .col-comment { width: 180px; }
        .col-check { width: 40px; }
        .col-del { width: 45px; }

        th {
            background: #f8fafc;
            font-size: 11px;
            border: 1px solid #e2e8f0;
            padding: 5px 0 !important;
            font-weight: 900;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.02em;
            user-select: none;
        }

        .date-cell-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            line-height: 1.2;
        }
        .date-day-num { font-size: 12px; font-weight: 900; }
        .date-day-name { font-size: 8px; font-weight: 700; color: #64748b; }

        th.sortable:hover { background: #f1f5f9; }
        th i.sort-icon { font-size: 8px; margin-left: 5px; color: #cbd5e1; }

        tbody tr:hover { background-color: #f8fafc; }
        tbody tr.hidden-row { display: none !important; }

        td {
            padding: 0 !important;
            height: 52px;
            border: 1px solid #e2e8f0;
            vertical-align: middle !important;
        }

        /* ФИО И ДОЛЖНОСТЬ ПО ЛЕВОМУ КРАЮ */
        .fio-cell {
            text-align: left !important;
            padding: 5px 15px !important;
        }

        .fio-text {
            font-size: 14px;
            font-weight: 900;
            color: #0f172a;
            /* text-transform убран для корректного регистра */
            display: block;
            line-height: 1.2;
            text-align: left;
        }

        .pos-text {
            font-size: 11px;
            font-weight: 800;
            color: #3b82f6;
            text-transform: uppercase;
            display: block;
            text-align: left;
            margin-top: 2px;
        }

        .status-td {
            text-align: center !important;
        }

        .cell-select {
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            border: none !important;
            outline: none !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            height: 100% !important;
            cursor: pointer !important;
            font-weight: 900 !important;
            font-size: 12px !important;
            background: transparent !important;
            display: block !important;
            text-align: center !important;
            text-align-last: center !important;
        }

        .cell-select::-ms-expand { display: none; }

        .comment-area {
            width: 100%; height: 100%; font-size: 10px; border: none;
            outline: none; resize: none; background: transparent;
            padding: 4px 8px; display: block; line-height: 1.2;
        }

        .saving { background-color: #fef9c3 !important; }
        .weekend { background-color: #fff1f2 !important; color: #e11d48 !important; }
        .weekend .date-day-name { color: #e11d48 !important; }

        .btn-delete { color: #cbd5e1; transition: all 0.2s; }
        tr:hover .btn-delete { color: #f43f5e; }

        .row-checkbox {
            width: 16px; height: 16px; cursor: pointer;
            accent-color: #0f172a;
        }
    </style>
</head>
<body>

<nav class="nav-menu">
    <div class="nav-links">
        <a href="{{ route('timesheets.index') }}" class="nav-link {{ request()->routeIs('timesheets.*') ? 'active' : '' }}">Табели</a>
        <a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">Сотрудники</a>
        <a href="{{ route('positions.index') }}" class="nav-link {{ request()->routeIs('positions.*') ? 'active' : '' }}">Должности</a>
        <a href="{{ route('statuses.index') }}" class="nav-link {{ request()->routeIs('statuses.*') ? 'active' : '' }}">Статусы</a>
    </div>

    <div class="flex items-center gap-4">
        <span class="text-[10px] font-black text-slate-400 uppercase">{{ Auth::user()->name }}</span>
        <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="text-[10px] font-black text-rose-600 uppercase hover:text-rose-800 transition flex items-center gap-1">
                <i class="fas fa-sign-out-alt"></i> Выйти
            </button>
        </form>
    </div>
</nav>

@php
    $displayDate = \Carbon\Carbon::parse($timesheet->start_date);
    $start = \Carbon\Carbon::parse($timesheet->start_date);
    $end = \Carbon\Carbon::parse($timesheet->end_date);
    $dates = [];
    $tempStart = $start->copy();
    while ($tempStart <= $end) {
        $dates[] = $tempStart->copy();
        $tempStart->addDay();
    }

    $daysOfWeek = [1 => 'Пн', 2 => 'Вт', 3 => 'Ср', 4 => 'Чт', 5 => 'Пт', 6 => 'Сб', 0 => 'Вс'];
    $monthName = [
        1 => 'ЯНВАРЬ', 2 => 'ФЕВРАЛЬ', 3 => 'МАРТ', 4 => 'АПРЕЛЬ', 5 => 'МАЙ', 6 => 'ИЮНЬ',
        7 => 'ИЮЛЬ', 8 => 'АВГУСТ', 9 => 'СЕНТЯБРЬ', 10 => 'ОКТЯБРЬ', 11 => 'НОЯБРЬ', 12 => 'ДЕКАБРЬ'
    ];

    // Изменено на MB_CASE_TITLE для формата "Иванов Иван Иванович"
    $formatName = function($l, $f, $m) {
        return mb_convert_case(trim("$l $f $m"), MB_CASE_TITLE, "UTF-8");
    };

    $sortedEmployees = $employees->sortBy('last_name');
@endphp

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-black uppercase tracking-tighter text-slate-900">
            {{ $monthName[$displayDate->month] }} {{ $displayDate->year }}
        </h1>
        <p class="text-slate-500 text-xs font-bold uppercase tracking-widest mt-1">
            ПЕРИОД: {{ $displayDate->format('d.m.Y') }} — {{ $end->format('d.m.Y') }}
        </p>
    </div>

    <div class="flex gap-2">
        <a href="{{ route('timesheets.excel', $timesheet) }}" class="px-4 py-2 bg-emerald-600 text-white rounded font-bold text-[10px] uppercase hover:bg-emerald-700 transition">Excel</a>
        <a href="{{ route('timesheets.pdf', $timesheet) }}" class="px-4 py-2 bg-rose-600 text-white rounded font-bold text-[10px] uppercase hover:bg-rose-700 transition">PDF</a>
    </div>
</div>

<div class="bg-white p-4 border border-slate-200 rounded-lg mb-4 shadow-sm">
    <div class="flex items-center gap-6 overflow-x-auto whitespace-nowrap">
        <div class="flex items-end gap-3 border-r border-slate-200 pr-6">
            <form action="{{ route('timesheets.add-employee', $timesheet) }}" method="POST" class="flex gap-2 items-end">
                @csrf
                <div class="flex flex-col">
                    <span class="text-[9px] font-black text-slate-400 uppercase mb-1 ml-1">Добавить одного:</span>
                    <select name="employee_id" required class="border border-slate-300 rounded h-9 px-2 text-xs min-w-[200px] outline-none">
                        <option value="">Выбор...</option>
                        @foreach(\App\Models\Employee::where('is_active', true)->orderBy('last_name')->get() as $e)
                            <option value="{{ $e->id }}">{{ $formatName($e->last_name, $e->first_name, $e->middle_name) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-slate-800 text-white px-3 h-9 rounded font-bold text-[10px] uppercase hover:bg-black transition">ОК</button>
            </form>

            <form action="{{ route('timesheets.fill-active', $timesheet) }}" method="POST">
                @csrf
                <div class="flex flex-col">
                    <span class="text-[9px] font-black text-slate-400 uppercase mb-1 ml-1">Групповое:</span>
                    <button type="submit" class="h-9 px-4 border border-indigo-600 text-indigo-700 rounded font-bold text-[10px] uppercase hover:bg-indigo-600 hover:text-white transition">
                       Добавить всех активных
                    </button>
                </div>
            </form>
        </div>

        <div class="flex items-end gap-2">
            <div class="flex flex-col">
                <span class="text-[9px] font-black text-orange-600 uppercase mb-1 ml-1">Установка статуса (интервал):</span>
                <div class="flex gap-1">
                    <input type="date" id="mass_start" value="{{ $timesheet->start_date }}" class="border border-slate-300 rounded h-9 px-2 text-[10px] outline-none">
                    <input type="date" id="mass_end" value="{{ $timesheet->end_date }}" class="border border-slate-300 rounded h-9 px-2 text-[10px] outline-none">
                    <select id="mass_status" class="border border-slate-300 rounded h-9 px-2 text-[10px] font-bold text-rose-600 min-w-[80px] outline-none">
                        <option value="">Очистить</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st->id }}">{{ $st->short_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-col">
                <span class="text-[9px] font-black text-slate-400 uppercase mb-1 ml-1">Применить для:</span>
                <select id="mass_employee" class="border border-slate-300 rounded h-9 px-2 text-[10px] bg-white min-w-[180px] outline-none">
                    <option value="all">ПРИМЕНИТЬ КО ВСЕМ СРАЗУ</option>
                    @foreach($sortedEmployees as $emp)
                        <option value="{{ $emp->id }}">{{ $formatName($emp->last_name, $emp->first_name, $emp->middle_name) }}</option>
                    @endforeach
                </select>
            </div>

            <button onclick="runMassAction({{ $timesheet->id }})" class="bg-orange-500 text-white px-5 h-9 rounded font-bold text-[10px] uppercase hover:bg-orange-600 transition shadow-sm">
                Выполнить
            </button>
        </div>
    </div>
</div>

<div class="mb-4">
    <div class="relative w-full">
        <i class="fas fa-search absolute left-4 top-3.5 text-slate-400 text-xs"></i>
        <input type="text" id="fio-filter" onkeyup="filterTable()" placeholder="ПОИСК ПО ФАМИЛИИ ИМЕНИ ОТЧЕСТВУ..."
            class="w-full bg-white border border-slate-200 rounded-lg h-11 pl-11 pr-4 text-[11px] font-black uppercase tracking-[0.2em] outline-none focus:border-slate-900 transition shadow-sm">
    </div>
</div>

<div class="table-container">
    <table id="timesheet-table">
        <thead>
            <tr>
                <th class="col-num sortable" onclick="sortTable(0, 'int')">№ <i class="fas fa-sort sort-icon"></i></th>
                <th class="col-fio sortable text-left px-5" onclick="sortTable(1, 'str')">Сотрудник / Должность <i class="fas fa-sort sort-icon"></i></th>
                @foreach($dates as $date)
                    <th class="col-date {{ $date->isWeekend() ? 'weekend' : '' }}">
                        <div class="date-cell-header">
                            <span class="date-day-num">{{ $date->format('d') }}</span>
                            <span class="date-day-name">{{ $daysOfWeek[$date->dayOfWeek] }}</span>
                        </div>
                    </th>
                @endforeach
                <th class="col-comment">Примечание</th>
                <th class="col-check text-center"><input type="checkbox" id="master-check" onclick="toggleAllChecks(this)" class="row-checkbox"></th>
                <th class="col-del">Удл.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sortedEmployees as $emp)
            @php $fullName = $formatName($emp->last_name, $emp->first_name, $emp->middle_name); @endphp
            <tr data-emp-id="{{ $emp->id }}" data-search="{{ mb_strtolower($fullName) }}">
                <td class="text-center font-mono text-[10px] text-slate-400 border-r" data-val="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                <td class="fio-cell border-r" data-val="{{ $fullName }}">
                    <span class="fio-text">{{ $fullName }}</span>
                    <span class="pos-text">{{ $emp->position->name ?? '---' }}</span>
                </td>
                @foreach($dates as $date)
                    @php
                        $dateStr = $date->format('Y-m-d');
                        $item = $items->get($emp->id)?->where('date', $dateStr)->first();
                        $st = $item ? $statuses->where('id', $item->status_id)->first() : null;
                        $color = $st ? $st->color : 'transparent';
                    @endphp
                    <td class="status-td" style="background-color: {{ $color }};" data-date="{{ $dateStr }}">
                        <select onchange="saveCell(this, {{ $timesheet->id }}, {{ $emp->id }}, '{{ $dateStr }}')"
                            class="cell-select"
                            style="color: {{ ($color == 'transparent' || $color == '#ffffff') ? '#1e293b' : '#fff' }};">
                            <option value=""> </option>
                            @foreach($statuses as $s)
                                <option value="{{ $s->id }}" data-color="{{ $s->color }}" {{ $item && $item->status_id == $s->id ? 'selected' : '' }}>{{ $s->short_name }}</option>
                            @endforeach
                        </select>
                    </td>
                @endforeach
                <td class="border-r">
                    <textarea onblur="saveComment(this, {{ $timesheet->id }}, {{ $emp->id }})" class="comment-area">{{ $items->get($emp->id)?->first()?->comment ?? '' }}</textarea>
                </td>
                <td class="text-center border-r">
                    <input type="checkbox" class="row-checkbox emp-check">
                </td>
                <td class="text-center">
                    <form action="{{ route('timesheets.remove-employee', [$timesheet, $emp]) }}" method="POST" onsubmit="return confirm('Удалить?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-delete"><i class="fas fa-trash-alt text-xs"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-6 flex flex-wrap gap-5 p-4 bg-white border border-slate-200 rounded-lg shadow-sm">
    @foreach($statuses as $status)
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 border border-slate-300 rounded-sm" style="background-color: {{ $status->color }}"></div>
            <span class="text-[10px] font-bold text-slate-700 uppercase">{{ $status->short_name }} — {{ $status->name }}</span>
        </div>
    @endforeach
</div>
{{-- ПОДКЛЮЧЕНИЕ ПОДВАЛА --}}
<div class="mt-8">
    @include('partials.footer')
</div>
<script>
    function filterTable() {
        const query = document.getElementById('fio-filter').value.toLowerCase();
        const rows = document.querySelectorAll('#timesheet-table tbody tr');
        rows.forEach(row => {
            const name = row.getAttribute('data-search');
            row.classList.toggle('hidden-row', !name.includes(query));
        });
    }

    function toggleAllChecks(master) {
        document.querySelectorAll('.emp-check').forEach(c => c.checked = master.checked);
    }

    function sortTable(colIndex, type) {
        const table = document.getElementById("timesheet-table");
        const tbody = table.tBodies[0];
        const rows = Array.from(tbody.rows);
        const isAsc = table.dataset.sortDir !== "asc";
        rows.sort((a, b) => {
            let valA = a.cells[colIndex].getAttribute('data-val') || a.cells[colIndex].innerText;
            let valB = b.cells[colIndex].getAttribute('data-val') || b.cells[colIndex].innerText;
            if (type === 'int') return isAsc ? parseInt(valA) - parseInt(valB) : parseInt(valB) - parseInt(valA);
            return isAsc ? valA.localeCompare(valB) : valB.localeCompare(valA);
        });
        table.dataset.sortDir = isAsc ? "asc" : "desc";
        tbody.append(...rows);
    }

    async function saveCell(select, tsId, empId, date) {
        const td = select.closest('td');
        const color = select.options[select.selectedIndex].getAttribute('data-color') || 'transparent';
        td.classList.add('saving');
        try {
            await fetch('{{ route('timesheets.save-item') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ timesheet_id: tsId, employee_id: empId, date: date, status_id: select.value })
            });
            td.style.backgroundColor = color;
            select.style.color = (color === 'transparent' || color === '#ffffff') ? '#1e293b' : '#fff';
        } catch(e) {}
        setTimeout(() => td.classList.remove('saving'), 400);
    }

    async function runMassAction(tsId) {
        const start = document.getElementById('mass_start').value;
        const end = document.getElementById('mass_end').value;
        const statusId = document.getElementById('mass_status').value;
        const targetEmpId = document.getElementById('mass_employee').value;

        let rowsToApply = [];
        if (targetEmpId === 'all') {
            rowsToApply = document.querySelectorAll('#timesheet-table tbody tr');
        } else {
            const row = document.querySelector(`#timesheet-table tbody tr[data-emp-id="${targetEmpId}"]`);
            if(row) rowsToApply = [row];
        }

        if(rowsToApply.length === 0) return;
        if(!confirm(`Установить статус для ${rowsToApply.length} чел.?`)) return;

        for (let row of rowsToApply) {
            const empId = row.getAttribute('data-emp-id');
            const cells = row.querySelectorAll('td[data-date]');
            for (let cell of cells) {
                const date = cell.getAttribute('data-date');
                if (date >= start && date <= end) {
                    const select = cell.querySelector('select');
                    if (select.value !== statusId) {
                        select.value = statusId;
                        await saveCell(select, tsId, empId, date);
                    }
                }
            }
        }
    }

    async function saveComment(textarea, tsId, empId) {
        textarea.classList.add('saving');
        await fetch('{{ route('timesheets.save-comment') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ timesheet_id: tsId, employee_id: empId, comment: textarea.value })
        });
        setTimeout(() => textarea.classList.remove('saving'), 400);
    }
</script>
</body>
</html>
