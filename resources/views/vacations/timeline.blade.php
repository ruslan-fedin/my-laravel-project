@extends('layouts.app')
@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <div>
        <h1 class="text-3xl md:text-4xl font-black text-slate-900">📊 График отпусков</h1>
        <p class="text-slate-500 mt-2 text-sm md:text-base">Горизонтальная временная шкала на {{ $year }} год</p>
    </div>
    <div class="flex flex-wrap gap-2 md:gap-3">
        <a href="{{ route('vacations.create') }}" class="bg-indigo-600 text-white px-4 md:px-6 py-2 md:py-3 rounded-xl md:rounded-2xl font-bold hover:bg-indigo-700 flex items-center gap-2 text-sm">
            <i class="fas fa-plus"></i> <span class="hidden lg:inline">Добавить</span>
        </a>
        <a href="{{ route('vacations.export', ['year' => $year]) }}" class="bg-emerald-600 text-white px-4 md:px-6 py-2 md:py-3 rounded-xl md:rounded-2xl font-bold hover:bg-emerald-700 flex items-center gap-2 text-sm">
            <i class="fas fa-file-excel"></i> <span class="hidden lg:inline">Excel</span>
        </a>
        <a href="{{ route('vacations.index') }}" class="bg-white px-4 py-2 md:py-3 rounded-xl md:rounded-2xl font-bold border hover:bg-slate-50 text-sm">📅</a>
        <a href="{{ route('dashboard') }}" class="bg-white px-4 py-2 md:py-3 rounded-xl md:rounded-2xl font-bold border hover:bg-slate-50 text-sm">← Дашборд</a>
    </div>
</div>

@if(session('success'))
<div class="bg-emerald-50 border border-emerald-200 rounded-xl md:rounded-2xl p-3 md:p-4 mb-4 md:mb-6 flex items-center gap-3">
    <i class="fas fa-check-circle text-emerald-600 text-lg md:text-xl"></i>
    <span class="font-bold text-emerald-700 text-sm md:text-base">{{ session('success') }}</span>
</div>
@endif

<div class="bg-white rounded-xl md:rounded-3xl p-4 md:p-6 shadow-lg mb-4 md:mb-6">
    <div class="flex justify-between items-center">
        <form action="{{ route('vacations.timeline') }}" method="GET" class="flex items-center gap-2">
            <button type="submit" name="year" value="{{ $year - 1 }}" class="px-3 md:px-4 py-2 rounded-lg md:rounded-xl border hover:bg-slate-50 transition-all">
                <i class="fas fa-chevron-left"></i>
            </button>
            <select name="year" onchange="this.form.submit()" class="px-4 md:px-6 py-2 rounded-lg md:rounded-xl border font-bold text-base md:text-lg focus:ring-2 focus:ring-indigo-500">
                @for($y = $year - 2; $y <= $year + 2; $y++)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" name="year" value="{{ $year + 1 }}" class="px-3 md:px-4 py-2 rounded-lg md:rounded-xl border hover:bg-slate-50 transition-all">
                <i class="fas fa-chevron-right"></i>
            </button>
        </form>
        <a href="{{ route('vacations.timeline', ['year' => now()->year]) }}" class="px-4 md:px-6 py-2 rounded-lg md:rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-all text-sm md:text-base">
            Сегодня
        </a>
    </div>
</div>

@if(count($employees ?? []) > 0)
<div class="bg-white rounded-xl md:rounded-2xl p-4 shadow-lg mb-4 md:mb-6" id="massActionsPanel">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-4">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" id="selectAll" class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm font-bold text-slate-700">Выбрать все</span>
            </label>
            <span class="text-sm text-slate-500" id="selectedCount">0 выбрано</span>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" onclick="clearSelection()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 text-sm">
                Снять выделение
            </button>
            <button type="button" onclick="deleteSelected()" class="px-6 py-2 rounded-xl bg-rose-600 text-white font-bold hover:bg-rose-700 text-sm flex items-center gap-2 shadow-lg shadow-rose-200">
                <i class="fas fa-trash-alt"></i> Удалить выбранные (<span id="deleteCount">0</span>)
            </button>
        </div>
    </div>
</div>
@endif

<div class="bg-white rounded-xl md:rounded-3xl p-4 md:p-6 shadow-lg mb-4 md:mb-6">
    <h3 class="text-base md:text-lg font-black text-slate-900 mb-4">📈 Нагрузка по месяцам</h3>
    <div class="grid grid-cols-12 gap-1 md:gap-2">
        @php
            $months = ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'];
        @endphp
        @foreach($loadByMonth ?? [] as $month)
        <div class="text-center">
            <div class="text-[8px] md:text-[10px] font-bold text-slate-400 mb-1">{{ $months[$month['month'] - 1] }}</div>
            <div class="h-16 md:h-24 bg-slate-100 rounded-lg md:rounded-xl relative overflow-hidden flex items-end">
                <div class="w-full {{ $month['percent'] > 30 ? 'bg-rose-500' : ($month['percent'] > 20 ? 'bg-orange-500' : 'bg-emerald-500') }}"
                    style="height: {{ max($month['percent'], 5) }}%"></div>
            </div>
            <div class="text-[8px] md:text-[10px] font-black text-slate-600 mt-1">{{ $month['percent'] }}%</div>
        </div>
        @endforeach
    </div>
