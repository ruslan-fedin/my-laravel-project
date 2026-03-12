@extends('layouts.app')

@section('title', 'История отчетов Telegram')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📋 История отчетов Telegram</h1>
        <a href="{{ url()->previous() }}" class="text-blue-600 hover:underline">← Назад</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    

    {{-- Фильтры --}}
    <form method="GET" class="bg-white p-4 rounded-lg shadow mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">С даты:</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">По дату:</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Статус:</label>
                <select name="success" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">Все</option>
                    <option value="1" {{ request('success') === '1' ? 'selected' : '' }}>✅ Успешно</option>
                    <option value="0" {{ request('success') === '0' ? 'selected' : '' }}>❌ Ошибка</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">🔍 Фильтр</button>
                <a href="{{ route('telegram-logs.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">✖ Сброс</a>
            </div>
        </div>
    </form>

    {{-- Верхняя панель --}}
    <div class="bg-white rounded-lg shadow mb-6 px-6 py-3 border-b border-gray-200 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <input type="checkbox" id="selectAll" class="w-4 h-4 accent-blue-600">
            <label for="selectAll" class="text-sm font-medium text-gray-700">Выбрать все</label>
            <span id="selectedCount" class="text-sm text-gray-500">(0 выбрано)</span>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="deleteSelected()" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 text-sm font-bold uppercase">🗑 Удалить выбранные</button>
            <button type="button" onclick="confirmClearAll()" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-900 text-sm font-bold uppercase">⚠ Очистить всю историю</button>
        </div>
    </div>

    {{-- Таблица --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-10"></th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Дата отчета</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Табель</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Сотрудников</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Отправлен</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Результат</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Действия</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <input type="checkbox" value="{{ $log->id }}" class="log-checkbox w-4 h-4 accent-blue-600">
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ \Carbon\Carbon::parse($log->date)->format('d.m.Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <a href="{{ route('travel-timesheets.show', $log->timesheet_id) }}" class="text-blue-600 hover:underline">#{{ $log->timesheet_id }}</a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $log->status->name ?? '---' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $log->employees_count }} чел.</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $log->created_at->format('d.m.Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($log->success)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">✅ Успешно</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">❌ Ошибка</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('telegram-logs.show', $log->id) }}" class="text-blue-600 hover:underline mr-3">👁 Просмотр</a>
                            <form action="{{ route('telegram-logs.destroy', $log->id) }}" method="POST" class="inline" onsubmit="return confirm('Удалить этот отчет?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">🗑 Удалить</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">📭 Нет отправленных отчетов</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>
</div>

{{-- 🔹 СКРИПТЫ --}}
<script>
// 🔹 CSRF токен - несколько способов получения
const csrfToken =
    document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
    document.querySelector('input[name="_token"]')?.value ||
    '{{ csrf_token() }}';

console.log('CSRF Token:', csrfToken ? '✅ Найден' : '❌ Не найден');

// Выделение всех
document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.log-checkbox').forEach(cb => cb.checked = this.checked);
    updateSelectedCount();
});

document.querySelectorAll('.log-checkbox').forEach(cb => {
    cb.addEventListener('change', updateSelectedCount);
});

function updateSelectedCount() {
    const count = document.querySelectorAll('.log-checkbox:checked').length;
    document.getElementById('selectedCount').innerText = `(${count} выбрано)`;
}

// Удаление выбранных
function deleteSelected() {
    const checked = document.querySelectorAll('.log-checkbox:checked');
    if (checked.length === 0) {
        alert('Выберите хотя бы один отчет!');
        return;
    }
    if (!confirm(`Удалить ${checked.length} отчет(ов)?`)) return;

    console.log('Удаление ID:', Array.from(checked).map(cb => cb.value));
    console.log('CSRF Token:', csrfToken);

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/telegram-logs/bulk-delete';

    // CSRF
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = csrfToken;
    form.appendChild(csrf);

    // DELETE method
    const method = document.createElement('input');
    method.type = 'hidden';
    method.name = '_method';
    method.value = 'DELETE';
    form.appendChild(method);

    // IDs
    checked.forEach(cb => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'log_ids[]';
        input.value = cb.value;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
}

// Очистка всей истории
function confirmClearAll() {
    if (!confirm('⚠ Удалить ВСЮ историю?')) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/telegram-logs/clear-all';

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = csrfToken;
    form.appendChild(csrf);

    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection
