@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 p-6">

    {{-- ВЕРХНЯЯ ПАНЕЛЬ --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-4xl font-black text-slate-900">✏️ Редактировать отпуск</h1>
            <p class="text-slate-500 mt-2">{{ $employee->last_name }} {{ $employee->first_name }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('vacations.index') }}" class="bg-white px-6 py-3 rounded-2xl font-bold border hover:bg-slate-50">← Календарь</a>
        </div>
    </div>

    {{-- ФОРМА --}}
    <div class="max-w-3xl mx-auto bg-white rounded-3xl p-10 shadow-lg">
        <form action="{{ route('vacations.updateVacation', $employee->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Сотрудник --}}
            <div class="mb-6">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Сотрудник</label>
                <input type="text" value="{{ $employee->last_name }} {{ $employee->first_name }} {{ $employee->middle_name }}"
                       class="w-full px-6 py-4 rounded-2xl border border-slate-200 bg-slate-50 text-slate-500 font-bold" readonly>
                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
            </div>

            {{-- Тип отпуска --}}
            <div class="mb-6">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Тип отпуска</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <label class="cursor-pointer">
                        <input type="radio" name="vacation_type" value="annual" {{ $employee->vacation_type === 'annual' ? 'checked' : '' }} required class="peer sr-only">
                        <div class="p-4 rounded-2xl border-2 border-slate-200 text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                            <span class="text-2xl mb-2 block">🏖️</span>
                            <span class="text-xs font-black text-slate-700">Ежегодный</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="vacation_type" value="sick" {{ $employee->vacation_type === 'sick' ? 'checked' : '' }} required class="peer sr-only">
                        <div class="p-4 rounded-2xl border-2 border-slate-200 text-center peer-checked:border-red-500 peer-checked:bg-red-50 transition-all">
                            <span class="text-2xl mb-2 block">🤒</span>
                            <span class="text-xs font-black text-slate-700">Больничный</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="vacation_type" value="unpaid" {{ $employee->vacation_type === 'unpaid' ? 'checked' : '' }} required class="peer sr-only">
                        <div class="p-4 rounded-2xl border-2 border-slate-200 text-center peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-all">
                            <span class="text-2xl mb-2 block">⏸️</span>
                            <span class="text-xs font-black text-slate-700">За свой счёт</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="vacation_type" value="study" {{ $employee->vacation_type === 'study' ? 'checked' : '' }} required class="peer sr-only">
                        <div class="p-4 rounded-2xl border-2 border-slate-200 text-center peer-checked:border-purple-500 peer-checked:bg-purple-50 transition-all">
                            <span class="text-2xl mb-2 block">📚</span>
                            <span class="text-xs font-black text-slate-700">Учебный</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Даты --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Дата начала</label>
                    <input type="date" name="vacation_start" value="{{ $employee->vacation_start }}" required
                           class="w-full px-6 py-4 rounded-2xl border border-slate-200 text-slate-900 font-bold focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Дата окончания</label>
                    <input type="date" name="vacation_end" value="{{ $employee->vacation_end }}" required
                           class="w-full px-6 py-4 rounded-2xl border border-slate-200 text-slate-900 font-bold focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            {{-- Кнопки --}}
            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-indigo-600 text-white px-8 py-4 rounded-2xl font-black uppercase hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200">
                    💾 Сохранить
                </button>

                <form action="{{ route('vacations.destroy', $employee->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Удалить отпуск?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-rose-500 text-white px-8 py-4 rounded-2xl font-black uppercase hover:bg-rose-600 transition-all">
                        🗑️ Удалить
                    </button>
                </form>

                <a href="{{ route('vacations.index') }}" class="px-8 py-4 rounded-2xl font-black uppercase border-2 border-slate-200 text-slate-600 hover:bg-slate-50 transition-all">
                    Отмена
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
