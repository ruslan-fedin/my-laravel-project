@extends('layouts.app') {{-- Используем ваш основной лейаут --}}

@section('content')
<div class="bg-white rounded-[2rem] shadow-xl overflow-hidden border border-slate-200">
    {{-- Шапка карточки --}}
    <div class="bg-slate-900 p-8 text-white">
        <div class="flex items-center gap-6">
            <div class="w-20 h-20 bg-blue-600 rounded-2xl flex items-center justify-center text-3xl shadow-lg">
                <i class="fas fa-user"></i>
            </div>
            <div>
                <span class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Карточка сотрудника</span>
                <h1 class="text-2xl font-black uppercase leading-tight">
                    {{-- Фамилия Имя Отчество полностью --}}
                    {{ $employee->last_name }} {{ $employee->first_name }} {{ $employee->middle_name }}
                </h1>
                <p class="text-slate-400 font-bold uppercase text-xs mt-1">
                    {{ $employee->position->name ?? 'Должность не указана' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Данные --}}
    <div class="p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h3 class="text-[10px] font-black text-slate-400 uppercase mb-4 tracking-widest">Основная информация</h3>
                <div class="space-y-4">
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-[11px] font-bold text-slate-500 uppercase">Табельный номер:</span>
                        <span class="text-[11px] font-black text-slate-900">{{ $employee->tab_no ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-[11px] font-bold text-slate-500 uppercase">Статус:</span>
                        <span class="text-[11px] font-black {{ $employee->status === 'active' ? 'text-green-600' : 'text-rose-600' }} uppercase">
                            {{ $employee->status === 'active' ? 'Работает' : 'В отпуске' }}
                        </span>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-[10px] font-black text-slate-400 uppercase mb-4 tracking-widest">Подразделение</h3>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <p class="text-[11px] font-black text-slate-900 uppercase">
                        {{ $employee->parent->last_name ?? '—' }} {{ $employee->parent->first_name ?? '' }}
                    </p>
                    <p class="text-[9px] font-bold text-slate-400 uppercase">Непосредственный руководитель</p>
                </div>
            </div>
        </div>

        <div class="mt-10 pt-6 border-t flex gap-4">
            <a href="{{ url()->previous() }}" class="px-6 py-3 bg-slate-100 text-slate-600 rounded-lg font-black uppercase text-[10px] hover:bg-slate-200 transition-all">
                <i class="fas fa-arrow-left mr-2"></i> Назад
            </a>
            <button class="px-6 py-3 bg-blue-600 text-white rounded-lg font-black uppercase text-[10px] shadow-lg hover:bg-blue-700 transition-all">
                Редактировать
            </button>
        </div>
    </div>
</div>
@endsection
