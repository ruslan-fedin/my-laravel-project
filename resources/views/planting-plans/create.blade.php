@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-emerald-50 p-6">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl md:text-4xl font-black text-slate-900">➕ Новый план посадок</h1>
            <p class="text-slate-500 mt-2 text-sm md:text-base">Добавление плана посадок цветов</p>
        </div>
        <a href="{{ route('planting-plans.index') }}" class="bg-white text-slate-700 px-6 py-3 rounded-2xl font-bold border hover:bg-slate-50 transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Назад к списку</span>
        </a>
    </div>

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

    <form action="{{ route('planting-plans.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <h3 class="font-black text-slate-900 mb-4 text-lg">📋 Основная информация</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="md:col-span-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">📍 Наименование *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500"
                           placeholder="Например: ул. Поляны (Скобелевская)">
                </div>

                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">🌱 Норма посадки *</label>
                    <input type="number" name="planting_rate" id="plantingRate" value="{{ old('planting_rate', 60) }}" required min="1"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">📏 Общая площадь (м²)</label>
                    <div id="totalAreaDisplay" class="px-4 py-3 rounded-xl bg-slate-100 text-slate-700 font-bold">
                        0.00
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">🌷 Общее количество (шт)</label>
                    <div id="totalQuantityDisplay" class="px-4 py-3 rounded-xl bg-emerald-100 text-emerald-700 font-bold">
                        0
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <h3 class="font-black text-slate-900 mb-4 text-lg">🌸 Клумбы и распределение цветов</h3>
            <p class="text-sm text-slate-500 mb-4">💡 Для каждой клумбы укажите количество цветов по цветам</p>

            <div id="flowerBedsContainer" class="space-y-4">
            </div>

            <button type="button" id="addFlowerBedBtn"
                    class="mt-4 w-full bg-emerald-100 text-emerald-600 px-6 py-3 rounded-xl font-bold hover:bg-emerald-200 transition">
                <i class="fas fa-plus mr-2"></i>Добавить клумбу
            </button>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <h3 class="font-black text-slate-900 mb-4 text-lg">📊 Итого по плану</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-slate-50 rounded-xl">
                    <div class="text-xs text-slate-500">Площадь</div>
                    <div class="text-lg font-black text-slate-700" id="summaryArea">0.00 м²</div>
                </div>
                <div class="text-center p-4 bg-emerald-50 rounded-xl">
                    <div class="text-xs text-slate-500">Количество</div>
                    <div class="text-lg font-black text-emerald-700" id="summaryQuantity">0 шт.</div>
                </div>
            </div>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-gradient-to-r from-emerald-600 to-emerald-700 text-white px-8 py-4 rounded-2xl font-bold hover:from-emerald-700 hover:to-emerald-800 transition shadow-lg shadow-emerald-200">
                <i class="fas fa-save mr-2"></i> Сохранить план
            </button>
            <a href="{{ route('planting-plans.index') }}" class="bg-white text-slate-700 px-8 py-4 rounded-2xl font-bold border hover:bg-slate-50 transition">
                Отмена
            </a>
        </div>
    </form>

</div>

<script>
let bedIndex = 0;
const colorTypes = {!! json_encode($colorTypes) !!};
const flowerBedsList = {!! json_encode($flowerBeds->map(function($bed) {
    return [
        'id' => $bed->id,
        'full_name' => $bed->full_name,
        'area' => $bed->area,
        'district' => $bed->district ?? '',
    ];
})) !!};

console.log('colorTypes:', colorTypes);
console.log('flowerBedsList:', flowerBedsList);

