<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Табель: {{ $timesheet->title }}</title>

<script src="{{ asset('vendor/tailwind.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('fonts/inter/inter.css') }}">
<script src="{{ asset('vendor/xlsx.bundle.js') }}"></script>


<style>
        body { background-color: #f8fafc; padding: 0 40px 40px 40px; font-family: 'Inter', sans-serif; color: #1e293b; }
        @media (max-width: 768px) {
            body { padding: 0 12px 20px 12px; }
            .col-name { width: 180px !important; }
        }
        .card { background: white; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); margin-bottom: 1rem; overflow: hidden; }
        .table-container { overflow-x: auto; width: 100%; border-radius: 8px; position: relative; background: white; }
        table { border-collapse: separate; border-spacing: 0; table-layout: fixed; width: 100%; }
        th, td { border: 1px solid #e2e8f0; height: 44px; text-align: center; font-size: 11px; }
        th { background: #f1f5f9; font-weight: 800; text-transform: uppercase; color: #475569; padding: 4px; }
        .col-num { width: 35px; }
        .col-name { width: 320px; text-align: left; padding: 8px 12px; position: sticky; left: 0; z-index: 30; background: #fff; border-right: 3px solid #3b82f6; }
        .emp-fullname { font-size: 13px; font-weight: 800; line-height: 1.2; color: #0f172a; white-space: normal; word-break: break-word; }
        .emp-position { font-size: 9px; font-weight: 700; text-transform: uppercase; color: #0a66e7; margin-top: 2px; }
        .day-col { width: 40px !important; min-width: 40px; max-width: 40px; }
        .col-extra { width: 150px; }
        .col-check { width: 45px; }
        .col-action { width: 45px; }
        .weekend-header { background-color: #fca5a5 !important; color: #7f1d1d !important; }
        .weekend-cell { background-color: #fecaca; }
        .status-select {
            width: 100%;
            height: 100%;
            border: none;
            background: transparent;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            text-align: center;
            text-align-last: center;
            font-weight: 900;
            cursor: pointer;
            outline: none;
            font-size: 13px;
            display: block;
            padding: 0;
        }
        .status-select::-ms-expand { display: none; }

        textarea { width: 100%; height: 100%; border: none; background: transparent; resize: none; font-size: 11px; padding: 6px; outline: none; }
        .hidden-day { display: none !important; }
        .filter-input { height: 40px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0 12px; font-size: 13px; font-weight: 600; outline: none; }
        .summary-pill-grid { display: flex; flex-wrap: wrap; gap: 8px; padding: 12px; }
        .summary-pill { display: flex; align-items: center; border-radius: 9999px; padding: 5px 14px 5px 6px; color: white; }
        .summary-pill-val { background: rgba(255,255,255,0.3); border-radius: 9999px; min-width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-weight: 900; margin-right: 10px; }
        .summary-pill-lab { font-size: 10px; font-weight: 800; text-transform: uppercase; }


    /* Анимация пульсации при сохранении */
@keyframes pulse-saving {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.cell-saving {
    animation: pulse-saving 0.6s infinite ease-in-out;
    pointer-events: none; /* Блокируем нажатия во время сохранения */
}

/* Эффект успешного сохранения */
.cell-success {
    position: relative;
}

.cell-success::after {
    content: '';
    position: absolute;
    inset: 0;
    border: 2px solid #22c55e; /* Зеленый ободок */
    border-radius: 4px;
    pointer-events: none;
    animation: success-fade 1s forwards;
}

@keyframes success-fade {
    0% { opacity: 1; transform: scale(1); }
    100% { opacity: 0; transform: scale(1.1); }
}

/* Плавный переход цвета ячейки */
.day-col {
    transition: background-color 0.4s ease, transform 0.2s ease;
}

.status-select {
    transition: color 0.3s ease;
}


    </style>
</head>
<body>

@include('layouts.navigation')

@php
    $start = \Carbon\Carbon::parse($timesheet->start_date);
    $end = \Carbon\Carbon::parse($timesheet->end_date);
    $dates = [];
    for($d = $start->copy(); $d <= $end; $d->addDay()) { $dates[] = $d->copy(); }
    $daysMap = [1=>'Пн', 2=>'Вт', 3=>'Ср', 4=>'Чт', 5=>'Пт', 6=>'Сб', 0=>'Вс'];
    $addedIds = $employees->pluck('id')->toArray();
    $formatFullFio = function($emp) {
        return trim("{$emp->last_name} {$emp->first_name} {$emp->middle_name}");
    };
@endphp

<div class="mt-6 mb-4 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
    <div>
        <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Редактор табеля</h1>
        <p class="text-slate-500 font-bold text-xs uppercase">{{ $start->translatedFormat('F Y') }}</p>
    </div>
    <div class="flex gap-2 w-full md:w-auto">
        <button onclick="exportToExcel()" class="bg-emerald-600 text-white px-5 h-11 rounded-lg font-black text-[10px] uppercase flex items-center justify-center gap-2 hover:bg-emerald-700 transition-all">
            <i class="fa-solid fa-file-excel"></i> <span>Экспорт Excel</span>
        </button>
        <div id="modeIndicator" class="hidden px-4 h-11 bg-blue-600 text-white rounded-lg text-[9px] font-black uppercase flex items-center">Фокус дня</div>
    </div>
</div>

{{-- БЛОК ФИЛЬТРОВ И ДОБАВЛЕНИЯ --}}
<div class="card p-3 bg-slate-50/50">
    <div class="flex flex-col xl:flex-row gap-4">
        <div class="flex flex-col sm:flex-row gap-2">
            <form action="/travel-timesheets/{{ $timesheet->id }}/add-employee" method="POST" class="flex gap-2">
                @csrf
               <select name="employee_id" class="filter-input w-64" required>
    <option value="">+ Выбрать сотрудника полностью...</option>
    @foreach($allAvailableEmployees as $e)
        {{-- Добавляем проверку на активность сотрудника --}}
        @if($e->is_active && !in_array($e->id, $addedIds))
            <option value="{{ $e->id }}">{{ $formatFullFio($e) }}</option>
        @endif
    @endforeach
</select>
                <button type="submit" class="bg-blue-600 text-white px-4 rounded-lg font-bold text-[10px] uppercase">Ок</button>
            </form>
            <form action="/travel-timesheets/{{ $timesheet->id }}/add-all" method="POST">
                @csrf
                <button type="submit" class="w-full bg-slate-800 text-white px-4 h-10 rounded-lg font-bold text-[10px] uppercase ">Добавить всех активных</button>
            </form>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 flex-1">
            <div class="relative flex-1">
                <i class="fa-solid fa-search absolute left-3 top-3.5 text-slate-400 text-xs"></i>
                <input type="text" id="tableSearch" class="filter-input pl-9 w-full" placeholder="Поиск (ФИО полностью)...">
            </div>
            <div class="flex gap-2">
                <select id="filterDate" class="filter-input w-32 border-blue-200 border-2">
                    <option value="">Весь месяц</option>
                    @foreach($dates as $date)
                        <option value="{{ $date->format('Y-m-d') }}">{{ $date->format('d.m') }}</option>
                    @endforeach
                </select>
                <select id="filterStatus" class="filter-input w-32">
                    <option value="">Статус...</option>
                    @foreach($statuses as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                </select>
                <button onclick="resetFilters()" class="px-4 bg-slate-200 rounded-lg font-black text-[9px] uppercase hover:bg-slate-300">Сброс</button>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 border border-slate-200 rounded-lg shadow-sm mt-4">
    <h3 class="text-sm font-black text-slate-900 uppercase mb-4">Массовая установка статусов</h3>

    <div class="flex flex-wrap items-end gap-4">
        {{-- Интервал дат --}}
        <div class="flex flex-col">
            <span class="text-[9px] font-black text-orange-600 uppercase mb-1 ml-1">Период:</span>
            <div class="flex gap-1">
                <input type="date" id="mass_start" value="{{ $timesheet->start_date }}" class="border border-slate-300 rounded h-10 px-3 text-sm outline-none">
                <input type="date" id="mass_end" value="{{ $timesheet->end_date }}" class="border border-slate-300 rounded h-10 px-3 text-sm outline-none">
            </div>
        </div>

        {{-- Выбор статуса --}}
        <div class="flex flex-col">
            <span class="text-[9px] font-black text-orange-600 uppercase mb-1 ml-1">Статус:</span>
            <select id="mass_status" class="border border-slate-300 rounded h-10 px-3 text-sm font-bold text-rose-600 min-w-[120px] outline-none">
                <option value="">Очистить</option>
                @foreach($statuses as $st)
                    <option value="{{ $st->id }}">{{ $st->short_name }} ({{ $st->name }})</option>
                @endforeach
            </select>
        </div>

        {{-- Выбор сотрудников --}}
        <div class="flex flex-col">
            <span class="text-[9px] font-black text-slate-400 uppercase mb-1 ml-1">Применить для:</span>
            <select id="mass_employee" class="border border-slate-300 rounded h-10 px-3 text-sm bg-white min-w-[250px] outline-none">
    <option value="all">ПРИМЕНИТЬ КО ВСЕМ СРАЗУ</option>
    @foreach($employees as $emp)
        {{-- Показываем только активных --}}
        @if($emp->is_active)
            <option value="{{ $emp->id }}">{{ $emp->last_name }} {{ $emp->first_name }} {{ $emp->middle_name }}</option>
        @endif
    @endforeach
</select>
        </div>

        {{-- Кнопка запуска --}}
        <button onclick="runMassAction({{ $timesheet->id }})" class="bg-orange-500 text-white px-6 h-10 rounded font-bold text-[11px] uppercase hover:bg-orange-600 transition shadow-md">
            Выполнить установку
        </button>
    </div>
</div>


</div>






{{-- ОСНОВНАЯ ТАБЛИЦА --}}
<div class="card shadow-sm">
    <div class="table-container">
        <table id="mainTable">
            <thead>
                <tr>
                    <th class="col-num">№</th>
                    <th class="col-name cursor-pointer hover:bg-slate-200 transition-colors" onclick="toggleSortFio()">
                        Сотрудник (ФИО полностью) <i class="fa-solid fa-sort ml-1 opacity-40"></i>
                    </th>
                    @foreach($dates as $date)
                        <th class="day-col {{ $date->isWeekend() ? 'weekend-header' : '' }}" data-day-header="{{ $date->format('Y-m-d') }}">
                            {{ $date->format('d') }}<br><span class="text-[7px] {{ $date->isWeekend() ? 'font-black text-red-900' : 'opacity-40' }}">{{ $daysMap[$date->dayOfWeek] }}</span>
                        </th>
                    @endforeach
                    <th class="col-extra extra-col">Примечание</th>
                    <th class="col-check extra-col">Выбор</th>
                    <th class="col-action extra-col">Удл.</th>
                </tr>
            </thead>
            <tbody>
    @foreach($employees as $index => $emp)
    @php
        $fullName = $formatFullFio($emp);
        // Проверка на должность Бригадир
        $isBrigadier = ($emp->position->name ?? '') === 'Бригадир';
    @endphp
    <tr class="employee-row"
        data-fio="{{ $fullName }}"
        data-emp-id="{{ $emp->id }}"
        {{-- Если бригадир, подсвечиваем всю строку легким красным фоном --}}
        @if($isBrigadier) style="background-color: #fff1f2;" @endif>

        <td class="text-slate-400 font-mono text-[9px] {{ $isBrigadier ? 'bg-rose-50' : 'bg-white' }}">{{ $index + 1 }}</td>

        <td class="col-name">
            <div class="emp-fullname">{{ $fullName }}</div>
            {{-- Выделяем текст должности красным, если это Бригадир --}}
            <div class="emp-position {{ $isBrigadier ? 'text-red-600 font-bold' : '' }}">
                {{ $emp->position->name ?? '---' }}
            </div>
        </td>

        @foreach($dates as $date)
            @php
                $dateStr = $date->format('Y-m-d');
                $item = ($items[$emp->id] ?? collect())->where('date', $dateStr)->first();
                $status = $statuses->where('id', $item->status_id ?? null)->first();
                $isWeekend = $date->isWeekend();
                $bgColor = $status ? $status->color : ($isWeekend ? '#fecaca' : '');
            @endphp
            <td class="day-col {{ $isWeekend ? 'weekend-cell' : '' }}"
                style="background-color: {{ $bgColor }};"
                data-date="{{ $dateStr }}">
                <select class="status-select" onchange="saveStatus(this, '{{ $emp->id }}', '{{ $dateStr }}')" style="color: {{ $status ? '#fff' : '' }}">
                    <option value=""></option>
                    @foreach($statuses as $s)
                        <option value="{{ $s->id }}" data-color="{{ $s->color }}" data-name="{{ $s->name }}" {{ ($item->status_id ?? '') == $s->id ? 'selected' : '' }}>{{ $s->short_name }}</option>
                    @endforeach
                </select>
            </td>
        @endforeach

        <td class="extra-col">
            <textarea onblur="saveComment('{{ $emp->id }}', this.value)">{{ ($items[$emp->id] ?? collect())->first()->comment ?? '' }}</textarea>
        </td>
        <td class="extra-col text-center">
            <input type="checkbox" name="ids[]" value="{{ $emp->id }}" class="w-4 h-4">
        </td>
        <td class="extra-col text-center">
            <button type="button" onclick="deleteEmp('{{ $emp->id }}')" class="text-slate-300 hover:text-red-500">
                <i class="fa-solid fa-circle-xmark fa-lg"></i>
            </button>
        </td>
    </tr>
    @endforeach
</tbody>
        </table>
    </div>
</div>

{{-- БЛОК ПАРАМЕТРОВ ВЫЕЗДА ДЛЯ TELEGRAM --}}
<div class="card bg-gray-50 border-l-4 border-blue-600 mb-4 shadow-sm">
    <div class="flex justify-between items-center p-4 cursor-pointer hover:bg-gray-100 transition-colors" onclick="toggleTgDetails()">
        <div class="flex items-center gap-3">
            <div class="text-[11px] font-black uppercase text-blue-700 tracking-widest">
                <i class="fa-solid fa-paper-plane mr-2"></i> Детали выезда (Telegram)
            </div>
            <span id="tgDetailsBadge" class="bg-emerald-100 text-emerald-600 text-[10px] font-bold px-2 py-0.5 rounded uppercase">Настройка</span>
        </div>
        <i id="tgDetailsChevron" class="fa-solid fa-chevron-down text-blue-600 transition-transform" style="transform: rotate(180deg)"></i>
    </div>

    <div id="tgDetailsContent" class="border-t border-gray-200 p-4 bg-white">

        {{-- 🔹 СТАНДАРТНЫЕ ПОЛЯ (Сетка 1) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            {{-- Мастер --}}
            <div class="p-3 border rounded-lg bg-white shadow-sm">
                <label class="flex items-center gap-2 mb-2 cursor-pointer">
                    <input type="checkbox" id="use_tgMaster" class="w-4 h-4 accent-blue-600" checked>
                    <span class="text-[11px] font-black text-gray-600 uppercase flex items-center gap-1">
                        <i class="fa-solid fa-user text-gray-400"></i> Мастер
                    </span>
                </label>
                <input type="text" id="tgMaster" class="w-full text-[12px] border-gray-300 rounded bg-gray-50 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="ФИО Мастера">
            </div>
            {{-- Место работы --}}
            <div class="p-3 border rounded-lg bg-white shadow-sm">
                <label class="flex items-center gap-2 mb-2 cursor-pointer">
                    <input type="checkbox" id="use_tgWorkLocation" class="w-4 h-4 accent-blue-600" checked>
                    <span class="text-[11px] font-black text-gray-600 uppercase flex items-center gap-1">
                        <i class="fa-solid fa-location-dot text-gray-400"></i> Место работы
                    </span>
                </label>
                <input type="text" id="tgWorkLocation" class="w-full text-[12px] border-gray-300 rounded bg-gray-50 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="ДЕО-3 Северное Бутово">
            </div>
            {{-- Вид работы --}}
            <div class="p-3 border rounded-lg bg-white shadow-sm">
                <label class="flex items-center gap-2 mb-2 cursor-pointer">
                    <input type="checkbox" id="use_tgWorkType" class="w-4 h-4 accent-blue-600" checked>
                    <span class="text-[11px] font-black text-gray-600 uppercase flex items-center gap-1">
                        <i class="fa-solid fa-gear text-gray-400"></i> Вид работы
                    </span>
                </label>
                <input type="text" id="tgWorkType" class="w-full text-[12px] border-gray-300 rounded bg-gray-50 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="Покос газона">
            </div>
            {{-- Выезд --}}
            <div class="p-3 border rounded-lg bg-white shadow-sm">
                <label class="flex items-center gap-2 mb-2 cursor-pointer">
                    <input type="checkbox" id="use_tgDeparture" class="w-4 h-4 accent-blue-600" checked>
                    <span class="text-[11px] font-black text-gray-600 uppercase flex items-center gap-1">
                        <i class="fa-solid fa-clock text-gray-400"></i> Выезд
                    </span>
                </label>
                <input type="text" id="tgDeparture" class="w-full text-[12px] border-gray-300 rounded bg-gray-50 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="с 75 в 05:00">
            </div>
        </div>

        {{-- 🔹 СТАНДАРТНЫЕ ПОЛЯ (Сетка 2) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            {{-- Транспорт --}}
            <div class="p-3 border rounded-lg bg-white shadow-sm">
                <label class="flex items-center gap-2 mb-2 cursor-pointer">
                    <input type="checkbox" id="use_tgTransport" class="w-4 h-4 accent-blue-600">
                    <span class="text-[11px] font-black text-gray-600 uppercase flex items-center gap-1">
                        <i class="fa-solid fa-truck text-gray-400"></i> Транспорт
                    </span>
                </label>
                <input type="text" id="tgTransport" class="w-full text-[12px] border-gray-300 rounded bg-gray-50 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="Газель 445 Автобус 470">
            </div>
            {{-- Инструмент --}}
            <div class="p-3 border rounded-lg bg-white shadow-sm">
                <label class="flex items-center gap-2 mb-2 cursor-pointer">
                    <input type="checkbox" id="use_tgInventory" class="w-4 h-4 accent-blue-600" checked>
                    <span class="text-[11px] font-black text-gray-600 uppercase flex items-center gap-1">
                        <i class="fa-solid fa-screwdriver-wrench text-gray-400"></i> Инструмент
                    </span>
                </label>
                <input type="text" id="tgInventory" class="w-full text-[12px] border-gray-300 rounded bg-gray-50 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="Триммер, Лопаты">
            </div>
            {{-- Примечание --}}
            <div class="p-3 border rounded-lg bg-white shadow-sm">
                <label class="flex items-center gap-2 mb-2 cursor-pointer">
                    <input type="checkbox" id="use_tgNotes" class="w-4 h-4 accent-blue-600" checked>
                    <span class="text-[11px] font-black text-gray-600 uppercase flex items-center gap-1">
                        <i class="fa-solid fa-backpack text-gray-400"></i> Примечание
                    </span>
                </label>
                <input type="text" id="tgNotes" class="w-full text-[12px] border-gray-300 rounded bg-gray-50 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="Форма, обед">
            </div>
        </div>

        {{-- 🔹 РАЗДЕЛИТЕЛЬ --}}
        <hr class="my-4 border-gray-200">
        <div class="mb-3 text-[11px] font-black text-gray-400 uppercase tracking-wider">+ Дополнительные поля (свои):</div>

        {{-- 🔹 ДИНАМИЧЕСКИЕ ПОЛЯ (Контейнер) --}}
        <div id="tgFieldsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">

            {{-- Шаблон для клонирования (Скрытый) --}}
            <template id="tgFieldTemplate">
                <div class="p-3 border rounded-lg bg-white shadow-sm field-row">
                    <label class="flex items-center gap-2 mb-2 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 accent-blue-600 tg-check" name="tg_send[]" value="0" checked>
                        <span class="text-[11px] font-black text-gray-600 uppercase flex items-center gap-1 flex-1">
                            <input type="text" class="tg-name flex-1 bg-transparent border-0 border-b border-gray-300 focus:ring-0 focus:border-blue-500 text-[11px] font-black uppercase placeholder-gray-400" placeholder="Название поля">
                        </span>
                        <button type="button" onclick="this.closest('.field-row').remove()" class="text-red-500 hover:text-red-700 p-1">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </label>
                    <input type="text" class="w-full text-[12px] border-gray-300 rounded bg-gray-50 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 tg-value" placeholder="Значение">
                </div>
            </template>
        </div>

        {{-- КНОПКА ДОБАВИТЬ ПОЛЕ --}}
        <button type="button" onclick="addTgField()" class="w-full py-3 border-2 border-dashed border-blue-300 text-blue-600 text-[11px] font-black uppercase rounded hover:bg-blue-50 transition-colors mb-4">
            + Добавить своё поле
        </button>

        {{-- 🔹 ССЫЛКА НА ТАБЕЛЬ --}}
        <div class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-100">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" id="tgIncludePublicLink" class="w-5 h-5 accent-blue-600" checked>
                <div>
                    <span class="block text-[11px] font-black text-blue-800 uppercase">Прикрепить ссылку на табель</span>
                    <span class="text-[10px] text-blue-400 font-bold italic">public-tabel/{{ $timesheet->slug ?? $timesheet->id }}</span>
                </div>
            </label>
        </div>

        {{-- 🔹 КНОПКА ОТПРАВИТЬ --}}
        <div class="flex justify-end border-t pt-4">
            <button type="button" onclick="sendToTelegram()" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-black text-[12px] uppercase flex items-center gap-2 hover:bg-blue-700 transition-all shadow-lg active:scale-95">
                <i class="fab fa-telegram-plane"></i> <span>Отправить отчет</span>
            </button>
        </div>
        <div class="flex justify-between items-center mb-4">
    <h1>Табель: {{ $timesheet->title }}</h1>
    <a href="{{ route('telegram-logs.index', ['timesheet_id' => $timesheet->id]) }}"
       class="text-blue-600 hover:underline text-sm">
        📋 История отчетов
    </a>
</div>
    </div>
</div>

{{-- 🔹 СКРЫТАЯ ФОРМА ДЛЯ ОТПРАВКИ --}}
<form id="tgHiddenForm" action="{{ route('report.send.telegram') }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="date" id="tgDateInput">
    <input type="hidden" name="status_id" id="tgStatusInput">
    <input type="hidden" name="timesheet_id" value="{{ $timesheet->id }}">

    {{-- Стандартные поля --}}
    <input type="hidden" name="master" id="tgMasterInput">
    <input type="hidden" name="work_location" id="tgWorkLocationInput">
    <input type="hidden" name="work_type" id="tgWorkTypeInput">
    <input type="hidden" name="departure" id="tgDepartureInput">
    <input type="hidden" name="transport" id="tgTransportInput">
    <input type="hidden" name="inventory" id="tgInventoryInput">
    <input type="hidden" name="notes" id="tgNotesInput">

    {{-- Динамические поля (JSON) --}}
    <input type="hidden" name="tg_data" id="tgDataInput">

    {{-- Ссылка --}}
    <input type="hidden" name="public_link" id="tgPublicLinkInput">
</form>

{{-- СВОДКИ И КОДЫ --}}
<div class="space-y-4">
    <div class="card p-4">
        <div class="flex items-center gap-6 flex-wrap">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Цветовые коды:</span>
            @foreach($statuses as $s)
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded shadow-sm border" style="background: {{ $s->color }}"></div>
                    <span class="text-slate-700 text-[10px] font-bold uppercase">{{ $s->name }}</span>
                </div>
            @endforeach
        </div>
    </div>
    <div class="card border-t-4 border-slate-900 shadow-md">
        <div class="bg-slate-900 text-white px-4 py-2 text-[10px] font-black uppercase flex justify-between items-center">
            <span>Оперативная сводка</span>
            <span id="summaryTitleLabel" class="text-blue-400 bg-white/10 px-3 py-0.5 rounded-full">Весь месяц</span>
        </div>
        <div id="dynamicSummaryWidget" class="summary-pill-grid bg-slate-50/50"></div>
    </div>
</div>

<div class="card mt-4 overflow-x-auto">
    <table class="w-full min-w-[800px]">
        <thead>
            <tr class="bg-slate-50 text-[9px] uppercase text-slate-500 font-bold">
                <th class="w-12">№</th>
                <th class="text-left px-8">Сотрудник (ФИО полностью)</th>
                @foreach($statuses as $s) <th class="w-24">{{ $s->name }}</th> @endforeach
                <th class="w-28 bg-blue-600 text-white font-black">Всего дней</th>
            </tr>
        </thead>
        <tbody id="summaryBody"></tbody>
        <tfoot id="summaryFoot"></tfoot>
    </table>
</div>



<script>



    /**
     * Выполняет массовое сохранение статусов для выбранного диапазона и сотрудников.
     * @param {number} tsId - ID табеля
     */
   async function runMassAction(tsId) {
    const start = document.getElementById('mass_start').value;
    const end = document.getElementById('mass_end').value;
    const statusId = document.getElementById('mass_status').value;
    const targetEmpId = document.getElementById('mass_employee').value;

    let rowsToApply = [];
    if (targetEmpId === 'all') {
        rowsToApply = document.querySelectorAll('.employee-row');
    } else {
        const row = document.querySelector(`.employee-row[data-emp-id="${targetEmpId}"]`);
        if(row) rowsToApply = [row];
    }

    if(rowsToApply.length === 0) return;

    if(!confirm(`Внимание! Будет изменено ${rowsToApply.length} строк. Продолжить?`)) return;

    // Эффект ожидания на кнопке
    const btn = event.currentTarget;
    const originalContent = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Обработка...';

    for (let row of rowsToApply) {
        const empId = row.getAttribute('data-emp-id');
        const cells = row.querySelectorAll('td[data-date]');

        for (let cell of cells) {
            const date = cell.getAttribute('data-date');

            if (date >= start && date <= end) {
                const select = cell.querySelector('select');
                if (select && select.value !== statusId) {
                    // Визуальный эффект "подмигивания" для понимания прогресса
                    cell.style.opacity = "0.5";
                    select.value = statusId;

                    await saveStatus(select, empId, date);

                    cell.style.opacity = "1";
                }
            }
        }
    }

    // Возвращаем кнопку в исходное состояние
    btn.disabled = false;
    btn.innerHTML = originalContent;

    alert('Массовая установка успешно завершена!');
}



    const allStatuses = [ @foreach($statuses as $s) { id: '{{ $s->id }}', name: '{{ $s->name }}', short: '{{ $s->short_name }}', color: '{{ $s->color }}' }, @endforeach ];
    let sortDirection = 'asc';





    function sendToTelegram() {
    const dateSelect = document.getElementById('filterDate');
    const statusSelect = document.getElementById('filterStatus');
    if (!dateSelect?.value || !statusSelect?.value) {
        alert('Сначала выберите ДАТУ и СТАТУС в фильтрах!');
        return;
    }
    document.getElementById('tgDateInput').value = dateSelect.value;
    document.getElementById('tgStatusInput').value = statusSelect.value;

    // 1️⃣ Стандартные поля
    const getVal = (cbId, inpId) => {
        const cb = document.getElementById(cbId);
        const inp = document.getElementById(inpId);
        return (cb && cb.checked && inp) ? String(inp.value).trim() : "";
    };
    document.getElementById('tgMasterInput').value = getVal('use_tgMaster', 'tgMaster');
    document.getElementById('tgWorkLocationInput').value = getVal('use_tgWorkLocation', 'tgWorkLocation');
    document.getElementById('tgWorkTypeInput').value = getVal('use_tgWorkType', 'tgWorkType');
    document.getElementById('tgDepartureInput').value = getVal('use_tgDeparture', 'tgDeparture');
    document.getElementById('tgTransportInput').value = getVal('use_tgTransport', 'tgTransport');
    document.getElementById('tgInventoryInput').value = getVal('use_tgInventory', 'tgInventory');
    document.getElementById('tgNotesInput').value = getVal('use_tgNotes', 'tgNotes');

    // 2️⃣ Динамические поля (JSON)
    const fields = [];
    document.querySelectorAll('.field-row').forEach(row => {
        const cb = row.querySelector('.tg-check');
        const name = row.querySelector('.tg-name');
        const val = row.querySelector('.tg-value');
        if (cb?.checked && name?.value.trim() && val?.value.trim()) {
            fields.push({ name: name.value.trim(), value: val.value.trim() });
        }
    });
    document.getElementById('tgDataInput').value = JSON.stringify(fields);

    // 3️⃣ Ссылка
    const linkCb = document.getElementById('tgIncludePublicLink');
    document.getElementById('tgPublicLinkInput').value = linkCb?.checked
        ? window.location.origin + "/public-tabel/{{ $timesheet->slug ?? $timesheet->id }}"
        : "";

    document.getElementById('tgHiddenForm').submit();
}

function toggleTgDetails() {
    const content = document.getElementById('tgDetailsContent');
    const badge = document.getElementById('tgDetailsBadge');
    const chevron = document.getElementById('tgDetailsChevron');
    content.classList.toggle('hidden');
    if (content.classList.contains('hidden')) {
        badge.innerText = "Скрыто";
        chevron.style.transform = "rotate(0deg)";
    } else {
        badge.innerText = "Активно";
        chevron.style.transform = "rotate(180deg)";
    }

}





function toggleTgDetails() {
    const content = document.getElementById('tgDetailsContent');
    const chevron = document.getElementById('tgDetailsChevron');
    const badge = document.getElementById('tgDetailsBadge');

    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        chevron.style.transform = 'rotate(180deg)';
        badge.innerText = 'Настройка';
        badge.classList.replace('bg-blue-100', 'bg-emerald-100');
        badge.classList.replace('text-blue-600', 'text-emerald-600');
    } else {
        content.classList.add('hidden');
        chevron.style.transform = 'rotate(0deg)';
        badge.innerText = 'Скрыто';
        badge.classList.replace('bg-emerald-100', 'bg-blue-100');
        badge.classList.replace('text-emerald-600', 'text-blue-600');
    }
}


    function toggleSortFio() {
        sortDirection = (sortDirection === 'asc') ? 'desc' : 'asc';
        sortTableByFio(sortDirection);
    }

    function sortTableByFio(direction = 'asc') {
        const table = document.getElementById('mainTable');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('.employee-row'));
        rows.sort((a, b) => {
            const nameA = a.getAttribute('data-fio').toUpperCase();
            const nameB = b.getAttribute('data-fio').toUpperCase();
            return (direction === 'asc') ? nameA.localeCompare(nameB, 'ru') : nameB.localeCompare(nameA, 'ru');
        });
        rows.forEach(row => tbody.appendChild(row));
        rows.forEach((row, index) => {
            const numCell = row.querySelector('td:first-child');
            if (numCell) numCell.innerText = index + 1;
        });
        calculateTotals();
    }

    function applyFilters() {
        const search = document.getElementById('tableSearch').value.toLowerCase();
        const selectedDate = document.getElementById('filterDate').value;
        const selectedStatus = document.getElementById('filterStatus').value;
        const table = document.getElementById('mainTable');

        table.style.width = selectedDate ? 'auto' : '100%';

        document.querySelectorAll('.day-col, th.day-col').forEach(el => {
            if (selectedDate) {
                const dAttr = el.getAttribute('data-date') || el.getAttribute('data-day-header');
                el.classList.toggle('hidden-day', dAttr !== selectedDate);
                document.querySelectorAll('.extra-col').forEach(c => c.classList.add('hidden-day'));
                document.getElementById('modeIndicator').classList.remove('hidden');
            } else {
                el.classList.remove('hidden-day');
                document.querySelectorAll('.extra-col').forEach(c => c.classList.remove('hidden-day'));
                document.getElementById('modeIndicator').classList.add('hidden');
            }
        });

        document.querySelectorAll('.employee-row').forEach(row => {
            let mSearch = row.getAttribute('data-fio').toLowerCase().includes(search);
            let mStatus = true;
            if (selectedDate) {
                const val = row.querySelector(`td[data-date="${selectedDate}"] select`).value;
                mStatus = selectedStatus ? (val === selectedStatus) : (val !== "");
            } else if (selectedStatus) {
                mStatus = Array.from(row.querySelectorAll('.status-select')).some(s => s.value === selectedStatus);
            }
            row.style.display = (mSearch && mStatus) ? '' : 'none';
        });
        calculateTotals();
        updateDynamicWidget();
    }

    function calculateTotals() {
        const body = document.getElementById('summaryBody');
        const foot = document.getElementById('summaryFoot');
        if(!body) return;
        body.innerHTML = '';
        let colTotals = {}; let grand = 0;
        allStatuses.forEach(s => colTotals[s.id] = 0);
        const visibleRows = Array.from(document.querySelectorAll('.employee-row')).filter(r => r.style.display !== 'none');
        visibleRows.forEach((row, i) => {
            let counts = {}; let total = 0;
            allStatuses.forEach(s => counts[s.id] = 0);
            row.querySelectorAll('.status-select').forEach(sel => {
                if(sel.value) { counts[sel.value]++; colTotals[sel.value]++; total++; grand++; }
            });
            let html = `<tr class="border-b"><td class="py-3 text-slate-400 font-mono text-[10px]">${i+1}</td><td class="text-left px-8 font-bold text-slate-700 text-[13px]">${row.getAttribute('data-fio')}</td>`;
            allStatuses.forEach(s => { html += `<td class="font-bold text-[13px] ${counts[s.id]>0?'text-slate-900':'text-slate-100'}">${counts[s.id]}</td>`; });
            html += `<td class="font-black text-blue-700 bg-blue-50 text-[15px] border-l-2 border-blue-100">${total}</td></tr>`;
            body.innerHTML += html;
        });
        let fHtml = `<tr class="bg-slate-100 font-black"><td colspan="2" class="text-right px-8 py-3 uppercase text-[9px] text-slate-500">Общий итог:</td>`;
        allStatuses.forEach(s => { fHtml += `<td class="text-[15px] text-slate-900">${colTotals[s.id]}</td>`; });
        fHtml += `<td class="text-white bg-blue-700 text-[16px]">${grand}</td></tr>`;
        if(foot) foot.innerHTML = fHtml;
    }

    function saveStatus(select, empId, date) {
    const selectedOption = select.options[select.selectedIndex];
    const color = selectedOption.dataset.color;
    const cell = select.parentElement;

    // 1. Визуальное начало: добавляем класс анимации сохранения
    cell.classList.add('cell-saving');

    // Сразу меняем цвет для отзывчивости интерфейса (Optimistic UI)
    if (color && color !== "") {
        cell.style.setProperty('background-color', color, 'important');
        select.style.color = '#fff';
    } else {
        // Возвращаем цвет выходного или пустой ячейки
        cell.style.setProperty('background-color', cell.classList.contains('weekend-cell') ? '#fecaca' : '', 'important');
        select.style.color = '';
    }

    fetch(`/travel-timesheets/{{ $timesheet->id }}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ employee_id: empId, date: date, status_id: select.value })
    })
    .then(res => {
        if(res.ok) {
            // 2. Успех: убираем "пульсацию" и добавляем вспышку успеха
            cell.classList.remove('cell-saving');
            cell.classList.add('cell-success');

            // Удаляем класс успеха через секунду
            setTimeout(() => cell.classList.remove('cell-success'), 1000);

            calculateTotals();
            updateDynamicWidget();
        } else {
            throw new Error('Ошибка сервера');
        }
    })
    .catch(err => {
        cell.classList.remove('cell-saving');
        cell.style.backgroundColor = '#fee2e2'; // Красная вспышка при ошибке
        alert('Ошибка при сохранении!');
    });
}

    function updateDynamicWidget() {
        const selectedDate = document.getElementById('filterDate').value;
        const widgetContainer = document.getElementById('dynamicSummaryWidget');
        const label = document.getElementById('summaryTitleLabel');
        let totals = {};
        allStatuses.forEach(s => totals[s.name] = 0);
        const rows = document.querySelectorAll('.employee-row:not([style*="display: none"])');
        if (selectedDate) {
            label.innerText = "На дату: " + selectedDate.split('-').reverse().join('.');
            rows.forEach(row => {
                const sel = row.querySelector(`td[data-date="${selectedDate}"] select`);
                if (sel && sel.value) {
                    const statusName = sel.options[sel.selectedIndex].getAttribute('data-name');
                    totals[statusName]++;
                }
            });
        } else {
            label.innerText = "Весь месяц (чел/дн)";
            rows.forEach(row => {
                row.querySelectorAll('.status-select').forEach(sel => {
                    if (sel.value) {
                        const statusName = sel.options[sel.selectedIndex].getAttribute('data-name');
                        totals[statusName]++;
                    }
                });
            });
        }
        widgetContainer.innerHTML = Object.entries(totals).filter(([n, c]) => c > 0).map(([n, c]) => {
            const sObj = allStatuses.find(s => s.name === n);
            return `<div class="summary-pill" style="background:${sObj.color}"><div class="summary-pill-val">${c}</div><div class="summary-pill-lab">${n}</div></div>`;
        }).join('') || '<div class="text-slate-400 text-[10px] font-bold uppercase p-1">Нет данных</div>';
    }

    function resetFilters() {
        document.getElementById('tableSearch').value = "";
        document.getElementById('filterDate').value = "";
        document.getElementById('filterStatus').value = "";
        applyFilters();
    }

    function saveComment(id, text) {
        fetch(`/travel-timesheets/{{ $timesheet->id }}/update-comment`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ employee_id: id, comment: text })
        });
    }

    function deleteEmp(id) {
        if(confirm('Удалить сотрудника?')) {
            const f = document.createElement('form'); f.method='POST';
            f.action='/travel-timesheets/{{ $timesheet->id }}/remove-employee/'+id;
            f.innerHTML = '@csrf @method("DELETE")'; document.body.appendChild(f); f.submit();
        }
    }

    document.getElementById('tableSearch').addEventListener('input', applyFilters);
    document.getElementById('filterDate').addEventListener('change', applyFilters);
    document.getElementById('filterStatus').addEventListener('change', applyFilters);

    document.addEventListener('DOMContentLoaded', () => {
        sortTableByFio('asc');
        calculateTotals();
        updateDynamicWidget();
    });

function addTgField() {
    const container = document.getElementById('tgFieldsContainer');
    const template = document.getElementById('tgFieldTemplate');
    if (container && template) {
        const clone = template.content.cloneNode(true);
        container.appendChild(clone);
    }
}

// Удалите дубликат toggleTgDetails, оставьте один:
function toggleTgDetails() {
    const content = document.getElementById('tgDetailsContent');
    const chevron = document.getElementById('tgDetailsChevron');
    const badge = document.getElementById('tgDetailsBadge');
    content.classList.toggle('hidden');
    if (content.classList.contains('hidden')) {
        badge.innerText = "Скрыто";
        badge.classList.replace('bg-emerald-100', 'bg-blue-100');
        badge.classList.replace('text-emerald-600', 'text-blue-600');
        chevron.style.transform = "rotate(0deg)";
    } else {
        badge.innerText = "Настройка";
        badge.classList.replace('bg-blue-100', 'bg-emerald-100');
        badge.classList.replace('text-blue-600', 'text-emerald-600');
        chevron.style.transform = "rotate(180deg)";
    }
}

</script>
</body>
</html>
