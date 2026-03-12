@extends('layouts.app')
@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-4xl font-black text-slate-900">🔔 Настройки уведомлений</h1>
        <p class="text-slate-500 mt-2">Управление Telegram-уведомлениями об отпусках</p>
    </div>
    <a href="{{ route('vacations.timeline') }}" class="bg-white px-6 py-3 rounded-2xl font-bold border hover:bg-slate-50">← Назад к отпускам</a>
</div>

@if(session('success'))
<div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 mb-6 flex items-center gap-3">
    <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
    <span class="font-bold text-emerald-700">{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="bg-rose-50 border border-rose-200 rounded-2xl p-4 mb-6 flex items-center gap-3">
    <i class="fas fa-exclamation-circle text-rose-600 text-xl"></i>
    <span class="font-bold text-rose-700">{{ session('error') }}</span>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="bg-white rounded-3xl p-6 shadow-lg">
        <h3 class="text-xl font-black text-slate-900 mb-6">⏰ Напоминания</h3>

        <form action="{{ route('notifications.settings.update') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                    <div>
                        <p class="font-bold text-slate-900">📅 За 30 дней</p>
                        <p class="text-xs text-slate-500">Планирование</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="vacation_30_days" value="1" {{ ($settings['vacation_30_days'] ?? null)?->enabled ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-14 h-7 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                    <div>
                        <p class="font-bold text-slate-900">⏰ За 20 дней</p>
                        <p class="text-xs text-slate-500">Напоминание</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="vacation_20_days" value="1" {{ ($settings['vacation_20_days'] ?? null)?->enabled ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-14 h-7 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                    <div>
                        <p class="font-bold text-slate-900">⏰ За 14 дней</p>
                        <p class="text-xs text-slate-500">Напоминание</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="vacation_14_days" value="1" {{ ($settings['vacation_14_days'] ?? null)?->enabled ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-14 h-7 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                    <div>
                        <p class="font-bold text-slate-900">🔔 За 7 дней</p>
                        <p class="text-xs text-slate-500">Срочно</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="vacation_7_days" value="1" {{ ($settings['vacation_7_days'] ?? null)?->enabled ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-14 h-7 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white px-6 py-4 rounded-2xl font-bold hover:bg-indigo-700 transition mt-6">
                    💾 Сохранить
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-lg">
        <h3 class="text-xl font-black text-slate-900 mb-6">📊 Период отчёта</h3>

        <form action="{{ route('notifications.send-summary') }}" method="POST" id="reportForm">
            @csrf

            <div class="space-y-5">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Период</label>
                    <select name="period_type" id="period_type" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" onchange="togglePeriodOptions()">
                        <option value="month">📅 1 месяц</option>
                        <option value="quarter">📊 Квартал</option>
                        <option value="half">📈 Полугодие</option>
                        <option value="12" selected>📅 12 месяцев (год)</option>
                    </select>
                </div>

                <div id="monthSelector" class="hidden">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Выберите месяц</label>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="text-[9px] font-bold text-slate-500 block mb-2">Год</label>
                            <select name="report_year" id="report_year" class="w-full px-3 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500" onchange="updateHiddenMonth()">
                                <option value="2025">2025</option>
                                <option value="2026" selected>2026</option>
                                <option value="2027">2027</option>
                                <option value="2028">2028</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[9px] font-bold text-slate-500 block mb-2">Месяц</label>
                            <select name="report_month_select" id="report_month_select" class="w-full px-3 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500" onchange="updateHiddenMonth()">
                                <option value="01">Январь</option>
                                <option value="02">Февраль</option>
                                <option value="03">Март</option>
                                <option value="04">Апрель</option>
                                <option value="05">Май</option>
                                <option value="06">Июнь</option>
                                <option value="07">Июль</option>
                                <option value="08">Август</option>
                                <option value="09">Сентябрь</option>
                                <option value="10">Октябрь</option>
                                <option value="11">Ноябрь</option>
                                <option value="12">Декабрь</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="report_month" id="report_month" value="2026-03">
                    <p class="text-[9px] text-slate-500">📅 Выбрано: <span id="selectedMonth" class="font-bold text-indigo-600">Март 2026</span></p>
                </div>

                <div id="quarterSelector" class="hidden">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Выберите квартал</label>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="text-[9px] font-bold text-slate-500 block mb-2">Год</label>
                            <select name="quarter_year" id="quarter_year" class="w-full px-3 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500" onchange="updateHiddenQuarter()">
                                <option value="2025">2025</option>
                                <option value="2026" selected>2026</option>
                                <option value="2027">2027</option>
                                <option value="2028">2028</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[9px] font-bold text-slate-500 block mb-2">Квартал</label>
                            <select name="quarter_select" id="quarter_select" class="w-full px-3 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500" onchange="updateHiddenQuarter()">
                                <option value="1">1 квартал (Янв-Март)</option>
                                <option value="2">2 квартал (Апр-Июн)</option>
                                <option value="3">3 квартал (Июл-Сен)</option>
                                <option value="4" selected>4 квартал (Окт-Дек)</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="quarter" id="quarter" value="4">
                    <input type="hidden" name="quarter_year_hidden" id="quarter_year_hidden" value="2026">
                    <p class="text-[9px] text-slate-500">📅 Выбрано: <span id="selectedQuarter" class="font-bold text-indigo-600">4 квартал 2026</span></p>
                </div>

                <div id="halfSelector" class="hidden">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Выберите полугодие</label>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="text-[9px] font-bold text-slate-500 block mb-2">Год</label>
                            <select name="half_year" id="half_year" class="w-full px-3 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500" onchange="updateHiddenHalf()">
                                <option value="2025">2025</option>
                                <option value="2026" selected>2026</option>
                                <option value="2027">2027</option>
                                <option value="2028">2028</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[9px] font-bold text-slate-500 block mb-2">Полугодие</label>
                            <select name="half_select" id="half_select" class="w-full px-3 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500" onchange="updateHiddenHalf()">
                                <option value="1">1-е полугодие (Янв-Июн)</option>
                                <option value="2" selected>2-е полугодие (Июл-Дек)</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="half" id="half" value="2">
                    <input type="hidden" name="half_year_hidden" id="half_year_hidden" value="2026">
                    <p class="text-[9px] text-slate-500">📅 Выбрано: <span id="selectedHalf" class="font-bold text-indigo-600">2-е полугодие 2026</span></p>
                </div>

                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Формат</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="format" value="short" checked class="sr-only peer">
                            <div class="p-3 rounded-xl border border-slate-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 text-center transition">
                                <p class="font-bold text-slate-900 text-sm">📝 Краткий</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="format" value="detailed" class="sr-only peer">
                            <div class="p-3 rounded-xl border border-slate-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 text-center transition">
                                <p class="font-bold text-slate-900 text-sm">📋 Подробный</p>
                            </div>
                        </label>
                    </div>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-4 rounded-2xl font-bold hover:from-indigo-700 hover:to-purple-700 transition shadow-lg shadow-indigo-200">
                    📤 Отправить отчёт
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-lg">
        <h3 class="text-xl font-black text-slate-900 mb-6">💾 Шаблоны</h3>

        <div class="space-y-3">
            @forelse($templates as $template)
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                <div class="flex justify-between items-center mb-2">
                    <p class="font-bold text-slate-900 text-sm">{{ $template->name }}</p>
                    <form action="{{ route('notifications.delete-template', $template->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-rose-500 hover:text-rose-700" onclick="return confirm('Удалить шаблон?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
                <p class="text-xs text-slate-500 mb-3">{{ $template->months }} мес. • {{ $template->format === 'short' ? 'Краткий' : 'Подробный' }}</p>
                <form action="{{ route('notifications.send-from-template', $template->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-xl font-bold text-xs hover:bg-indigo-700 transition">
                        📤 Отправить
                    </button>
                </form>
            </div>
            @empty
            <p class="text-sm text-slate-500 text-center py-6">Нет сохранённых шаблонов</p>
            @endforelse
        </div>

        <div class="mt-6 pt-6 border-t border-slate-200">
            <h4 class="text-sm font-bold text-slate-700 mb-3">Сохранить текущий как шаблон</h4>
            <form action="{{ route('notifications.save-template') }}" method="POST">
                @csrf
                <input type="text" name="name" placeholder="Название шаблона" class="w-full px-4 py-3 rounded-xl border border-slate-200 mb-3 focus:ring-2 focus:ring-indigo-500" required>
                <input type="hidden" name="months" value="12" id="template_months">
                <input type="hidden" name="format" value="short" id="template_format">
                <button type="submit" class="w-full bg-slate-900 text-white px-4 py-3 rounded-xl font-bold hover:bg-slate-800 transition text-sm">
                    💾 Сохранить шаблон
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-lg lg:col-span-3">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-black text-slate-900">📝 История отправок</h3>
            <div class="flex gap-2">
                <a href="{{ route('notifications.logs') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-xl font-bold text-sm hover:bg-indigo-700 transition">
                    👁️ Просмотреть все
                </a>
                <form action="{{ route('notifications.logs.clear') }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-rose-600 text-white px-4 py-2 rounded-xl font-bold text-sm hover:bg-rose-700 transition" onclick="return confirm('Удалить всю историю?')">
                        🗑️ Очистить
                    </button>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
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
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
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
                            <div class="flex gap-2">
                                <button type="button" onclick="openModal({{ $log->id }})"
                                       class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg font-bold text-xs hover:bg-indigo-700 transition">
                                    👁️
                                </button>
                                <form action="{{ route('notifications.logs.delete', $log->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-rose-600 text-white px-3 py-1.5 rounded-lg font-bold text-xs hover:bg-rose-700 transition" onclick="return confirm('Удалить запись?')" title="Удалить">
                                        🗑️
                                    </button>
                                </form>
                            </div>
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
    </div>

</div>

<div id="logModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[80vh] overflow-y-auto shadow-2xl">
        <div class="sticky top-0 bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-black text-slate-900">📋 Детали отправки</h3>
            <button type="button" onclick="closeModal()" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 transition flex items-center justify-center">
                <i class="fas fa-times text-slate-600"></i>
            </button>
        </div>

        <div class="p-6 space-y-6">
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

            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">📄 Текст сообщения</p>
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                    <pre class="text-sm text-slate-700 whitespace-pre-wrap font-sans" id="modalMessage">—</pre>
                </div>
            </div>

            <div id="modalErrorBlock" class="hidden">
                <p class="text-[10px] font-black text-rose-400 uppercase tracking-widest mb-3">⚠️ Ошибка</p>
                <div class="bg-rose-50 rounded-xl p-4 border border-rose-200">
                    <p class="text-sm text-rose-700" id="modalError">—</p>
                </div>
            </div>

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

        <div class="sticky bottom-0 bg-white border-t border-slate-200 px-6 py-4">
            <button type="button" onclick="closeModal()" class="w-full bg-slate-900 text-white px-6 py-3 rounded-xl font-bold hover:bg-slate-800 transition">
                Закрыть
            </button>
        </div>
    </div>
</div>

<script>
const logData = {
    @foreach($logs as $log)
    {{ $log->id }}: {
        message: {!! json_encode($log->message) !!},
        date: '{{ $log->created_at->format('d.m.Y H:i') }}',
        sent: {{ $log->sent ? 'true' : 'false' }},
        error: {!! json_encode($log->error) !!},
        type: '{{ $log->type }}',
        recipient: '{{ $log->recipient }}'
    },
    @endforeach
};

function openModal(logId) {
    const data = logData[logId];
    if (!data) return;

    document.getElementById('modalDate').textContent = data.date;
    document.getElementById('modalStatus').textContent = data.sent ? '✅ Отправлено' : '❌ Ошибка';
    document.getElementById('modalStatus').className = 'font-bold ' + (data.sent ? 'text-emerald-600' : 'text-rose-600');
    document.getElementById('modalType').textContent = data.type;
    document.getElementById('modalMessage').textContent = data.message.replace(/<[^>]*>/g, '');
    document.getElementById('modalRecipient').textContent = data.recipient;

    if (data.error && !data.sent) {
        document.getElementById('modalErrorBlock').classList.remove('hidden');
        document.getElementById('modalError').textContent = data.error;
    } else {
        document.getElementById('modalErrorBlock').classList.add('hidden');
    }

    const employeesMatch = data.message.match(/Сотрудников:\s*(\d+)/);
    const daysMatch = data.message.match(/Всего дней:\s*(\d+)/);

    document.getElementById('modalEmployees').textContent = employeesMatch ? employeesMatch[1] : '—';
    document.getElementById('modalDays').textContent = daysMatch ? daysMatch[1] : '—';

    let months = '—';
    if (data.type.includes('1m')) months = '1';
    else if (data.type.includes('3m')) months = '3';
    else if (data.type.includes('6m')) months = '6';
    else if (data.type.includes('12m')) months = '12';
    else if (data.type.includes('monthly')) months = '1';
    document.getElementById('modalMonths').textContent = months;

    const modal = document.getElementById('logModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal() {
    const modal = document.getElementById('logModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

const monthNames = {
    '01': 'Январь', '02': 'Февраль', '03': 'Март', '04': 'Апрель',
    '05': 'Май', '06': 'Июнь', '07': 'Июль', '08': 'Август',
    '09': 'Сентябрь', '10': 'Октябрь', '11': 'Ноябрь', '12': 'Декабрь'
};

const quarterNames = {
    '1': '1 квартал', '2': '2 квартал', '3': '3 квартал', '4': '4 квартал'
};

const halfNames = {
    '1': '1-е полугодие', '2': '2-е полугодие'
};

function togglePeriodOptions() {
    const periodType = document.getElementById('period_type');
    const monthSelector = document.getElementById('monthSelector');
    const quarterSelector = document.getElementById('quarterSelector');
    const halfSelector = document.getElementById('halfSelector');
    const templateMonths = document.getElementById('template_months');

    if (periodType.value === 'month') {
        monthSelector.classList.remove('hidden');
        quarterSelector.classList.add('hidden');
        halfSelector.classList.add('hidden');
        templateMonths.value = '1';
    } else if (periodType.value === 'quarter') {
        monthSelector.classList.add('hidden');
        quarterSelector.classList.remove('hidden');
        halfSelector.classList.add('hidden');
        templateMonths.value = '3';
    } else if (periodType.value === 'half') {
        monthSelector.classList.add('hidden');
        quarterSelector.classList.add('hidden');
        halfSelector.classList.remove('hidden');
        templateMonths.value = '6';
    } else {
        monthSelector.classList.add('hidden');
        quarterSelector.classList.add('hidden');
        halfSelector.classList.add('hidden');
        templateMonths.value = periodType.value;
    }
}

function updateHiddenMonth() {
    const year = document.getElementById('report_year').value;
    const month = document.getElementById('report_month_select').value;
    const hiddenInput = document.getElementById('report_month');
    const displaySpan = document.getElementById('selectedMonth');

    hiddenInput.value = year + '-' + month;
    displaySpan.textContent = monthNames[month] + ' ' + year;
}

function updateHiddenQuarter() {
    const year = document.getElementById('quarter_year').value;
    const quarter = document.getElementById('quarter_select').value;
    const hiddenQuarter = document.getElementById('quarter');
    const hiddenYear = document.getElementById('quarter_year_hidden');
    const displaySpan = document.getElementById('selectedQuarter');

    hiddenQuarter.value = quarter;
    hiddenYear.value = year;
    displaySpan.textContent = quarterNames[quarter] + ' ' + year;
}

function updateHiddenHalf() {
    const year = document.getElementById('half_year').value;
    const half = document.getElementById('half_select').value;
    const hiddenHalf = document.getElementById('half');
    const hiddenYear = document.getElementById('half_year_hidden');
    const displaySpan = document.getElementById('selectedHalf');

    hiddenHalf.value = half;
    hiddenYear.value = year;
    displaySpan.textContent = halfNames[half] + ' ' + year;
}

document.querySelectorAll('input[name="format"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('template_format').value = this.value;
    });
});

togglePeriodOptions();
updateHiddenMonth();
updateHiddenQuarter();
updateHiddenHalf();

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});

document.getElementById('logModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endsection
