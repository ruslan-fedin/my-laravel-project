<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TdmReportController extends Controller
{
    /**
     * Отправка отчета в мессенджер TDM
     */
    public function sendCenterReport(Request $request)
    {
        // 1. Сбор данных из формы
        $rawDate = $request->input('date');
        $statusId = $request->input('status_id');
        $timesheetId = $request->input('timesheet_id');
        $publicLink = $request->input('public_link');

        $workType = $request->input('work_type');
        $inventory = $request->input('inventory');
        $notes = $request->input('notes');
        $transport = $request->input('transport');
        $departure = $request->input('departure');

        // 2. Валидация статуса
        $status = Status::find($statusId);
        if (!$status) {
            return back()->with('error', "Статус не выбран.");
        }

        $searchDate = Carbon::parse($rawDate)->format('Y-m-d');
        $displayDate = Carbon::parse($rawDate)->format('d.m.Y');

        // 3. Получение списка сотрудников (ФИО полностью)
        $items = DB::table('travel_timesheet_items')
            ->join('employees', 'travel_timesheet_items.employee_id', '=', 'employees.id')
            ->select('employees.last_name', 'employees.first_name', 'employees.middle_name')
            ->where('travel_timesheet_items.travel_timesheet_id', $timesheetId)
            ->where('travel_timesheet_items.date', $searchDate)
            ->where('travel_timesheet_items.status_id', $statusId)
            ->orderBy('employees.last_name')
            ->get();

        if ($items->isEmpty()) {
            return back()->with('error', "На дату {$displayDate} со статусом «{$status->name}» сотрудников не найдено.");
        }

        // 4. Формирование текста сообщения (TDM поддерживает Markdown)
        $message = "📢 *ОТЧЕТ: {$status->name}*\n";
        $message .= "📅 Дата: *{$displayDate}*\n";
        $message .= "──────────────────\n";

        foreach ($items as $index => $emp) {
            // Пишем ФИО полностью согласно вашему требованию
            $fio = trim("{$emp->last_name} {$emp->first_name} {$emp->middle_name}");
            $message .= ($index + 1) . ". " . $fio . "\n";
        }

        $message .= "──────────────────\n";
        $message .= "ИТОГО: *" . $items->count() . " чел.*\n\n";

        if ($workType)  { $message .= "📝 *Вид работ:* {$workType}\n"; }
        if ($inventory) { $message .= "📦 *Инвентарь:* {$inventory}\n"; }
        if ($departure) { $message .= "🛫 *Выезд:* {$departure}\n"; }
        if ($transport) { $message .= "🚘 *Транспорт:* {$transport}\n"; }
        if ($notes)     { $message .= "ℹ️ *Примечание:* {$notes}\n"; }

        if ($publicLink) {
            $message .= "\n🔗 *Ссылка на табель:* \n" . $publicLink;
        }

        // 5. Настройки API TDM
        $baseUrl = rtrim(env('TDM_API_URL'), '/');
        $token   = env('TDM_BOT_TOKEN');
        $wId     = env('TDM_WORKSPACE_ID');
        $gId     = env('TDM_GROUP_ID');

        // clientRandomId — обязательное поле для TDM (uint64)
        // Используем комбинацию времени и рандома
        $clientRandomId = (int)(microtime(true) * 1000);



        try {
            // Endpoint согласно документации: /botapi/v1/messages/sendTextMessage/{workspaceId}/{groupId}
            $url = "{$baseUrl}/botapi/v1/messages/sendTextMessage/{$wId}/{$gId}";

            $response = Http::withHeaders([
                'Authorization' => $token,
                'Content-Type'  => 'application/json',
            ])
            ->withoutVerifying() // Часто для корпоративных TDM нужны самоподписанные сертификаты
            ->timeout(15)
            ->post($url, [
                'clientRandomId' => $clientRandomId,
                'message'        => $message,
            ]);

            if ($response->successful()) {
                return back()->with('success', "Отчет в TDM отправлен успешно!");
            }

            Log::error("TDM API Error: " . $response->body());
            return back()->with('error', "Ошибка Tdm: Код " . $response->status());

        } catch (\Exception $e) {
            Log::error("TDM Connection Error: " . $e->getMessage());
            return back()->with('error', "Ошибка связи: " . $e->getMessage());
        }
    }
}
