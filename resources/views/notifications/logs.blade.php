@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 p-6">

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-4xl font-black text-slate-900">📝 История отправок</h1>
            <p class="text-slate-500 mt-2">Все отправленные уведомления</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('notifications.settings') }}" class="bg-white px-6 py-3 rounded-2xl font-bold border hover:bg-slate-50">← Назад</a>
            <form action="{{ route('notifications.logs.clear') }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-rose-600 text-white px-6 py-3 rounded-2xl font-bold hover:bg-rose-700 transition" onclick="return confirm('Удалить всю историю?')">
                    🗑️ Очистить всё
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
        <span class="font-bold text-emerald-700">{{ session('success') }}</span>
    </div>
    @endif

    {{-- ФИЛЬТРЫ --}}
    <div class="bg-white rounded-3xl p-6 shadow-lg mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Тип отчёта</label>
                <select id="filterType" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500" onchange="filterLogs()">
                    <option value="">Все типы</option>
                    <option value="monthly_summary">Ежемесячный</option>
                    <option value="period_summary">Период</option>
                    <option value="test">Тестовый</option>
                    <option value="vacation">Напоминание</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Статус</label>
                <select id="filterStatus" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500" onchange="filterLogs()">
                    <option value="">Все</option>
                    <option value="sent">✅ Отправлено</option>
                    <option value="error">❌ Ошибка</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Дата с</label>
                <input type="date" id="filterDateFrom" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500" onchange="filterLogs()">
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Дата по</label>
                <input type="date" id="filterDateTo" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500" onchange="filterLogs()">
            </div>
        </div>
    </div>

    {{-- ТАБЛИЦА --}}
    <div class="bg-white rounded-3xl p-6 shadow-lg">
        <div class="overflow-x-auto">
            <table class="w-full" id="logsTable">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-3 px-4 text-[10px] font-black text-slate-400 uppercase">Дата</th>
                        <th class="text-left py-3 px-4 text-[10px] font-black text-slate-400 uppercase">Тип</th>
                        <th class="text-left py-3 px-4 text-[10px] font-black text-slate-400 uppercase">Период</th>
                        <th class="text-left py-3 px-4 text-[10px] font-black text-slate-400 uppercase">Получатель</th>
                        <th class="text-left py-3 px-4 text-[10px] font-black text-slate-400 uppercase">Статус</th>
                        <th class="text-left py-3 px-4 text-[10px] font-black text-slate-400 uppercase">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr class="border-b border-slate-100 hover:bg-slate-50" data-type="{{ $log->type }}" data-status="{{ $log->sent ? 'sent' : 'error' }}" data-date="{{ $log->created_at->format('Y-m-d') }}">
                        <td class="py-3 px-4 text-sm">
                            <div class="font-bold text-slate-900">{{ $log->created_at->format('d.m.Y') }}</div>
                            <div class="text-xs text-slate-500">{{ $log->created_at->format('H:i') }}</div>
                        </td>
                        <td class="py-3 px-4">
                            @php
                                $typeConfig = [
                                    'monthly_summary' => ['label' => '📅 Ежемесячный', 'color' => 'bg-blue-100 text-blue-700'],
                                    'period_summary_1m' => ['label' => '📅 1 месяц', 'color' => 'bg-indigo-100 text-indigo-700'],
                                    'period_summary_3m' => ['label' => '📊 Квартал', 'color' => 'bg-purple-100 text-purple-700'],
                                    'period_summary_6m' => ['label' => '📈 Полугодие', 'color' => 'bg-pink-100 text-pink-700'],
                                    'period_summary_12m' => ['label' => '📅 Год', 'color' => 'bg-emerald-100 text-emerald-700'],
                                    'test' => ['label' => '🧪 Тест', 'color' => 'bg-slate-100 text-slate-700'],
                                    'vacation_30_days' => ['label' => '📅 30 дней', 'color' => 'bg-amber-100 text-amber-700'],
                                    'vacation_20_days' => ['label' => '⏰ 20 дней', 'color' => 'bg-orange-100 text-orange-700'],
                                    'vacation_14_days' => ['label' => '⏰ 14 дней', 'color' => 'bg-yellow-100 text-yellow-700'],
                                    'vacation_7_days' => ['label' => '🔔 7 дней', 'color' => 'bg-rose-100 text-rose-700'],
                                ];
                                $config = $typeConfig[$log->type] ?? ['label' => $log->type, 'color' => 'bg-slate-100 text-slate-700'];
                            @endphp
                            <span class="text-xs font-bold px-3 py-1 rounded-lg {{ $config['color'] }}">{{ $config['label'] }}</span>
                        </td>
                        <td class="py-3 px-4 text-sm text-slate-600">
                            @php
                                $period = '';
                                if (preg_match('/📅 <b>(.*?)<\/b>/u', $log->message, $matches)) {
                                    $period = $matches[1];
                                }
                            @endphp
                            {{ $period ?: '—' }}
                        </td>
                        <td class="py-3 px-4 text-sm font-bold text-slate-900">{{ $log->recipient }}</td>
                        <td class="py-3 px-4">
                            @if($log->sent)
                            <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                                <i class="fas fa-check-circle"></i> Отправлено
                            </span>
                            @else
                            <span class="text-xs font-bold text-rose-600 flex items-center gap-1">
                                <i class="fas fa-times-circle"></i> Ошибка
                            </span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <button onclick="openLogModal({{ $log->id }}, '{{ addslashes($log->type) }}', '{{ addslashes($log->message) }}', '{{ $log->created_at->format('d.m.Y H:i') }}', {{ $log->sent ? 'true' : 'false' }}, '{{ $log->error ?? '' }}')"
                                    class="bg-indigo-600 text-white px-4 py-2 rounded-xl font-bold text-xs hover:bg-indigo-700 transition">
                                👁️ Просмотр
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">
                            <i class="fas fa-inbox text-3xl mb-2"></i>
                            <p class="text-sm">История пуста</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="mt-6">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>

