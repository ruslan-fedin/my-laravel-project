@extends('layouts.app')

@section('title', 'Просмотр отчета Telegram')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📋 Детали отчета Telegram</h1>
        <a href="{{ route('telegram-logs.index') }}" class="text-blue-600 hover:underline">← Назад к списку</a>
    </div>

    {{-- Сообщения об успехе/ошибке --}}
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Основная информация --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">📊 Информация</h2>

                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-500">Дата отчета:</span>
                        <div class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($log->date)->format('d.m.Y') }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500">Дата отправки:</span>
                        <div class="font-semibold text-gray-800">{{ $log->created_at->format('d.m.Y H:i') }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500">Табель:</span>
                        <div>
                            <a href="{{ route('travel-timesheets.show', $log->timesheet_id) }}" class="text-blue-600 hover:underline">
                                #{{ $log->timesheet_id }}
                            </a>
                        </div>
                    </div>
                    <div>
                        <span class="text-gray-500">Статус:</span>
                        <div class="font-semibold text-gray-800">{{ $log->status->name ?? '---' }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500">Сотрудников:</span>
                        <div class="font-semibold text-gray-800">{{ $log->employees_count }} чел.</div>
                    </div>
                    <div>
                        <span class="text-gray-500">Отправил:</span>
                        <div class="font-semibold text-gray-800">{{ $log->sent_by ?? 'system' }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500">Результат:</span>
                        <div>
                            @if($log->success)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">✅ Успешно</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">❌ Ошибка</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if(!$log->success && $log->error_message)
                    <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <div class="text-xs font-bold text-red-800 mb-1">Ошибка:</div>
                        <div class="text-xs text-red-600">{{ $log->error_message }}</div>
                    </div>
                @endif

                {{-- 🔹 ДЕЙСТВИЯ --}}
                <div class="mt-6 space-y-2">
                    @if($log->success)
                        <form action="{{ route('telegram-logs.resend', $log->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors text-sm font-bold uppercase">
                                🔄 Повторно отправить
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('travel-timesheets.show', $log->timesheet_id) }}" class="block w-full bg-blue-600 text-white text-center px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-bold uppercase">
                        📄 Открыть табель
                    </a>
                    {{-- 🔹 КНОПКА УДАЛЕНИЯ --}}
                    <form action="{{ route('telegram-logs.destroy', $log->id) }}" method="POST" onsubmit="return confirm('⚠ Удалить этот отчет из истории? Это действие нельзя отменить.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors text-sm font-bold uppercase">
                            🗑 Удалить из истории
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Сообщение Telegram --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">📨 Сообщение Telegram</h2>

                {{-- Предпросмотр сообщения --}}
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                    <div class="text-xs font-bold text-gray-500 uppercase mb-2">Предпросмотр:</div>
                    <div class="whitespace-pre-wrap text-sm text-gray-800 font-mono bg-white p-4 rounded border border-gray-200 max-h-96 overflow-y-auto">
                        {{ $log->message }}
                    </div>
                </div>

                {{-- Поля отчета --}}
                @if($log->fields && count($log->fields) > 0)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="text-xs font-bold text-blue-800 uppercase mb-3">📝 Заполненные поля:</div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                            @foreach($log->fields as $key => $value)
                                <div class="flex justify-between items-center bg-white px-3 py-2 rounded border border-blue-100">
                                    <span class="text-gray-500 text-xs uppercase">{{ str_replace('_', ' ', str_replace('custom_', '', $key)) }}</span>
                                    <span class="font-semibold text-gray-800 truncate max-w-xs">{{ $value }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Кнопка копирования --}}
                <div class="mt-4">
                    <button onclick="copyMessage()" class="text-blue-600 hover:underline text-sm font-bold flex items-center gap-2">
                        📋 Копировать текст сообщения
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyMessage() {
    const message = @json($log->message);
    navigator.clipboard.writeText(message).then(() => {
        alert('Сообщение скопировано в буфер обмена!');
    }).catch(err => {
        alert('Ошибка копирования: ' + err);
    });
}
</script>
@endsection
