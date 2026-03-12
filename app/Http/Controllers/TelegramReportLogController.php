<?php

namespace App\Http\Controllers;

use App\Models\TelegramReportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramReportLogController extends Controller
{
    public function index(Request $request)
    {
        $query = TelegramReportLog::with(['timesheet', 'status'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('timesheet_id')) {
            $query->where('timesheet_id', $request->timesheet_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('success')) {
            $query->where('success', $request->success === '1');
        }

        $logs = $query->paginate(20);
        return view('telegram-logs.index', compact('logs'));
    }

    public function show($id)
    {
        $log = TelegramReportLog::with(['timesheet', 'status'])->findOrFail($id);
        return view('telegram-logs.show', compact('log'));
    }

    public function resend($id)
    {
        $log = TelegramReportLog::findOrFail($id);

        try {
            $response = Http::withoutVerifying()->post(
                "https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage",
                [
                    'chat_id' => env('TELEGRAM_CHAT_ID'),
                    'text' => $log->message,
                    'parse_mode' => 'Markdown',
                ]
            );

            if ($response->successful()) {
                TelegramReportLog::create([
                    'timesheet_id' => $log->timesheet_id,
                    'date' => $log->date,
                    'status_id' => $log->status_id,
                    'employees_count' => $log->employees_count,
                    'message' => $log->message,
                    'fields' => $log->fields,
                    'sent_by' => auth()->user()->email ?? 'system',
                    'success' => true,
                    'error_message' => null,
                ]);
                return back()->with('success', "Отчет повторно отправлен!");
            }
        } catch (\Exception $e) {
            return back()->with('error', "Ошибка: " . $e->getMessage());
        }

        return back()->with('error', "Не удалось отправить отчет");
    }

    public function destroy($id)
{
    $log = TelegramReportLog::findOrFail($id);
    $log->delete();

    // 🔹 ИСПРАВЛЕНИЕ: редирект на явный маршрут, а не back()
    return redirect()->route('telegram-logs.index')
        ->with('success', "Отчет удален из истории!");
}

public function bulkDestroy(Request $request)
{
    $ids = $request->input('log_ids', []);

    if (empty($ids)) {
        return redirect()->route('telegram-logs.index')
            ->with('error', "Не выбрано ни одного отчета для удаления");
    }

    $count = \App\Models\TelegramReportLog::whereIn('id', $ids)->delete();

    return redirect()->route('telegram-logs.index')
        ->with('success', "Удалено отчетов: {$count}");
}

public function clearAll()
{
    $count = TelegramReportLog::count();
    TelegramReportLog::truncate();
    return redirect()->route('telegram-logs.index')
        ->with('success', "Вся история очищена ({$count} отчетов)!");
}
}
