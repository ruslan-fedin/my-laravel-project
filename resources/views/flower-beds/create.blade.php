@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-emerald-50 p-6">

    {{-- ЗАГОЛОВОК --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl md:text-4xl font-black text-slate-900">🌸 Новая клумба</h1>
            <p class="text-slate-500 mt-2 text-sm md:text-base">Добавление в справочник</p>
        </div>
        <a href="{{ route('flower-beds.index') }}" class="bg-white text-slate-700 px-6 py-3 rounded-2xl font-bold border hover:bg-slate-50 transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Назад к списку</span>
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
    <form action="{{ route('flower-beds.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="createFlowerBedForm">
        @csrf

        {{-- Основные данные --}}
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <h3 class="font-black text-slate-900 mb-4 text-lg">📋 Основная информация</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Короткое название *</label>
                    <input type="text" name="short_name" value="{{ old('short_name') }}" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500"
                           placeholder="Клумба №3">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Полное название *</label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500"
                           placeholder="(МКАД-Глушко).Клумба №3.">
                </div>
                <div class="md:col-span-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Адрес/Местоположение</label>
                    <textarea name="address" rows="2"
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500"
                              placeholder="г.Москва ул.Поляны (МКАД-Глушко)">{{ old('address') }}</textarea>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Площадь (м²) *</label>
                    <input type="number" name="area" value="{{ old('area', 0) }}" step="0.01" min="0" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500"
                           placeholder="0.00">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Район</label>
                    <input type="text" name="district" value="{{ old('district') }}"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500"
                           placeholder="Например: Центральный">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Статус</label>
                    <select name="is_active" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                        <option value="1" {{ old('is_active', true) ? 'selected' : '' }}>✅ Активна</option>
                        <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>❌ Неактивна</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Тип цветов</label>
                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_perennial" value="1" {{ old('is_perennial') ? 'checked' : '' }}
                                   class="w-5 h-5 rounded border-slate-300 text-emerald-600 focus:ring-2 focus:ring-emerald-500">
                            <span class="text-sm font-bold text-slate-700">🌿 Многолетка</span>
                        </label>
                        <span class="text-xs text-slate-500">Если не отмечено — однолетние цветы</span>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Примечание</label>
                    <textarea name="notes" rows="3"
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500"
                              placeholder="Дополнительная информация о клумбе...">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ЗАГРУЗКА ФАЙЛОВ --}}
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <h3 class="font-black text-slate-900 mb-4 text-lg">📎 Загрузить файлы</h3>

            <div class="border-2 border-dashed border-emerald-300 rounded-xl p-8 bg-emerald-50/50">
                <div class="text-center">
                    <i class="fas fa-cloud-upload-alt text-5xl text-emerald-400 mb-4"></i>
                    <h4 class="font-bold text-slate-700 text-lg mb-2">Выберите файлы</h4>
                    <p class="text-sm text-slate-500 mb-4">Можно выбрать несколько файлов сразу</p>

                    <label class="inline-block">
                        <input type="file" id="fileInput" multiple accept=".pdf,.jpg,.jpeg,.png,.gif"
                               class="hidden" onchange="showFilesWithNotes()">
                        <span class="bg-emerald-600 text-white px-8 py-4 rounded-2xl font-bold hover:bg-emerald-700 transition cursor-pointer shadow-lg shadow-emerald-200 inline-flex items-center gap-2">
                            <i class="fas fa-folder-open"></i>
                            <span>Выбрать файлы</span>
                        </span>
                    </label>

                    <p class="text-xs text-slate-500 mt-4">
                        PDF, JPG, PNG, GIF • Максимум 10 MB каждый • Максимум 10 файлов
                    </p>
                </div>
            </div>

            <div id="uploadProgress" class="hidden mt-6">
                <div class="bg-emerald-100 rounded-xl p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-circle-notch fa-spin text-emerald-600"></i>
                        <span class="text-sm font-bold text-emerald-700">Загрузка файлов...</span>
                    </div>
                    <div class="w-full bg-emerald-200 rounded-full h-2">
                        <div id="progressBar" class="bg-gradient-to-r from-emerald-500 to-emerald-600 h-2 rounded-full transition-all" style="width: 0%"></div>
                    </div>
                </div>
            </div>

            <div id="filesWithNotesList" class="space-y-4 mt-6 hidden"></div>

            <div id="uploadButtonContainer" class="hidden mt-6 text-center">
                <button type="button" onclick="uploadAllFiles()"
                        class="bg-gradient-to-r from-emerald-600 to-emerald-700 text-white px-8 py-3 rounded-xl font-bold hover:from-emerald-700 hover:to-emerald-800 transition shadow-lg shadow-emerald-200">
                    <i class="fas fa-upload mr-2"></i>Загрузить все файлы
                </button>
            </div>

            <p class="text-sm text-slate-500 mt-4">
                <i class="fas fa-info-circle mr-1"></i>
                Можно загрузить до 10 файлов
            </p>
        </div>

        {{-- КНОПКИ --}}
        <div class="flex gap-4">
            <button type="submit" class="bg-gradient-to-r from-emerald-600 to-emerald-700 text-white px-8 py-4 rounded-2xl font-bold hover:from-emerald-700 hover:to-emerald-800 transition shadow-lg shadow-emerald-200">
                <i class="fas fa-save mr-2"></i> Сохранить клумбу
            </button>
            <a href="{{ route('flower-beds.index') }}" class="bg-white text-slate-700 px-8 py-4 rounded-2xl font-bold border hover:bg-slate-50 transition">
                Отмена
            </a>
        </div>
    </form>

