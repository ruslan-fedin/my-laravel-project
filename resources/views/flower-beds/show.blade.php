@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-emerald-50 p-6">

    {{-- ЗАГОЛОВОК --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl md:text-4xl font-black text-slate-900">🌸 {{ $flowerBed->short_name }}</h1>
            <p class="text-slate-500 mt-2 text-sm md:text-base">{{ $flowerBed->full_name }}</p>
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

    {{-- СТАТУС --}}
    <div class="mb-6">
        @if($flowerBed->is_active)
        <span class="text-sm font-bold px-4 py-2 rounded-full bg-emerald-100 text-emerald-700">✅ Активна</span>
        @else
        <span class="text-sm font-bold px-4 py-2 rounded-full bg-slate-100 text-slate-700">❌ Неактивна</span>
        @endif
    </div>

    {{-- УВЕДОМЛЕНИЯ --}}
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
        <span class="font-bold text-emerald-700">{{ session('success') }}</span>
    </div>
    @endif

   

    {{-- АДРЕС --}}
    @if($flowerBed->address)
    <div class="bg-white rounded-2xl p-6 shadow-lg mb-6">
        <h3 class="font-black text-slate-900 mb-4 text-lg">📍 Местоположение</h3>
        <p class="text-slate-600 whitespace-pre-line">{{ $flowerBed->address }}</p>
    </div>
    @endif
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-6 shadow-lg">
        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Район</div>
        <div class="text-lg font-bold text-slate-900 mt-2">{{ $flowerBed->district ?? '—' }}</div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-lg">
        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Тип цветов</div>
        <div class="text-lg font-bold mt-2">
            @if($flowerBed->is_perennial)
            <span class="text-emerald-600">🌿 Многолетка</span>
            @else
            <span class="text-amber-600">🌸 Однолетка</span>
            @endif
        </div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-lg">
        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Площадь</div>
        <div class="text-3xl font-black text-slate-900 mt-2">{{ number_format($flowerBed->area, 2) }} м²</div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-lg">
        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Статус</div>
        <div class="text-lg font-bold text-slate-900 mt-2">{{ $flowerBed->is_active ? 'Активна' : 'Неактивна' }}</div>
    </div>
</div>

    {{-- ПРИМЕЧАНИЕ --}}
    @if($flowerBed->notes)
    <div class="bg-white rounded-2xl p-6 shadow-lg mb-6">
        <h3 class="font-black text-slate-900 mb-4 text-lg">📝 Примечание</h3>
        <p class="text-slate-600 whitespace-pre-line">{{ $flowerBed->notes }}</p>
    </div>
    @endif

    {{-- ФАЙЛЫ --}}
    <div class="bg-white rounded-2xl p-6 shadow-lg mb-6">
        <h3 class="font-black text-slate-900 mb-4 text-lg">📎 Файлы ({{ $flowerBed->files->count() }})</h3>

        @if($flowerBed->files->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($flowerBed->files as $file)
            <div class="file-item bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                @if($file->notes)
                <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 p-3">
                    <div class="text-sm font-black text-white leading-snug">
                        <i class="fas fa-quote-left mr-2 opacity-75"></i>
                        {{ $file->notes }}
                    </div>
                </div>
                @endif

                <div class="p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center">
                            <i class="fas {{ $file->file_icon }} {{ $file->file_color }} text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-bold text-slate-700 truncate">{{ $file->original_name }}</div>
                            <div class="text-xs text-slate-500">{{ $file->formatted_size }}</div>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('flower-beds.files.view', $file->id) }}" target="_blank"
                           class="flex-1 bg-emerald-600 text-white py-2 rounded-lg font-bold text-xs hover:bg-emerald-700 transition text-center">
                            <i class="fas fa-eye mr-1"></i>Просмотр
                        </a>
                        <a href="{{ route('flower-beds.files.download', $file->id) }}"
                           class="flex-1 bg-indigo-600 text-white py-2 rounded-lg font-bold text-xs hover:bg-indigo-700 transition text-center">
                            <i class="fas fa-download mr-1"></i>Скачать
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8 bg-slate-50 rounded-xl">
            <i class="fas fa-file-pdf text-4xl text-slate-300 mb-2"></i>
            <p class="text-slate-500 text-sm">Нет файлов</p>
            <p class="text-slate-400 text-xs mt-1">Загрузите файлы через редактирование</p>
        </div>
        @endif
    </div>

        {{-- ЛОГ ИЗМЕНЕНИЙ --}}
    <div class="bg-white rounded-2xl p-6 shadow-lg mb-6">
        <h3 class="font-black text-slate-900 mb-4 text-lg">📋 Лог изменений</h3>

        @if($flowerBed->logs->count() > 0)
        <div class="space-y-3" id="logsList">
            @foreach($flowerBed->logs->sortByDesc('created_at') as $log)
            <div class="log-item border border-slate-200 rounded-xl p-4" data-log-id="{{ $log->id }}">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold px-2 py-1 rounded-full {{ $log->action_color }}">
                            {{ $log->action_label }}
                        </span>
                        <span class="text-xs text-slate-500">
                            {{ $log->created_at->format('d.m.Y H:i') }}
                        </span>
                    </div>
                    @if($log->user)
                    <span class="text-xs text-slate-500">
                        <i class="fas fa-user mr-1"></i>{{ $log->user->name }}
                    </span>
                    @endif
                </div>
                <p class="text-sm text-slate-700 log-description">{{ $log->description }}</p>

                {{-- Кнопки для всех пользователей --}}
                @if($log->is_editable)
                <div class="flex gap-2 mt-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="openEditLogModal({{ $log->id }}, '{{ addslashes($log->description) }}')"
                            class="text-xs bg-indigo-100 text-indigo-600 px-3 py-1.5 rounded-lg font-bold hover:bg-indigo-200 transition">
                        <i class="fas fa-pen mr-1"></i>Изменить
                    </button>
                    <button type="button" onclick="deleteLog({{ $log->id }})"
                            class="text-xs bg-rose-100 text-rose-600 px-3 py-1.5 rounded-lg font-bold hover:bg-rose-200 transition">
                        <i class="fas fa-trash mr-1"></i>Удалить
                    </button>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8 bg-slate-50 rounded-xl">
            <i class="fas fa-history text-4xl text-slate-300 mb-2"></i>
            <p class="text-slate-500 text-sm">Нет записей в логе</p>
        </div>
        @endif
    </div>

    {{-- МОДАЛЬНОЕ ОКНО РЕДАКТИРОВАНИЯ ЛОГА --}}
    <div id="editLogModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50" style="display:none;">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4 shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-black text-slate-900 text-xl">✏️ Редактировать запись лога</h3>
                <button type="button" onclick="closeEditLogModal()"
                        class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-200 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="editLogForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editLogId">

                <div class="mb-4">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Описание</label>
                    <textarea id="editLogDescription" name="description" rows="4"
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500"
                              placeholder="Введите новое описание..."></textarea>
                    <p class="text-xs text-slate-500 mt-2">Максимум 500 символов</p>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white py-3 rounded-xl font-bold hover:bg-indigo-700 transition">
                        <i class="fas fa-save mr-2"></i>Сохранить
                    </button>
                    <button type="button" onclick="closeEditLogModal()"
                            class="flex-1 bg-slate-100 text-slate-700 py-3 rounded-xl font-bold hover:bg-slate-200 transition">
                        Отмена
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- МОДАЛЬНОЕ ОКНО РЕДАКТИРОВАНИЯ ЛОГА --}}
    @if(Auth::user()->is_admin)
    <div id="editLogModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50" style="display:none;">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4 shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-black text-slate-900 text-xl">✏️ Редактировать запись лога</h3>
                <button type="button" onclick="closeEditLogModal()"
                        class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-200 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="editLogForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editLogId">

                <div class="mb-4">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Описание</label>
                    <textarea id="editLogDescription" name="description" rows="4"
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500"
                              placeholder="Введите новое описание..."></textarea>
                    <p class="text-xs text-slate-500 mt-2">Максимум 500 символов</p>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white py-3 rounded-xl font-bold hover:bg-indigo-700 transition">
                        <i class="fas fa-save mr-2"></i>Сохранить
                    </button>
                    <button type="button" onclick="closeEditLogModal()"
                            class="flex-1 bg-slate-100 text-slate-700 py-3 rounded-xl font-bold hover:bg-slate-200 transition">
                        Отмена
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif



    {{-- МЕТА-ИНФОРМАЦИЯ --}}
    <div class="bg-white rounded-2xl p-6 shadow-lg">
        <h3 class="font-black text-slate-900 mb-4 text-lg">ℹ️ Информация</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-slate-400">Создано:</span>
                <div class="font-bold text-slate-700">{{ $flowerBed->created_at->format('d.m.Y H:i') }}</div>
                @if($flowerBed->createdBy)
                <div class="text-xs text-slate-500">{{ $flowerBed->createdBy->name }}</div>
                @endif
            </div>
            <div>
                <span class="text-slate-400">Обновлено:</span>
                <div class="font-bold text-slate-700">{{ $flowerBed->updated_at->format('d.m.Y H:i') }}</div>
                @if($flowerBed->updatedBy)
                <div class="text-xs text-slate-500">{{ $flowerBed->updatedBy->name }}</div>
                @endif
            </div>
        </div>
    </div>

