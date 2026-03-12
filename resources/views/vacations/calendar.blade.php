@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 p-6">

    {{-- ВЕРХНЯЯ ПАНЕЛЬ --}}
   {{-- ВЕРХНЯЯ ПАНЕЛЬ --}}
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-4xl font-black text-slate-900">📅 Календарь отпусков</h1>
        <p class="text-slate-500 mt-2">Планирование отпусков сотрудников</p>
    </div>
    <div class="flex gap-3">
        {{-- НОВАЯ КНОПКА: TIMELINE --}}
        <a href="{{ route('vacations.timeline') }}" class="bg-purple-600 text-white px-6 py-3 rounded-2xl font-bold hover:bg-purple-700 flex items-center gap-2">
            <i class="fas fa-chart-gantt"></i> График
        </a>
        <a href="{{ route('vacations.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl font-bold hover:bg-indigo-700 flex items-center gap-2">
            <i class="fas fa-plus"></i> Добавить
        </a>
        <a href="{{ route('vacations.export') }}" class="bg-emerald-600 text-white px-6 py-3 rounded-2xl font-bold hover:bg-emerald-700">📥 Экспорт</a>
        <a href="{{ route('dashboard') }}" class="bg-white px-6 py-3 rounded-2xl font-bold border">← Дашборд</a>
    </div>
</div>
    {{-- СООБЩЕНИЕ ОБ УСПЕХЕ --}}
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
        <span class="font-bold text-emerald-700">{{ session('success') }}</span>
    </div>
    @endif

    {{-- ФИЛЬТРЫ --}}
    <div class="bg-white rounded-3xl p-6 shadow-lg mb-6">
        <form id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <select name="position_id" id="positionFilter" class="px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500">
                <option value="">Все должности</option>
                @foreach($positions as $pos)
                    <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                @endforeach
            </select>
            <select name="master_id" id="masterFilter" class="px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500">
                <option value="">Все мастера</option>
                @foreach($masters as $master)
                    <option value="{{ $master->id }}">{{ $master->last_name }} {{ $master->first_name }}</option>
                @endforeach
            </select>
            <select name="vacation_type" id="typeFilter" class="px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500">
                <option value="">Все типы</option>
                <option value="annual">Ежегодный</option>
                <option value="sick">Больничный</option>
                <option value="unpaid">За свой счёт</option>
                <option value="study">Учебный</option>
            </select>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-indigo-700 transition-all">
                <i class="fas fa-filter"></i> Применить
            </button>
        </form>
    </div>

    {{-- КАЛЕНДАРЬ --}}
    <div class="bg-white rounded-3xl p-6 shadow-lg">
        <div id="calendar"></div>
    </div>

    {{-- ЛЕГЕНДА --}}
    <div class="mt-6 flex gap-4 justify-center flex-wrap">
        <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-xl shadow-sm">
            <div class="w-4 h-4 rounded bg-blue-500"></div>
            <span class="text-sm font-bold text-slate-600">Ежегодный</span>
        </div>
        <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-xl shadow-sm">
            <div class="w-4 h-4 rounded bg-red-500"></div>
            <span class="text-sm font-bold text-slate-600">Больничный</span>
        </div>
        <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-xl shadow-sm">
            <div class="w-4 h-4 rounded bg-orange-500"></div>
            <span class="text-sm font-bold text-slate-600">За свой счёт</span>
        </div>
        <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-xl shadow-sm">
            <div class="w-4 h-4 rounded bg-purple-500"></div>
            <span class="text-sm font-bold text-slate-600">Учебный</span>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        // ИСПРАВЛЕНО: начальный вид - год
        initialView: 'dayGridYear',
        locale: 'ru',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            // ИСПРАВЛЕНО: добавлен вид года
            right: 'dayGridMonth,dayGridQuarter,dayGridYear,listYear'
        },
        buttonText: {
            today: 'Сегодня',
            month: 'Месяц',
            quarter: 'Квартал',
            year: 'Год',
            list: 'Список'
        },
        // ИСПРАВЛЕНО: загружаем данные на год вперёд
        events: function(info, successCallback, failureCallback) {
            var positionId = document.getElementById('positionFilter').value;
            var masterId = document.getElementById('masterFilter').value;
            var typeFilter = document.getElementById('typeFilter').value;

            // ИСПРАВЛЕНО: расширенный диапазон дат для года
            var url = '/vacations/api?start=' + info.startStr + '&end=' + info.endStr;
            if (positionId) url += '&position_id=' + positionId;
            if (masterId) url += '&master_id=' + masterId;
            if (typeFilter) url += '&vacation_type=' + typeFilter;

            fetch(url)
                .then(r => r.json())
                .then(data => {
                    console.log('События:', data);
                    successCallback(data);
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    failureCallback(error);
                });
        },
        editable: true,
        eventDrop: function(info) {
            fetch('/vacations/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    id: info.event.id,
                    start: info.event.start.toISOString().split('T')[0],
                    end: info.event.end ? info.event.end.toISOString().split('T')[0] : info.event.start.toISOString().split('T')[0]
                })
            });
        },
        eventClick: function(info) {
            if (info.event.url) {
                window.location = info.event.url;
                return false;
            }
            var p = info.event.extendedProps;
            alert('Сотрудник: ' + p.full_name + '\nДолжность: ' + p.position + '\nТип: ' + p.type + '\nТелефон: ' + p.phone);
        },
        // ИСПРАВЛЕНО: настройки для годового вида
        dayGridYear: {
            fixedWeekCount: false
        },
        // ИСПРАВЛЕНО: компактное отображение событий
        eventDisplay: 'block',
        eventMaxStack: 5,
        // ИСПРАВЛЕНО: высота календаря
        height: 'auto',
        contentHeight: 800
    });
    calendar.render();

    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        calendar.refetchEvents();
    });
});
</script>
@endsection