</div>

<script>
window.pendingFiles = [];

function showFilesWithNotes() {
    const fileInput = document.getElementById('fileInput');
    const list = document.getElementById('filesWithNotesList');
    const uploadBtn = document.getElementById('uploadButtonContainer');

    if (!fileInput.files || fileInput.files.length === 0) return;

    const maxFiles = 10;

    if (fileInput.files.length > maxFiles) {
        alert(`❌ Можно загрузить максимум ${maxFiles} файлов`);
        fileInput.value = '';
        return;
    }

    window.pendingFiles = [];
    list.innerHTML = '';

    for (let i = 0; i < fileInput.files.length; i++) {
        const file = fileInput.files[i];
        window.pendingFiles.push({
            file: file,
            index: i,
            notes: ''
        });

        const iconClass = file.type === 'application/pdf' ? 'fa-file-pdf text-rose-500' : 'fa-file-image text-emerald-500';

        const div = document.createElement('div');
        div.className = 'bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden';
        div.innerHTML = `
            <div class="p-4">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center">
                        <i class="fas ${iconClass} text-xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-bold text-slate-700 truncate">${file.name}</div>
                        <div class="text-xs text-slate-500">${(file.size / 1024 / 1024).toFixed(2)} MB</div>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">💬 Примечание к этому файлу</label>
                    <textarea class="file-note-input w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-emerald-500 text-sm"
                              rows="2"
                              placeholder="Например: Паспорт утверждён 15.03.2025..."
                              data-index="${i}"></textarea>
                </div>
            </div>
        `;
        list.appendChild(div);
    }

    list.classList.remove('hidden');
    uploadBtn.classList.remove('hidden');
    fileInput.value = '';
}

async function uploadAllFiles() {
    if (window.pendingFiles.length === 0) return;

    const list = document.getElementById('filesWithNotesList');
    const progress = document.getElementById('uploadProgress');
    const progressBar = document.getElementById('progressBar');
    const uploadBtn = document.getElementById('uploadButtonContainer');

    const noteInputs = list.querySelectorAll('.file-note-input');
    noteInputs.forEach(input => {
        const index = parseInt(input.dataset.index);
        if (window.pendingFiles[index]) {
            window.pendingFiles[index].notes = input.value.trim();
        }
    });

    progress.classList.remove('hidden');
    uploadBtn.classList.add('hidden');

    let uploaded = 0;
    const total = window.pendingFiles.length;

    for (let item of window.pendingFiles) {
        const formData = new FormData();
        formData.append('file', item.file);
        formData.append('file_index', item.index);
        formData.append('file_notes[' + item.index + ']', item.notes);

        try {
            const response = await fetch(`/flower-beds/${window.newFlowerBedId}/files/upload`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                uploaded++;
            } else {
                alert(result.message);
            }
        } catch (error) {
            console.error('Ошибка:', error);
            alert('❌ Ошибка при загрузке: ' + item.file.name);
        }

        const percent = Math.round((uploaded / total) * 100);
        progressBar.style.width = percent + '%';
    }

    setTimeout(() => {
        progress.classList.add('hidden');
        progressBar.style.width = '0%';
        list.classList.add('hidden');
        list.innerHTML = '';
        window.pendingFiles = [];

        // Перенаправляем на страницу просмотра
        window.location.href = `/flower-beds/${window.newFlowerBedId}`;
    }, 500);
}

// Перехватываем отправку формы для сохранения ID клумбы
document.getElementById('createFlowerBedForm').addEventListener('submit', async function(e) {
    if (window.pendingFiles.length > 0) {
        e.preventDefault();

        const formData = new FormData(this);

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const responseData = await response.json();
                window.newFlowerBedId = responseData.id || null;

                if (!window.newFlowerBedId) {
                    const urlMatch = response.url.match(/flower-beds\/(\d+)/);
                    if (urlMatch) {
                        window.newFlowerBedId = urlMatch[1];
                    }
                }

                if (window.newFlowerBedId) {
                    await uploadAllFiles();
                } else {
                    this.submit();
                }
            } else {
                this.submit();
            }
        } catch (error) {
            console.error('Ошибка:', error);
            this.submit();
        }
    }
});
</script>
@endsection
