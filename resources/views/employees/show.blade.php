@extends('layouts.app')

@section('content')
<div class="px-6"> {{-- Боковые отступы по вашему стандарту --}}

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tighter">Карточка сотрудника</h1>
            <p class="text-[11px] text-slate-400 font-bold uppercase tracking-widest mt-1">Детальная информация и история работы</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('employees.edit', $employee->id) }}" class="bg-slate-900 text-white px-5 py-2.5 rounded text-[10px] font-black uppercase tracking-widest shadow-lg hover:bg-black transition flex items-center gap-2">
                <i class="fas fa-edit"></i> Редактировать
            </a>
            <a href="{{ route('employees.index') }}" class="bg-white border border-slate-200 text-slate-400 px-5 py-2.5 rounded text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition">
                Назад к списку
            </a>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-8">
        {{-- ЛЕВАЯ КОЛОНКА: ФОТО И ОСНОВНОЕ --}}
        <div class="col-span-4">
            <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-sm text-center">
                <div class="w-48 h-48 rounded-full border-4 border-slate-50 overflow-hidden bg-slate-50 mx-auto mb-6 shadow-inner">
                    @if($employee->photo && file_exists(public_path($employee->photo)))
                        <img src="{{ asset($employee->photo) }}" class="w-full h-full object-cover">
                    @else
                        <div class="flex items-center justify-center h-full text-slate-200 text-6xl">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                </div>

                <h2 class="text-xl font-black text-slate-800 uppercase leading-tight mb-1">
                    {{ $employee->last_name }}<br>
                    {{ $employee->first_name }} {{ $employee->middle_name }}
                </h2>
                <p class="text-blue-600 font-black uppercase text-xs tracking-widest mb-4">
                    {{ $employee->position->name ?? 'Должность не указана' }}
                </p>

                <div class="flex flex-col gap-2 mt-6">
                    <div class="bg-slate-50 p-3 rounded border border-slate-100 flex justify-between items-center">
                        <span class="text-[9px] font-black text-slate-400 uppercase">Статус:</span>
                        <span class="text-[10px] font-black uppercase {{ $employee->is_active ? 'text-green-600' : 'text-slate-400' }}">
                            {{ $employee->is_active ? 'Активен' : 'Неактивен' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ПРАВАЯ КОЛОНКА: ДАННЫЕ И ИСТОРИЯ --}}
        <div class="col-span-8">
            <div class="bg-white border border-slate-200 rounded-lg overflow-hidden shadow-sm mb-8">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                    <h3 class="text-[11px] font-black text-slate-700 uppercase tracking-widest">Персональные данные</h3>
                </div>
                <div class="p-6 grid grid-cols-2 gap-y-6">
                    <div>
                        <label class="block text-[9px] font-black text-slate-400 uppercase mb-1">Телефон:</label>
                        <span class="text-sm font-black text-slate-700">{{ $employee->phone ?? '—' }}</span>
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-slate-400 uppercase mb-1">Дата рождения:</label>
                        <span class="text-sm font-black text-slate-700">
                            {{ $employee->birth_date ? \Carbon\Carbon::parse($employee->birth_date)->format('d.m.Y') : '—' }}
                            @if($employee->birth_date)
                                <span class="text-slate-400 ml-2">({{ \Carbon\Carbon::parse($employee->birth_date)->age }} лет)</span>
                            @endif
                        </span>
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-slate-400 uppercase mb-1">Дата приема:</label>
                        <span class="text-sm font-black text-slate-700">
                            {{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('d.m.Y') : '—' }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-slate-400 uppercase mb-1">Общий стаж:</label>
                        <span class="text-sm font-black text-slate-700">
                            @if($employee->hire_date)
                                @php
                                    $diff = \Carbon\Carbon::parse($employee->hire_date)->diff(\Carbon\Carbon::now());
                                @endphp
                                {{ $diff->y }} г. {{ $diff->m }} мес.
                            @else
                                —
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            {{-- БЛОК С ОШИБКОЙ (ИСТОРИЯ ТАБЕЛЕЙ) --}}
            <div class="bg-white border border-slate-200 rounded-lg overflow-hidden shadow-sm">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                    <h3 class="text-[11px] font-black text-slate-700 uppercase tracking-widest">История в табелях</h3>
                </div>
                <div class="p-0">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase">Период</th>
                                <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase text-center">Отработано</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            {{-- ИСПРАВЛЕННЫЙ ЦИКЛ: Добавлена проверка на наличие данных --}}
                            @if(isset($employee->timesheetItems) && count($employee->timesheetItems) > 0)
                                @foreach($employee->timesheetItems->groupBy('timesheet_id') as $tsId => $items)
                                    @php $ts = $items->first()->timesheet; @endphp
                                    @if($ts)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('timesheets.show', $ts->id) }}" class="text-xs font-black text-slate-700 hover:text-blue-600 uppercase">
                                                {{ \Carbon\Carbon::parse($ts->start_date)->format('M Y') }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="text-xs font-black text-slate-600">{{ count($items) }} дн.</span>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2" class="px-6 py-10 text-center">
                                        <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">История табелей пуста</span>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-12">
        @include('layouts.footer')
    </div>
</div>
@endsection
