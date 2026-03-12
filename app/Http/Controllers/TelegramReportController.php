<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;
use App\Models\TelegramReportLog;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TelegramReportController extends Controller
{
    public function sendCenterReport(Request $request)
    {
        $rawDate = $request->input('date');
        $statusId = $request->input('status_id');
        $timesheetId = $request->input('timesheet_id');

        $status = Status::find($statusId);
        $displayDate = Carbon::parse($rawDate)->format('d.m.Y');

        // Список сотрудников
        $items = DB::table('travel_timesheet_items')
            ->join('employees', 'travel_timesheet_items.employee_id', '=', 'employees.id')
            ->select('employees.last_name', 'employees.first_name', 'employees.middle_name')
            ->where('travel_timesheet_id', $timesheetId)
            ->where('date', $rawDate)
            ->where('status_id', $statusId)
            ->orderBy('employees.last_name')
            ->get();

        if ($items->isEmpty()) {
            return back()->with('error', "Нет людей на указанную дату.");
        }

        // Собираем сообщение
        $message = "";
        $message .= "📅 Дата: {$displayDate}\n";
        $message .= "──────────────────\n";

        foreach ($items as $index => $emp) {
            $fio = trim("{$emp->last_name} {$emp->first_name} {$emp->middle_name}");
            $message .= ($index + 1) . ". " . $fio . "\n";
        }

        $message .= "──────────────────\n";
        $message .= "ИТОГО: " . $items->count() . " чел.\n\n";

        // Стандартные поля
        $reportFields = [];
        if ($val = $request->input('master')) {
            $message .= "👤 МАСТЕР: {$val}\n";
            $reportFields['master'] = $val;
        }
        if ($val = $request->input('work_location')) {
            $message .= "📍 МЕСТО РАБОТЫ: {$val}\n";
            $reportFields['work_location'] = $val;
        }
        if ($val = $request->input('work_type')) {
            $message .= "⚙️ ВИД РАБОТЫ: {$val}\n";
            $reportFields['work_type'] = $val;
        }
        if ($val = $request->input('inventory')) {
            $message .= "⚒️ ИНСТРУМЕНТ: {$val}\n";
            $reportFields['inventory'] = $val;
        }
        if ($val = $request->input('departure')) {
            $message .= "🕒 ВЫЕЗД: {$val}\n";
            $reportFields['departure'] = $val;
        }
        if ($val = $request->input('transport')) {
            $message .= "🚚 ТРАНСПОРТ: {$val}\n";
            $reportFields['transport'] = $val;
        }
        if ($val = $request->input('notes')) {
            $message .= "🎒 ПРИМЕЧАНИЕ: {$val}\n";
            $reportFields['notes'] = $val;
        }

        // Динамические поля
        if ($json = $request->input('tg_data')) {
            $fields = json_decode($json, true);
            if (is_array($fields)) {
                foreach ($fields as $field) {
                    $name = mb_strtoupper(trim($field['name'] ?? ''), 'UTF-8');
                    $value = trim($field['value'] ?? '');
                    if ($name && $value) {
                        $message .= "🔹 {$name}: {$value}\n";
                        $reportFields['custom_' . md5($name)] = $value;
                    }
                }
            }
        }

        // Ссылка
        if ($val = $request->input('public_link')) {
            $message .= "\n🔗 Ссылка на табель:\n" . $val;
            $reportFields['public_link'] = $val;
        }

        // Отправка в Telegram
        $success = false;
        $errorMessage = null;

        try {
            $response = Http::withoutVerifying()->post(
                "https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage",
                [
                    'chat_id' => env('TELEGRAM_CHAT_ID'),
                    'text' => $message,
                    'parse_mode' => 'Markdown',
                ]
            );

            $success = $response->successful();
            if (!$success) {
                $errorMessage = $response->body();
            }
        } catch (\Exception $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }

        // 🔹 СОХРАНЕНИЕ ЛОГА
        TelegramReportLog::create([
            'timesheet_id' => $timesheetId,
            'date' => $rawDate,
            'status_id' => $statusId,
            'employees_count' => $items->count(),
            'message' => $message,
            'fields' => $reportFields,
            'sent_by' => auth()->user()->email ?? 'system',
            'success' => $success,
            'error_message' => $errorMessage,
        ]);

        if ($success) {
            return back()->with('success', "Отчет успешно отправлен в Telegram!");
        } else {
            return back()->with('error', "Ошибка отправки: " . ($errorMessage ?? 'Неизвестная ошибка'));
        }
    }
}
