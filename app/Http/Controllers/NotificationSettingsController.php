<?php

namespace App\Http\Controllers;

use App\Models\NotificationSetting;
use App\Models\NotificationLog;
use App\Models\ReportTemplate;
use App\Services\TelegramVacationService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificationSettingsController extends Controller
{
    protected TelegramVacationService $telegramService;

    public function __construct(TelegramVacationService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    public function index()
    {
        $settings = NotificationSetting::all()->keyBy('notification_type');
        $logs = NotificationLog::orderBy('created_at', 'desc')->limit(10)->get();
        $templates = ReportTemplate::all();
        $presets = ReportTemplate::getPresets();

        return view('notifications.settings', compact('settings', 'logs', 'templates', 'presets'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'vacation_30_days' => 'boolean',
            'vacation_20_days' => 'boolean',
            'vacation_14_days' => 'boolean',
            'vacation_7_days' => 'boolean',
        ]);

        $this->updateSetting('vacation_30_days', $request->vacation_30_days);
        $this->updateSetting('vacation_20_days', $request->vacation_20_days);
        $this->updateSetting('vacation_14_days', $request->vacation_14_days);
        $this->updateSetting('vacation_7_days', $request->vacation_7_days);

        return back()->with('success', '✅ Настройки сохранены');
    }

    public function sendSummary(Request $request)
    {
        $chatId = env('TELEGRAM_CHAT_ID');

        if (!$chatId) {
            return back()->with('error', '❌ TELEGRAM_CHAT_ID не настроен');
        }

        $periodType = $request->input('period_type', '12');
        $format = $request->input('format', 'short');
        $months = 12;
        $endDate = null;

        if ($periodType === 'month') {
            $months = 1;
            if ($request->filled('report_month')) {
                $endDate = Carbon::parse($request->report_month)->endOfMonth();
            } else {
                $endDate = now()->endOfMonth();
            }
        } elseif ($periodType === 'quarter') {
            $months = 3;
            $quarter = (int) $request->input('quarter', 4);
            $year = (int) $request->input('quarter_year_hidden', now()->year);
            $quarterEndMonth = $quarter * 3;
            $endDate = Carbon::create($year, $quarterEndMonth, 1)->endOfMonth();
        } elseif ($periodType === 'half') {
            $months = 6;
            $half = (int) $request->input('half', 2);
            $year = (int) $request->input('half_year_hidden', now()->year);
            $halfEndMonth = $half * 6;
            $endDate = Carbon::create($year, $halfEndMonth, 1)->endOfMonth();
        } elseif ($periodType === '12') {
            $months = 12;
            $endDate = now()->endOfYear();
        }

        $result = $this->telegramService->sendPeriodSummary($chatId, $months, $format, $endDate);

        if ($result['success']) {
            return back()->with('success', "✅ Сводка за {$months} мес. отправлена");
        }

        return back()->with('error', '❌ Ошибка: ' . $result['error']);
    }

    public function saveTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'months' => 'required|integer|min:1|max:24',
            'format' => 'required|in:short,detailed',
        ]);

        ReportTemplate::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'months' => $request->months,
            'format' => $request->format,
        ]);

        return back()->with('success', '✅ Шаблон сохранён');
    }

    public function sendFromTemplate($id)
    {
        $chatId = env('TELEGRAM_CHAT_ID');

        if (!$chatId) {
            return back()->with('error', '❌ TELEGRAM_CHAT_ID не настроен');
        }

        $template = ReportTemplate::findOrFail($id);
        $result = $this->telegramService->sendPeriodSummary($chatId, $template->months, $template->format);

        if ($result['success']) {
            return back()->with('success', "✅ '{$template->name}' отправлен");
        }

        return back()->with('error', '❌ Ошибка: ' . $result['error']);
    }

    public function deleteTemplate($id)
    {
        ReportTemplate::destroy($id);
        return back()->with('success', '✅ Шаблон удалён');
    }

    /**
     * Просмотр всех логов
     */
    public function logs()
    {
        $logs = NotificationLog::orderBy('created_at', 'desc')->paginate(50);
        return view('notifications.logs', compact('logs'));
    }

    /**
     * Удалить одну запись лога
     */
    public function deleteLog($id)
    {
        NotificationLog::destroy($id);
        return back()->with('success', '✅ Запись удалена');
    }

    /**
     * Очистить все логи
     */
    public function clearLogs()
    {
        NotificationLog::truncate();
        return back()->with('success', '✅ История очищена');
    }

    private function updateSetting(string $type, bool $enabled)
    {
        NotificationSetting::updateOrCreate(['notification_type' => $type], ['enabled' => $enabled]);
    }
}