function addFlowerBed() {
    console.log('addFlowerBed called');
    const container = document.getElementById('flowerBedsContainer');
    const rowId = bedIndex++;

    const row = document.createElement('div');
    row.className = 'border-2 border-slate-200 rounded-xl p-4 bg-slate-50';

    let flowerBedOptions = '<option value="">-- Выберите клумбу --</option>';
    flowerBedsList.forEach(function(bed) {
        flowerBedOptions += '<option value="' + bed.id + '" data-area="' + bed.area + '" data-name="' + bed.full_name + '">' + bed.full_name + ' (' + bed.area + ' м²)</option>';
    });

    let colorInputs = '';
    for (const type in colorTypes) {
        const data = colorTypes[type];
        colorInputs += '<div><label class="text-xs text-slate-500 block mb-1">' + data.icon + ' ' + data.name + '</label>';
        colorInputs += '<input type="number" name="flower_beds[' + rowId + '][colors][' + type + ']" data-color="' + type + '" class="color-input w-full px-2 py-2 rounded-lg border border-slate-200 text-sm text-center" placeholder="0" min="0" value="0" oninput="updateBedTotal(this, ' + rowId + ')"></div>';
    }

    row.innerHTML = '<div class="flex items-center justify-between mb-3"><span class="font-bold text-slate-700">🌸 Клумба #' + (rowId + 1) + '</span><button type="button" onclick="removeFlowerBed(this)" class="text-rose-500 hover:text-rose-700 text-sm font-bold"><i class="fas fa-trash mr-1"></i>Удалить</button></div>';
    row.innerHTML += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4"><div><label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Выберите клумбу *</label><select name="flower_beds[' + rowId + '][flower_bed_id]" class="bed-select w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-emerald-500 text-sm" onchange="updateBedArea(this, ' + rowId + ')">' + flowerBedOptions + '</select></div><div><label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Площадь (м²)</label><input type="text" name="flower_beds[' + rowId + '][area]" class="bed-area w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-100 text-sm" readonly value="0.00"></div></div>';
    row.innerHTML += '<div class="border-t border-slate-200 pt-3"><label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">🎨 Распределение по цветам</label><div class="grid grid-cols-2 md:grid-cols-4 gap-2">' + colorInputs + '</div></div>';
    row.innerHTML += '<div class="mt-3 flex items-center justify-between"><span class="text-sm font-bold text-slate-600">Итого по клумбе:</span><span class="text-lg font-black text-emerald-600 bed-total">0 шт.</span></div>';
    row.innerHTML += '<input type="hidden" name="flower_beds[' + rowId + '][total]" class="bed-total-hidden" value="0"><input type="hidden" name="flower_beds[' + rowId + '][sort_order]" value="' + rowId + '">';

    container.appendChild(row);
    console.log('Flower bed added, rowId:', rowId);
}

function removeFlowerBed(button) {
    button.closest('.border-2').remove();
    updateSummary();
}

function updateBedArea(select, rowId) {
    const option = select.options[select.selectedIndex];
    const area = option.getAttribute('data-area') || 0;
    const row = select.closest('.border-2');
    row.querySelector('.bed-area').value = parseFloat(area).toFixed(2);
    updateSummary();
}

function updateBedTotal(input, rowId) {
    const row = input.closest('.border-2');
    let total = 0;
    row.querySelectorAll('.color-input').forEach(function(inp) {
        total += parseInt(inp.value) || 0;
    });
    row.querySelector('.bed-total').textContent = total.toLocaleString('ru-RU') + ' шт.';
    row.querySelector('.bed-total-hidden').value = total;
    updateSummary();
}

function updateSummary() {
    let totalArea = 0;
    let totalQuantity = 0;

    document.querySelectorAll('.border-2').forEach(function(row) {
        const area = parseFloat(row.querySelector('.bed-area').value) || 0;
        const quantity = parseInt(row.querySelector('.bed-total-hidden').value) || 0;
        totalArea += area;
        totalQuantity += quantity;
    });

    document.getElementById('totalAreaDisplay').textContent = totalArea.toFixed(2);
    document.getElementById('totalQuantityDisplay').textContent = totalQuantity.toLocaleString('ru-RU');
    document.getElementById('summaryArea').textContent = totalArea.toFixed(2).replace('.', ',') + ' м²';
    document.getElementById('summaryQuantity').textContent = totalQuantity.toLocaleString('ru-RU') + ' шт.';
}

document.getElementById('plantingRate').addEventListener('input', function() {
    updateSummary();
});

document.getElementById('addFlowerBedBtn').addEventListener('click', addFlowerBed);

addFlowerBed();
console.log('Script loaded successfully');
</script>

@endsection
