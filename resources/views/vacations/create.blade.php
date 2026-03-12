@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 p-6">

    {{-- ВЕРХНЯЯ ПАНЕЛЬ --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-4xl font-black text-slate-900">➕ Добавить отпуск</h1>
            <p class="text-slate-500 mt-2">Заполните форму для добавления отпуска сотруднику</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('vacations.index') }}" class="bg-white px-6 py-3 rounded-2xl font-bold border hover:bg-slate-50">← Календарь</a>
        </div>
    </div>

    {{-- ФОРМА --}}
    <div class="max-w-3xl mx-auto bg-white rounded-3xl p-10 shadow-lg">
        <form action="{{ route('vacations.store') }}" method="POST">
            @csrf

            {{-- Сотрудник --}}
            <div class="mb-6">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">
                    Сотрудник <span class="text-rose-500">*</span>
                </label>
                <select name="employee_id" required class="w-full px-6 py-4 rounded-2xl border border-slate-200 text-slate-900 font-bold text-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">Выберите сотрудника</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->last_name }} {{ $emp->first_name }} {{ $emp->middle_name }}
                            @if($emp->position) ({{ $emp->position->name }}) @endif
                        </option>
                    @endforeach
                </select>
                @error('employee_id')
                    <p class="text-rose-500 text-xs font-bold mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Тип отпуска --}}
            <div class="mb-6">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">
                    Тип отпуска <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <label class="cursor-pointer">
                        <input type="radio" name="vacation_type" value="annual" {{ old('vacation_type') === 'annual' ? 'checked' : '' }} required class="peer sr-only">
                        <div class="p-4 rounded-2xl border-2 border-slate-200 text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                            <span class="text-2xl mb-2 block">🏖️</span>
                            <span class="text-xs font-black text-slate-700">Ежегодный</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="vacation_type" value="sick" {{ old('vacation_type') === 'sick' ? 'checked' : '' }} required class="peer sr-only">
                        <div class="p-4 rounded-2xl border-2 border-slate-200 text-center peer-checked:border-red-500 peer-checked:bg-red-50 transition-all">
                            <span class="text-2xl mb-2 block">🤒</span>
                            <span class="text-xs font-black text-slate-700">Больничный</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="vacation_type" value="unpaid" {{ old('vacation_type') === 'unpaid' ? 'checked' : '' }} required class="peer sr-only">
                        <div class="p-4 rounded-2xl border-2 border-slate-200 text-center peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-all">
                            <span class="text-2xl mb-2 block">⏸️</span>
                            <span class="text-xs font-black text-slate-700">За свой счёт</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="vacation_type" value="study" {{ old('vacation_type') === 'study' ? 'checked' : '' }} required class="peer sr-only">
                        <div class="p-4 rounded-2xl border-2 border-slate-200 text-center peer-checked:border-purple-500 peer-checked:bg-purple-50 transition-all">
                            <span class="text-2xl mb-2 block">📚</span>
                            <span class="text-xs font-black text-slate-700">Учебный</span>
                        </div>
                    </label>
                </div>
                @error('vacation_type')
                    <p class="text-rose-500 text-xs font-bold mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Даты --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">
                        Дата начала <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="vacation_start" value="{{ old('vacation_start') }}" required
                           class="w-full px-6 py-4 rounded-2xl border border-slate-200 text-slate-900 font-bold focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('vacation_start')
                        <p class="text-rose-500 text-xs font-bold mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">
                        Дата окончания <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="vacation_end" value="{{ old('vacation_end') }}" required
                           class="w-full px-6 py-4 rounded-2xl border border-slate-200 text-slate-900 font-bold focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('vacation_end')
                        <p class="text-rose-500 text-xs font-bold mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Кнопки --}}
            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-indigo-600 text-white px-8 py-4 rounded-2xl font-black uppercase hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200">
                    ✅ Добавить отпуск
                </button>
                <a href="{{ route('vacations.index') }}" class="px-8 py-4 rounded-2xl font-black uppercase border-2 border-slate-200 text-slate-600 hover:bg-slate-50 transition-all">
                    Отмена
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
