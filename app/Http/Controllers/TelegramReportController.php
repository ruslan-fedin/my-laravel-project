<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TelegramReportController extends Controller
{
    public function sendCenterReport(Request $request)
    {
        // 1. Сбор данных из запроса
        $rawDate = $request->input('date');
        $statusId = $request->input('status_id');
        $timesheetId = $request->input('timesheet_id');

        // Данные приходят из JS уже с иконками или без (контроллер обработает оба варианта)
        $workType = $request->input('work_type');
        $inventory = $request->input('inventory');
        $notes = $request->input('notes');
        $transport = $request->input('transport');
        $departure = $request->input('departure');

        // 2. Поиск статуса
        $status = Status::find($statusId);
        if (!$status) {
            return back()->with('error', "Статус не выбран.");
        }

        $searchDate = Carbon::parse($rawDate)->format('Y-m-d');
        $displayDate = Carbon::parse($rawDate)->format('d.m.Y');

        // 3. Получение списка людей через Query Builder (работает без правок в моделях)
        $items = DB::table('travel_timesheet_items')
            ->join('employees', 'travel_timesheet_items.employee_id', '=', 'employees.id')
            ->select('employees.last_name', 'employees.first_name', 'employees.middle_name')
            ->where('travel_timesheet_items.travel_timesheet_id', $timesheetId)
            ->where('travel_timesheet_items.date', $searchDate)
            ->where('travel_timesheet_items.status_id', $statusId)
            ->orderBy('employees.last_name') // Сортировка по алфавиту для красоты
            ->get();

        if ($items->isEmpty()) {
            return back()->with('error', "На дату {$displayDate} со статусом «{$status->name}» никого нет.");
        }

        // 4. Формирование текста (Ваш стиль с иконками)
        // Используем Markdown для жирного шрифта
        $message = "📢 *ОТЧЕТ: {$status->name}*\n";
        $message .= "📅 Дата: *{$displayDate}*\n";
        $message .= "──────────────────\n";

        foreach ($items as $index => $emp) {
            // Пишем ФИО полностью
            $fio = trim("{$emp->last_name} {$emp->first_name} {$emp->middle_name}");
            $message .= ($index + 1) . ". " . $fio . "\n";
        }

        $message .= "──────────────────\n";
        $message .= "ИТОГО: *" . $items->count() . " чел.*\n\n";

        // Добавляем строки. Если в JS иконка уже добавлена, она просто выведется.
        // Если нет — можно добавить префиксы здесь.
        if ($workType)  { $message .= "{$workType}\n"; }
        if ($inventory) { $message .= "{$inventory}\n"; }
        if ($departure) { $message .= "{$departure}\n"; }
        if ($transport) { $message .= "{$transport}\n"; }
        if ($notes)     { $message .= "{$notes}"; }

        // 5. Отправка
        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        try {
            // Используем Markdown для корректного отображения жирного текста
            $response = Http::withoutVerifying()->timeout(15)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);

            if ($response->successful()) {
                return back()->with('success', "Отчет по статусу «{$status->name}» отправлен!");
            }

            return back()->with('error', "Ошибка Telegram: " . $response->body());
        } catch (\Exception $e) {
            return back()->with('error', "Ошибка связи: " . $e->getMessage());
        }
    }
}
