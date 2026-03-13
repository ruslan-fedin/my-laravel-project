@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-emerald-50 p-6">

    {{-- ЗАГОЛОВОК --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl md:text-4xl font-black text-slate-900">🌷 Новая работа</h1>
            <p class="text-slate-500 mt-2 text-sm md:text-base">
                Клумба: <strong>{{ $flowerBed->full_name }}</strong>
                @if($flowerBed->district) <span class="text-slate-400">({{ $flowerBed->district }})</span> @endif
            </p>
        </div>
        <a href="{{ route('flower-beds.show', $flowerBed->id) }}" class="bg-white text-slate-700 px-6 py-3 rounded-2xl font-bold border hover:bg-slate-50 transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Назад к клумбе</span>
        </a>
    </div>

    {{-- УВЕДОМЛЕНИЯ ОБ ОШИБКАХ --}}
    @if($errors->any())
    <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 mb-6">
        <div class="flex items-center gap-3 text-rose-700 font-bold">
            <i class="fas fa-exclamation-circle"></i>
            <span>Исправьте ошибки в форме</span>
        </div>
        <ul class="mt-2 text-sm text-rose-600 list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- ФОРМА --}}
    <form action="{{ route('flower-beds.works.store', $flowerBed->id) }}" method="POST" class="space-y-6">
        @csrf

        {{-- ОСНОВНАЯ ИНФОРМАЦИЯ --}}
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <h3 class="font-black text-slate-900 mb-4 text-lg">📋 Основная информация</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Вид работы --}}
                <div class="md:col-span-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">🔧 Вид работы *</label>
                    <select name="work_type_id" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                        <option value="">Выберите вид работы</option>
                        @foreach($workTypes as $type)
                        <option value="{{ $type->id }}" {{ old('work_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Заголовок --}}
                <div class="md:col-span-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">📝 Заголовок *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500"
                           placeholder="Например: Посадка тюльпанов весна 2025">
                </div>

                {{-- Описание --}}
                <div class="md:col-span-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">📄 Описание</label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500"
                              placeholder="Подробное описание работы...">{{ old('description') }}</textarea>
                </div>

                {{-- Статус --}}
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">📊 Статус *</label>
                    <select name="status" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                        <option value="completed" {{ old('status', 'completed') == 'completed' ? 'selected' : '' }}>✅ Завершено</option>
                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>🔵 В работе</option>
                        <option value="planned" {{ old('status') == 'planned' ? 'selected' : '' }}>🟡 Запланировано</option>
                    </select>
                </div>

                {{-- Дата работы --}}
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">📅 Дата работы</label>
                    <input type="date" name="work_date" value="{{ old('work_date', date('Y-m-d')) }}"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                </div>

                {{-- Заметки --}}
                <div class="md:col-span-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">💬 Заметки</label>
                    <textarea name="notes" rows="2"
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500"
                              placeholder="Дополнительная информация...">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ЦВЕТЫ (для посадочных работ) --}}
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-black text-slate-900 text-lg">🌷 Цветы (если применимо)</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        💡 Можно добавить несколько видов цветов
                    </p>
                </div>
                <button type="button" onclick="addFlowerRow()"
                        class="bg-emerald-100 text-emerald-600 px-4 py-2 rounded-xl font-bold text-sm hover:bg-emerald-200 transition">
                    <i class="fas fa-plus mr-1"></i>Добавить вид
                </button>
            </div>

            <div id="flowersContainer" class="space-y-4">
                <p class="text-sm text-slate-400 text-center py-8" id="emptyFlowersMessage">
                    <i class="fas fa-flower text-3xl mb-2"></i><br>
                    Нажмите кнопку выше, чтобы добавить информацию о цветах
                </p>
            </div>

            <div class="mt-4 p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                <h4 class="text-sm font-bold text-emerald-800 mb-2">📋 Пример:</h4>
                <ul class="text-xs text-emerald-700 space-y-1">
                    <li>✅ 500 шт. | Жёлтые | Апельдорн</li>
                    <li>✅ 300 шт. | Красные | Дарвиновы</li>
                </ul>
            </div>
        </div>

        {{-- КНОПКИ --}}
        <div class="flex gap-4">
            <button type="submit" class="bg-gradient-to-r from-emerald-600 to-emerald-700 text-white px-8 py-4 rounded-2xl font-bold hover:from-emerald-700 hover:to-emerald-800 transition shadow-lg shadow-emerald-200">
                <i class="fas fa-save mr-2"></i> Сохранить работу
            </button>
            <a href="{{ route('flower-beds.show', $flowerBed->id) }}" class="bg-white text-slate-700 px-8 py-4 rounded-2xl font-bold border hover:bg-slate-50 transition">
                Отмена
            </a>
        </div>
    </form>

</div>

{{-- СКРИПТ ДЛЯ ДОБАВЛЕНИЯ ЦВЕТОВ --}}
@push('scripts')
<script>
let flowerRowIndex = 0;

function addFlowerRow() {
    const emptyMessage = document.getElementById('emptyFlowersMessage');
    if (emptyMessage) emptyMessage.style.display = 'none';

    const container = document.getElementById('flowersContainer');
    const rowId = flowerRowIndex++;

    const row = document.createElement('div');
    row.className = 'border-2 border-emerald-200 rounded-xl p-4 bg-gradient-to-br from-emerald-50 to-white';
    row.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-black text-emerald-700">🌸 Вид #${rowId + 1}</span>
            <button type="button" onclick="removeFlowerRow(this)" class="text-rose-500 hover:text-rose-700 text-sm font-bold">
                <i class="fas fa-trash mr-1"></i>Удалить
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">🔢 Количество *</label>
                <input type="number" name="flowers[${rowId}][quantity]" min="1" required
                       class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-emerald-500 text-sm"
                       placeholder="500">
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">🎨 Цвет</label>
                <input type="text" name="flowers[${rowId}][flower_color]"
                       class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-emerald-500 text-sm"
                       placeholder="Жёлтые">
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">🏷️ Сорт</label>
                <input type="text" name="flowers[${rowId}][flower_variety]"
                       class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-emerald-500 text-sm"
                       placeholder="Апельдорн">
            </div>
            <div class="md:col-span-3">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">💬 Заметки</label>
                <input type="text" name="flowers[${rowId}][notes]"
                       class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-emerald-500 text-sm"
                       placeholder="Например: Первый ряд...">
            </div>
        </div>
    `;

    container.appendChild(row);
}

function removeFlowerRow(button) {
    button.closest('.border-2').remove();
    const container = document.getElementById('flowersContainer');
    const rows = container.querySelectorAll('.border-2');
    const emptyMessage = document.getElementById('emptyFlowersMessage');
    if (rows.length === 0 && emptyMessage) {
        emptyMessage.style.display = 'block';
    }
}
</script>
@endpush
@endsection