</div>

<div class="bg-white rounded-xl md:rounded-3xl p-4 md:p-6 shadow-lg">
    <h3 class="text-base md:text-lg font-black text-slate-900 mb-4">📅 Временная шкала отпусков</h3>

    <div class="w-full">
        <div class="flex mb-3">
            <div class="w-12 md:w-16 flex-shrink-0"></div>
            <div class="w-48 md:w-64 flex-shrink-0 font-black text-slate-700 flex items-center text-sm md:text-base">
                Сотрудник
            </div>
            <div class="flex-1 flex">
                @php
                    $monthWidth = 100 / 12;
                    $months = ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'];
                @endphp
                @foreach($months as $month)
                <div class="text-center font-bold text-slate-600 text-[10px] md:text-sm" style="width: {{ $monthWidth }}%">
                    {{ $month }}
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex mb-4">
            <div class="w-12 md:w-16 flex-shrink-0"></div>
            <div class="w-48 md:w-64 flex-shrink-0"></div>
            <div class="flex-1 flex relative h-3 md:h-4">
                @foreach(range(1, 12) as $i)
                <div class="border-r border-slate-200" style="width: {{ $monthWidth }}%"></div>
                @endforeach
            </div>
        </div>

        <div class="space-y-2 max-h-[500px] md:max-h-[600px] overflow-y-auto pr-2" id="employeesList">
            @forelse($employees ?? [] as $emp)
            @php
                $startDate = \Carbon\Carbon::parse($emp->vacation_start);
                $endDate = \Carbon\Carbon::parse($emp->vacation_end);
                $startDay = $startDate->dayOfYear;
                $endDay = $endDate->dayOfYear;
                $totalDays = 365;
                $leftPercent = ($startDay / $totalDays) * 100;
                $widthPercent = (($endDay - $startDay + 1) / $totalDays) * 100;

                $bgColor = '#3b82f6';
                if ($emp->vacation_type === 'sick') {
                    $bgColor = '#ef4444';
                } elseif ($emp->vacation_type === 'unpaid') {
                    $bgColor = '#f59e0b';
                } elseif ($emp->vacation_type === 'study') {
                    $bgColor = '#8b5cf6';
                }

                $days = $startDate->diffInDays($endDate) + 1;

                // ✅ Формат: Фамилия И.О. (БЕЗ запятой)
                $fullName = $emp->last_name . ' ' .
                           mb_substr($emp->first_name, 0, 1) . '.' .
                           ($emp->middle_name ? mb_substr($emp->middle_name, 0, 1) . '.' : '');

                // ✅ Проверка на бригадира (с большой буквы Б)
                $isBrigadier = $emp->position && stripos($emp->position->name, 'Бригадир') !== false;
            @endphp
            <div class="flex items-center group hover:bg-slate-50 rounded-lg transition-colors {{ $isBrigadier ? 'bg-rose-50' : '' }}"
                data-employee-id="{{ $emp->id }}"
                id="row-{{ $emp->id }}">
                <div class="w-12 md:w-16 flex-shrink-0 flex items-center justify-center">
                    <input type="checkbox"
                          class="vacation-checkbox w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                          value="{{ $emp->id }}"
                          data-name="{{ $fullName }}"
                          onchange="updateSelectedCount()">
                </div>
                <div class="w-48 md:w-64 flex-shrink-0 pr-2 md:pr-4">
                    <div class="flex items-center gap-2">
                        {{-- ✅ УБРАН АВАТАР СОТРУДНИКА --}}
                        <div class="min-w-0 flex-1">
                            <div class="text-[11px] md:text-sm font-bold {{ $isBrigadier ? 'text-rose-700' : 'text-slate-900' }} truncate" title="{{ $fullName }}">
                                {{ $fullName }}
                                {{-- ✅ УБРАНА ИКОНКА 🔴 --}}
                            </div>
                            <div class="text-[8px] md:text-[10px] {{ $isBrigadier ? 'text-rose-600 font-bold' : 'text-slate-500' }} truncate">
                                {{ $emp->position->name ?? 'Без должности' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex-1 relative h-8 md:h-10 bg-slate-50 rounded-lg overflow-hidden border border-slate-100">
                    <div class="absolute inset-0 flex pointer-events-none">
                        @foreach(range(1, 12) as $i)
                        <div class="border-r border-slate-100" style="width: {{ $monthWidth }}%"></div>
                        @endforeach
                    </div>
                    <div class="absolute top-1 bottom-1 rounded hover:opacity-90 transition-opacity cursor-pointer group/bar"
                        style="left: {{ $leftPercent }}%; width: {{ max($widthPercent, 0.5) }}%; background-color: {{ $bgColor }};"
                        onclick="window.location='{{ route('vacations.edit', $emp->id) }}'"
                        title="{{ $fullName }}: {{ $days }} дн.">
                        @if($widthPercent > 3)
                        <div class="h-full flex items-center px-1 md:px-2 overflow-hidden">
                            <span class="text-[8px] md:text-[10px] font-black text-white whitespace-nowrap">{{ $days }} дн.</span>
                        </div>
                        @endif
                        <button type="button"
                               onclick="event.stopPropagation(); quickDelete({{ $emp->id }}, '{{ addslashes($fullName) }}')"
                               class="absolute right-1 top-1/2 -translate-y-1/2 w-5 h-5 rounded-full bg-white/30 hover:bg-rose-500 flex items-center justify-center opacity-0 group-hover/bar:opacity-100 transition-all shadow-sm"
                               title="Удалить отпуск">
                            <i class="fas fa-times text-[10px] text-white"></i>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-10 text-slate-400 bg-slate-50 rounded-xl">
                <i class="fas fa-calendar-times text-3xl md:text-4xl mb-3"></i>
                <p class="font-bold text-sm md:text-base">Нет отпусков на {{ $year }} год</p>
                <a href="{{ route('vacations.create') }}" class="text-indigo-600 font-bold hover:underline mt-2 inline-block text-sm">
                    Добавить первый отпуск
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>

<div class="mt-4 md:mt-6 flex gap-2 md:gap-4 justify-center flex-wrap">
    <div class="flex items-center gap-1 md:gap-2 bg-white px-3 md:px-4 py-2 rounded-lg md:rounded-xl shadow-sm">
        <div class="w-3 h-3 md:w-4 md:h-4 rounded bg-blue-500"></div>
        <span class="text-[10px] md:text-sm font-bold text-slate-600">Ежегодный</span>
    </div>
    <div class="flex items-center gap-1 md:gap-2 bg-white px-3 md:px-4 py-2 rounded-lg md:rounded-xl shadow-sm">
        <div class="w-3 h-3 md:w-4 md:h-4 rounded bg-red-500"></div>
        <span class="text-[10px] md:text-sm font-bold text-slate-600">Больничный</span>
    </div>
    <div class="flex items-center gap-1 md:gap-2 bg-white px-3 md:px-4 py-2 rounded-lg md:rounded-xl shadow-sm">
        <div class="w-3 h-3 md:w-4 md:h-4 rounded bg-orange-500"></div>
        <span class="text-[10px] md:text-sm font-bold text-slate-600">За свой счёт</span>
    </div>
    <div class="flex items-center gap-1 md:gap-2 bg-white px-3 md:px-4 py-2 rounded-lg md:rounded-xl shadow-sm">
        <div class="w-3 h-3 md:w-4 md:h-4 rounded bg-purple-500"></div>
        <span class="text-[10px] md:text-sm font-bold text-slate-600">Учебный</span>
    </div>
    @php $hasBrigadier = false; @endphp
    @foreach($employees ?? [] as $emp)
        @if($emp->position && stripos($emp->position->name, 'Бригадир') !== false)
            @php $hasBrigadier = true; @endphp
        @endif
    @endforeach
    @if($hasBrigadier)
    <div class="flex items-center gap-1 md:gap-2 bg-rose-50 px-3 md:px-4 py-2 rounded-lg md:rounded-xl shadow-sm">
        <div class="w-3 h-3 md:w-4 md:h-4 rounded bg-rose-500"></div>
        <span class="text-[10px] md:text-sm font-bold text-rose-600">Бригадир</span>
    </div>
    @endif
</div>

<script>
function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('.vacation-checkbox:checked');
    const count = checkboxes.length;

    document.getElementById('selectedCount').textContent = count + ' выбрано';
    document.getElementById('deleteCount').textContent = count;

    document.querySelectorAll('[data-employee-id]').forEach(row => {
        const checkbox = row.querySelector('.vacation-checkbox');
        if (checkbox && checkbox.checked) {
            row.classList.add('bg-indigo-50', 'border', 'border-indigo-200');
        } else {
            row.classList.remove('bg-indigo-50', 'border', 'border-indigo-200');
        }
    });
}

document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.vacation-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = this.checked;
    });
    updateSelectedCount();
});

function clearSelection() {
    const checkboxes = document.querySelectorAll('.vacation-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = false;
    });
    document.getElementById('selectAll').checked = false;
    updateSelectedCount();
}

function quickDelete(id, name) {
    if (confirm('Удалить отпуск сотрудника ' + name + '?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/vacations/' + id;
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteSelected() {
    const checkboxes = document.querySelectorAll('.vacation-checkbox:checked');

    if (checkboxes.length === 0) {
        alert('Выберите хотя бы один отпуск для удаления');
        return;
    }

    const names = Array.from(checkboxes).map(cb => cb.dataset.name).join('\n');

    if (confirm('Удалить отпуска ' + checkboxes.length + ' сотрудников?\n\n' + names + '\n\nЭто действие нельзя отменить!')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("vacations.mass-delete") }}';
        form.innerHTML = '@csrf @method("DELETE")';

        checkboxes.forEach(cb => {
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'ids[]';
            idInput.value = cb.value;
            form.appendChild(idInput);
        });

        document.body.appendChild(form);
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
});
</script>
@endsection