</div>




<script>
// Редактирование лога
function openEditLogModal(logId, description) {
    document.getElementById('editLogId').value = logId;
    document.getElementById('editLogDescription').value = description;

    const modal = document.getElementById('editLogModal');
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
}

function closeEditLogModal() {
    const modal = document.getElementById('editLogModal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
    document.getElementById('editLogForm').reset();
}

// Отправка формы редактирования
document.getElementById('editLogForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const logId = document.getElementById('editLogId').value;
    const description = document.getElementById('editLogDescription').value;

    try {
        const response = await fetch(`/flower-beds/logs/${logId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ description })
        });

        const result = await response.json();

        if (result.success) {
            // Обновляем описание в списке
            const logItem = document.querySelector(`.log-item[data-log-id="${logId}"]`);
            if (logItem) {
                const descElement = logItem.querySelector('.log-description');
                if (descElement) {
                    descElement.textContent = result.new_description;
                }
            }

            closeEditLogModal();
            alert('✅ Лог обновлён');
        } else {
            alert(result.message || '❌ Ошибка при обновлении');
        }
    } catch (error) {
        console.error('Ошибка:', error);
        alert('❌ Ошибка при обновлении лога');
    }
});

// Удаление лога
async function deleteLog(logId) {
    if (!confirm('Удалить эту запись лога? Это действие нельзя отменить.')) return;

    try {
        const response = await fetch(`/flower-beds/logs/${logId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            // Удаляем из списка
            const logItem = document.querySelector(`.log-item[data-log-id="${logId}"]`);
            if (logItem) {
                logItem.remove();
            }
            alert('✅ Лог удалён');
        } else {
            alert(result.message || '❌ Ошибка при удалении');
        }
    } catch (error) {
        console.error('Ошибка:', error);
        alert('❌ Ошибка при удалении лога');
    }
}

// Закрытие модального окна по клику вне
document.getElementById('editLogModal').addEventListener('click', function(e) {
    if (e.target.id === 'editLogModal') closeEditLogModal();
});
</script>



@endsection
