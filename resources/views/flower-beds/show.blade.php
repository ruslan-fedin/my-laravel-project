@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-emerald-50 p-6">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl md:text-4xl font-black text-slate-900">🌸 {{ $flowerBed->short_name }}</h1>
            <p class="text-slate-500 mt-2 text-sm md:text-base">{{ $flowerBed->full_name }} @if($flowerBed->district) ({{ $flowerBed->district }}) @endif</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('flower-beds.edit', $flowerBed->id) }}" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl font-bold hover:bg-indigo-700 transition flex items-center gap-2">
                <i class="fas fa-pen"></i>
                <span>Редактировать</span>
            </a>
            <a href="{{ route('flower-beds.index') }}" class="bg-white text-slate-700 px-6 py-3 rounded-2xl font-bold border hover:bg-slate-50 transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                <span>Назад к списку</span>
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
        <span class="font-bold text-emerald-700">{{ session('success') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Район</div>
            <div class="text-lg font-bold text-slate-900 mt-2">{{ $flowerBed->district ?? '—' }}</div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Площадь</div>
            <div class="text-lg font-bold text-slate-900 mt-2">{{ $flowerBed->area ?? '—' }} м²</div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Тип</div>
            <div class="text-lg font-bold text-slate-900 mt-2">{{ $flowerBed->bed_type ?? '—' }}</div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Статус</div>
            <div class="mt-2">
                @if($flowerBed->is_active)
                <span class="text-sm font-bold px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-700">✅ Активна</span>
                @else
                <span class="text-sm font-bold px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700">⚪ Неактивна</span>
                @endif
            </div>
        </div>
    </div>

    @if($flowerBed->description)
    <div class="bg-white rounded-2xl p-6 shadow-lg mb-6">
        <h3 class="font-black text-slate-900 mb-4 text-lg">📄 Описание</h3>
        <p class="text-slate-700 whitespace-pre-line">{{ $flowerBed->description }}</p>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg p-6 mt-6">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-black text-slate-900">🌷 История работ</h2>
            <span class="text-sm font-bold text-slate-500">{{ $workRecords->count() }} записей</span>
        </div>

        <div class="bg-gradient-to-br from-emerald-50 to-white border-2 border-emerald-200 rounded-xl p-4 mb-6">
            <h3 class="font-bold text-emerald-800 mb-3">➕ Добавить работу</h3>

            <form id="workForm" class="space-y-3">
                @csrf
                <input type="hidden" name="flower_bed_id" value="{{ $flowerBed->id }}">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">🔧 Вид работы *</label>
                        <select name="work_type_id" required class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                            <option value="">Выберите</option>
                            @foreach($workTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">📅 Дата *</label>
                        <input type="date" name="work_date" value="{{ date('Y-m-d') }}" required
                               class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-emerald-700 transition">
                            <i class="fas fa-plus mr-2"></i>Добавить
                        </button>
                    </div>
                </div>

                <div id="flowersSection" class="hidden pt-3 border-t border-emerald-200">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">🌷 Цветы (необязательно)</label>
                        <button type="button" onclick="addFlowerRow()" class="text-emerald-600 text-xs font-bold hover:text-emerald-700">
                            <i class="fas fa-plus mr-1"></i>Добавить вид
                        </button>
                    </div>
                    <div id="flowersContainer" class="space-y-2"></div>
                </div>

                <button type="button" onclick="toggleFlowers()" class="text-xs text-emerald-600 font-bold hover:text-emerald-700">
                    <i class="fas fa-flower mr-1"></i><span id="toggleFlowersText">Добавить информацию о цветах</span>
                </button>
            </form>
        </div>

        <div class="space-y-4">
            @forelse($workRecords as $work)
            <div class="border border-slate-200 rounded-xl p-4 hover:bg-slate-50 transition">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-lg font-black text-slate-900">{{ $work->work_date->format('d.m.Y') }}</span>
                            <span class="text-sm font-bold text-slate-600">{{ $work->workType->name }}</span>
                            <span class="text-xs font-bold px-2 py-1 rounded bg-emerald-100 text-emerald-700">✅</span>
                        </div>

                        @if($work->flowers->count() > 0)
                        <div class="flex flex-wrap gap-2 mb-2">
                            @foreach($work->flowers as $flower)
                            <span class="text-xs bg-slate-100 px-2 py-1 rounded">
                                {{ number_format($flower->quantity, 0, ',', ' ') }} шт.
                                @if($flower->flower_color) · {{ $flower->flower_color }} @endif
                            </span>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="togglePhotos({{ $work->id }})"
                                class="text-xs bg-indigo-100 text-indigo-600 px-3 py-1.5 rounded-lg font-bold hover:bg-indigo-200 transition">
                            <i class="fas fa-images mr-1"></i>Фото ({{ $work->photos->count() }})
                        </button>
                        <form action="{{ route('flower-beds.works.destroy', $work->id) }}" method="POST" onsubmit="return confirm('Удалить?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center hover:bg-rose-200 transition">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div id="photos-{{ $work->id }}" class="hidden mt-4 pt-4 border-t border-slate-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <div class="text-xs font-bold text-blue-700 bg-blue-100 px-3 py-2 rounded-lg text-center">🟦 До работы</div>
                            <div class="space-y-2">
                                @foreach($work->photos->where('photo_type', 'before') as $photo)
                                <div class="relative group">
                                    <img src="{{ $photo->view_url }}" alt="До" class="w-full h-32 object-cover rounded-lg border border-slate-200">
                                    <button type="button" onclick="deletePhoto({{ $photo->id }}, {{ $work->id }})" class="absolute top-1 right-1 w-6 h-6 bg-rose-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            <label class="block w-full py-2 px-3 bg-blue-600 text-white text-xs font-bold text-center rounded-lg cursor-pointer hover:bg-blue-700 transition">
                                <i class="fas fa-plus mr-1"></i>Добавить
                                <input type="file" accept="image/*" onchange="uploadPhoto(this, {{ $work->id }}, 'before')" class="hidden">
                            </label>
                        </div>

                        <div class="space-y-2">
                            <div class="text-xs font-bold text-amber-700 bg-amber-100 px-3 py-2 rounded-lg text-center">🟨 Во время</div>
                            <div class="space-y-2">
                                @foreach($work->photos->where('photo_type', 'during') as $photo)
                                <div class="relative group">
                                    <img src="{{ $photo->view_url }}" alt="Во время" class="w-full h-32 object-cover rounded-lg border border-slate-200">
                                    <button type="button" onclick="deletePhoto({{ $photo->id }}, {{ $work->id }})" class="absolute top-1 right-1 w-6 h-6 bg-rose-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            <label class="block w-full py-2 px-3 bg-amber-600 text-white text-xs font-bold text-center rounded-lg cursor-pointer hover:bg-amber-700 transition">
                                <i class="fas fa-plus mr-1"></i>Добавить
                                <input type="file" accept="image/*" onchange="uploadPhoto(this, {{ $work->id }}, 'during')" class="hidden">
                            </label>
                        </div>

                        <div class="space-y-2">
                            <div class="text-xs font-bold text-emerald-700 bg-emerald-100 px-3 py-2 rounded-lg text-center">🟩 После</div>
                            <div class="space-y-2">
                                @foreach($work->photos->where('photo_type', 'after') as $photo)
                                <div class="relative group">
                                    <img src="{{ $photo->view_url }}" alt="После" class="w-full h-32 object-cover rounded-lg border border-slate-200">
                                    <button type="button" onclick="deletePhoto({{ $photo->id }}, {{ $work->id }})" class="absolute top-1 right-1 w-6 h-6 bg-rose-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            <label class="block w-full py-2 px-3 bg-emerald-600 text-white text-xs font-bold text-center rounded-lg cursor-pointer hover:bg-emerald-700 transition">
                                <i class="fas fa-plus mr-1"></i>Добавить
                                <input type="file" accept="image/*" onchange="uploadPhoto(this, {{ $work->id }}, 'after')" class="hidden">
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12 bg-slate-50 rounded-xl">
                <i class="fas fa-clipboard-list text-4xl text-slate-300 mb-2"></i>
                <p class="text-slate-500 text-sm">Нет записей о работах</p>
                <p class="text-slate-400 text-xs mt-1">Добавьте первую работу выше</p>
            </div>
            @endforelse
        </div>
    </div>

</div>

@push('scripts')
<script>
let flowerRowIndex = 0;

function toggleFlowers() {
    const section = document.getElementById('flowersSection');
    const text = document.getElementById('toggleFlowersText');
    section.classList.toggle('hidden');
    text.textContent = section.classList.contains('hidden') ? 'Добавить информацию о цветах' : 'Скрыть';
}

function addFlowerRow() {
    const container = document.getElementById('flowersContainer');
    const rowId = flowerRowIndex++;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-3 gap-2';
    row.innerHTML = `
        <input type="number" name="flowers[${rowId}][quantity]" min="1" placeholder="Кол-во" class="px-2 py-2 rounded-lg border border-slate-200 text-sm">
        <input type="text" name="flowers[${rowId}][flower_color]" placeholder="Цвет" class="px-2 py-2 rounded-lg border border-slate-200 text-sm">
        <input type="text" name="flowers[${rowId}][flower_variety]" placeholder="Сорт" class="px-2 py-2 rounded-lg border border-slate-200 text-sm">
    `;
    container.appendChild(row);
}

function togglePhotos(workId) {
    const section = document.getElementById('photos-' + workId);
    section.classList.toggle('hidden');
}

async function uploadPhoto(input, workId, photoType) {
    const file = input.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('file', file);
    formData.append('photo_type', photoType);

    try {
        const response = await fetch(`/flower-beds/works/${workId}/photos/upload`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            alert('✅ Фото загружено');
            location.reload();
        } else {
            alert('❌ Ошибка при загрузке');
        }
    } catch (error) {
        console.error('Ошибка:', error);
        alert('❌ Ошибка при загрузке');
    }
}

async function deletePhoto(photoId, workId) {
    if (!confirm('Удалить это фото?')) return;

    try {
        const response = await fetch(`/flower-beds/works/photos/${photoId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            location.reload();
        } else {
            alert('❌ Ошибка при удалении');
        }
    } catch (error) {
        console.error('Ошибка:', error);
        alert('❌ Ошибка при удалении');
    }
}

document.getElementById('workForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const flowerBedId = formData.get('flower_bed_id');

    try {
        const response = await fetch(`/flower-beds/${flowerBedId}/works`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            alert(result.message);
            location.reload();
        } else {
            alert('❌ Ошибка при сохранении');
        }
    } catch (error) {
        console.error('Ошибка:', error);
        alert('❌ Ошибка при сохранении');
    }
});
</script>
@endpush
@endsection