{{-- МОДАЛЬНОЕ ОКНО --}}
<div id="logModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[80vh] overflow-y-auto shadow-2xl">
        <div class="sticky top-0 bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-black text-slate-900">📋 Детали отправки</h3>
            <button onclick="closeLogModal()" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 transition flex items-center justify-center">
                <i class="fas fa-times text-slate-600"></i>
            </button>
        </div>

        <div class="p-6 space-y-6">
            {{-- ИНФОРМАЦИЯ --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-slate-50 rounded-xl">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Дата отправки</p>
                    <p class="font-bold text-slate-900" id="modalDate">—</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-xl">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Статус</p>
                    <p class="font-bold" id="modalStatus">—</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-slate-50 rounded-xl">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Тип отчёта</p>
                    <p class="font-bold text-slate-900" id="modalType">—</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-xl">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Получатель</p>
                    <p class="font-bold text-slate-900" id="modalRecipient">—</p>
                </div>
            </div>

            {{-- СООБЩЕНИЕ --}}
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">📄 Текст сообщения</p>
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                    <pre class="text-sm text-slate-700 whitespace-pre-wrap font-sans" id="modalMessage">—</pre>
                </div>
            </div>

            {{-- ОШИБКА --}}
            <div id="modalErrorBlock" class="hidden">
                <p class="text-[10px] font-black text-rose-400 uppercase tracking-widest mb-3">⚠️ Ошибка</p>
                <div class="bg-rose-50 rounded-xl p-4 border border-rose-200">
                    <p class="text-sm text-rose-700" id="modalError">—</p>
                </div>
            </div>

            {{-- СТАТИСТИКА --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="p-4 bg-indigo-50 rounded-xl text-center">
                    <p class="text-2xl font-black text-indigo-600" id="modalEmployees">—</p>
                    <p class="text-[9px] font-bold text-indigo-400 uppercase">Сотрудников</p>
                </div>
                <div class="p-4 bg-purple-50 rounded-xl text-center">
                    <p class="text-2xl font-black text-purple-600" id="modalDays">—</p>
                    <p class="text-[9px] font-bold text-purple-400 uppercase">Дней всего</p>
                </div>
                <div class="p-4 bg-emerald-50 rounded-xl text-center">
                    <p class="text-2xl font-black text-emerald-600" id="modalMonths">—</p>
                    <p class="text-[9px] font-bold text-emerald-400 uppercase">Месяцев</p>
                </div>
            </div>
        </div>

        <div class="sticky bottom-0 bg-white border-t border-slate-200 px-6 py-4 flex gap-3">
            <button onclick="closeLogModal()" class="flex-1 bg-slate-900 text-white px-6 py-3 rounded-xl font-bold hover:bg-slate-800 transition">
                Закрыть
            </button>
        </div>
    </div>
</div>

<script>
function openLogModal(id, type, message, date, sent, error) {
    document.getElementById('modalDate').textContent = date;
    document.getElementById('modalStatus').textContent = sent ? '✅ Отправлено' : '❌ Ошибка';
    document.getElementById('modalStatus').className = 'font-bold ' + (sent ? 'text-emerald-600' : 'text-rose-600');
    document.getElementById('modalType').textContent = type;
    document.getElementById('modalMessage').textContent = message.replace(/<[^>]*>/g, '');
    document.getElementById('modalRecipient').textContent = '{{ env('TELEGRAM_CHAT_ID') }}';

    // Ошибка
    if (error && !sent) {
        document.getElementById('modalErrorBlock').classList.remove('hidden');
        document.getElementById('modalError').textContent = error;
    } else {
        document.getElementById('modalErrorBlock').classList.add('hidden');
    }

    // Статистика из сообщения
    const employeesMatch = message.match(/Сотрудников:\s*(\d+)/);
    const daysMatch = message.match(/Всего дней:\s*(\d+)/);
    const periodMatch = message.match(/📅\s*<b>(.*?)<\/b>/u);

    document.getElementById('modalEmployees').textContent = employeesMatch ? employeesMatch[1] : '—';
    document.getElementById('modalDays').textContent = daysMatch ? daysMatch[1] : '—';

    // Определяем количество месяцев из типа
    let months = '—';
    if (type.includes('1m')) months = '1';
    else if (type.includes('3m')) months = '3';
    else if (type.includes('6m')) months = '6';
    else if (type.includes('12m')) months = '12';
    else if (type.includes('monthly')) months = '1';
    document.getElementById('modalMonths').textContent = months;

    document.getElementById('logModal').classList.remove('hidden');
    document.getElementById('logModal').classList.add('flex');
}

function closeLogModal() {
    document.getElementById('logModal').classList.add('hidden');
    document.getElementById('logModal').classList.remove('flex');
}

function filterLogs() {
    const type = document.getElementById('filterType').value;
    const status = document.getElementById('filterStatus').value;
    const dateFrom = document.getElementById('filterDateFrom').value;
    const dateTo = document.getElementById('filterDateTo').value;

    document.querySelectorAll('#logsTable tbody tr').forEach(row => {
        const rowType = row.dataset.type;
        const rowStatus = row.dataset.status;
        const rowDate = row.dataset.date;

        let show = true;

        if (type && !rowType.includes(type)) show = false;
        if (status && rowStatus !== status) show = false;
        if (dateFrom && rowDate < dateFrom) show = false;
        if (dateTo && rowDate > dateTo) show = false;

        row.style.display = show ? '' : 'none';
    });
}

// Закрытие по ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLogModal();
});

// Закрытие по клику вне модального окна
document.getElementById('logModal').addEventListener('click', function(e) {
    if (e.target === this) closeLogModal();
});
</script>
@endsection
