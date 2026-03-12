<?php

namespace App\Services;

use App\Models\NotificationLog;
use App\Models\NotificationSetting;
use App\Models\Employee;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class TelegramVacationService
{
    protected string $botToken;
    protected string $apiUrl;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN'));
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}";
    }

    public function sendMessage(string $chatId, string $message): array
    {
        try {
            $response = Http::post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful()) {
                return ['success' => true, 'message' => 'Отправлено'];
            }

            return ['success' => false, 'error' => $response->json()['description'] ?? 'Ошибка Telegram'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendTestMessage(string $chatId): array
    {
        $message = "🧪 <b>Тестовое сообщение</b>\n\n";
        $message .= "Это тестовое уведомление из системы учёта отпусков.\n";
        $message .= "Время: " . now()->format('d.m.Y H:i');

        $result = $this->sendMessage($chatId, $message);

        NotificationLog::create([
            'type' => 'test',
            'channel' => 'telegram',
            'recipient' => $chatId,
            'message' => $message,
            'sent' => $result['success'],
            'error' => $result['error'] ?? null,
        ]);

        return $result;
    }

    public function sendPeriodSummary(string $chatId, int $months, string $format = 'short', ?Carbon $endDate = null): array
    {
        // Если end_date не передан — берём конец текущего года
        if (!$endDate) {
            $endDate = now()->endOfYear();
        }

        // Рассчитываем start_date
        $startDate = $endDate->copy()->subMonths($months - 1)->startOfMonth();

        $employees = Employee::whereNotNull('vacation_start')
            ->whereNotNull('vacation_end')
            ->whereBetween('vacation_start', [$startDate, $endDate])
            ->with('position')
            ->orderBy('vacation_start')
            ->get();

        // Формируем название периода
        if ($months === 12 && $startDate->year === $endDate->year) {
            $periodName = $startDate->year . ' год';
        } elseif ($months === 1) {
            $periodName = mb_ucfirst($startDate->translatedFormat('F Y'));
        } elseif ($months === 3) {
            $periodName = 'Квартал ' . $startDate->quarter . ' ' . $startDate->year;
        } elseif ($months === 6) {
            $periodName = 'Полугодие ' . $startDate->year;
        } else {
            $periodName = mb_ucfirst($startDate->translatedFormat('F')) . ' - ' . mb_ucfirst($endDate->translatedFormat('F Y'));
        }

        if ($format === 'short') {
            $message = $this->formatShortPeriodSummary($periodName, $employees, $months);
        } else {
            $message = $this->formatDetailedPeriodSummary($periodName, $employees, $months);
        }

        $result = $this->sendMessage($chatId, $message);

        NotificationLog::create([
            'type' => 'period_summary_' . $months . 'm',
            'channel' => 'telegram',
            'recipient' => $chatId,
            'message' => $message,
            'sent' => $result['success'],
            'error' => $result['error'] ?? null,
        ]);

        return [
            'success' => $result['success'],
            'sent' => $result['success'] ? 1 : 0,
            'error' => $result['error'] ?? null,
        ];
    }

    private function formatShortPeriodSummary(string $periodName, $employees, int $months): string
    {
        $message = "📊 <b>Сводка по отпускам</b>\n\n";
        $message .= "📅 <b>{$periodName}</b>\n\n";

        if ($employees->isEmpty()) {
            $message .= "ℹ️ В этом периоде отпусков нет";
        } else {
            $totalDays = 0;

            // Группируем по месяцам с годом
            $byMonth = $employees->groupBy(function($emp) {
                $month = Carbon::parse($emp->vacation_start)->translatedFormat('F Y');
                return mb_ucfirst($month);
            });

            $message .= "👥 <b>Сотрудники в отпуске:</b>\n\n";

            foreach ($byMonth as $monthName => $monthEmployees) {
                $message .= "📌 <b>{$monthName}:</b>\n";

                foreach ($monthEmployees as $emp) {
                    $startDate = Carbon::parse($emp->vacation_start);
                    $endDate = Carbon::parse($emp->vacation_end);
                    $days = $startDate->diffInDays($endDate) + 1;
                    $totalDays += $days;

                    $fullName = trim("{$emp->last_name} {$emp->first_name} {$emp->middle_name}");
                    $message .= "  • {$fullName}\n";
                }
                $message .= "\n";
            }

            $message .= "📈 <b>Итого за период:</b>\n";
            $message .= "• Сотрудников: {$employees->count()}\n";
            $message .= "• Всего дней: {$totalDays}";
        }

        return $message;
    }

    private function formatDetailedPeriodSummary(string $periodName, $employees, int $months): string
    {
        $message = "📊 <b>Сводка по отпускам</b>\n\n";
        $message .= "📅 <b>{$periodName}</b>\n\n";

        if ($employees->isEmpty()) {
            $message .= "ℹ️ В этом периоде отпусков нет";
        } else {
            $totalDays = 0;

            // Группируем по месяцам с годом
            $byMonth = $employees->groupBy(function($emp) {
                $month = Carbon::parse($emp->vacation_start)->translatedFormat('F Y');
                return mb_ucfirst($month);
            });

            foreach ($byMonth as $monthName => $monthEmployees) {
                $monthDays = 0;
                $message .= "\n📌 <b>{$monthName}</b> ({$monthEmployees->count()} сотрудников)\n\n";

                foreach ($monthEmployees as $index => $emp) {
                    $startDate = Carbon::parse($emp->vacation_start);
                    $endDate = Carbon::parse($emp->vacation_end);
                    $days = $startDate->diffInDays($endDate) + 1;
                    $monthDays += $days;
                    $totalDays += $days;

                    $fullName = trim("{$emp->last_name} {$emp->first_name} {$emp->middle_name}");
                    $position = $emp->position->name ?? 'Без должности';

                    $message .= ($index + 1) . ". <b>{$fullName}</b>\n";
                    $message .= "   📍 {$position}\n";
                    $message .= "   📅 {$startDate->format('d.m')} - {$endDate->format('d.m.Y')} ({$days} дн.)\n\n";
                }

                $message .= "   <i>Всего за месяц: {$monthDays} дней</i>\n\n";
            }

            $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "📈 <b>Итого за период:</b>\n";
            $message .= "• Сотрудников: {$employees->count()}\n";
            $message .= "• Всего дней: {$totalDays}";
        }

        return $message;
    }

    public function sendVacationReminder30Days($employee, string $chatId): array
    {
        return $this->sendVacationReminder($employee, $chatId, 30);
    }

    public function sendVacationReminder20Days($employee, string $chatId): array
    {
        return $this->sendVacationReminder($employee, $chatId, 20);
    }

    public function sendVacationReminder14Days($employee, string $chatId): array
    {
        return $this->sendVacationReminder($employee, $chatId, 14);
    }

    public function sendVacationReminder7Days($employee, string $chatId): array
    {
        return $this->sendVacationReminder($employee, $chatId, 7);
    }

    private function sendVacationReminder($employee, string $chatId, int $daysBefore): array
    {
        $startDate = Carbon::parse($employee->vacation_start);
        $fullName = trim("{$employee->last_name} {$employee->first_name} {$employee->middle_name}");

        $emoji = match($daysBefore) {
            30 => '📅',
            20 => '⏰',
            14 => '⏰',
            7 => '🔔',
            default => '⏰',
        };

        $title = match($daysBefore) {
            30 => 'Планирование отпуска',
            20 => 'Напоминание об отпуске',
            14 => 'Напоминание об отпуске',
            7 => 'Срочное напоминание!',
            default => 'Напоминание об отпуске',
        };

        $message = "{$emoji} <b>{$title}</b>\n\n";
        $message .= "👤 Сотрудник: {$fullName}\n";
        $message .= "📅 Начало: {$startDate->format('d.m.Y')}\n";
        $message .= "⏳ Осталось дней: <b>{$daysBefore}</b>";

        $result = $this->sendMessage($chatId, $message);

        NotificationLog::create([
            'type' => "vacation_{$daysBefore}_days",
            'channel' => 'telegram',
            'recipient' => $chatId,
            'employee_id' => $employee->id,
            'message' => $message,
            'sent' => $result['success'],
            'error' => $result['error'] ?? null,
        ]);

        return $result;
    }

    public function sendDailyReminders(): array
    {
        $today = now()->startOfDay();
        $chatId = env('TELEGRAM_CHAT_ID');

        if (!$chatId) {
            return ['success' => false, 'error' => 'TELEGRAM_CHAT_ID не настроен'];
        }

        $sent = 0;
        $errors = 0;

        $settings = NotificationSetting::all()->keyBy('notification_type');

        if (($settings['vacation_30_days'] ?? null)?->enabled) {
            $targetDate = $today->copy()->addDays(30);
            $employees = $this->getEmployeesByDate($targetDate);
            foreach ($employees as $emp) {
                $result = $this->sendVacationReminder30Days($emp, $chatId);
                if ($result['success']) $sent++; else $errors++;
            }
        }

        if (($settings['vacation_20_days'] ?? null)?->enabled) {
            $targetDate = $today->copy()->addDays(20);
            $employees = $this->getEmployeesByDate($targetDate);
            foreach ($employees as $emp) {
                $result = $this->sendVacationReminder20Days($emp, $chatId);
                if ($result['success']) $sent++; else $errors++;
            }
        }

        if (($settings['vacation_14_days'] ?? null)?->enabled) {
            $targetDate = $today->copy()->addDays(14);
            $employees = $this->getEmployeesByDate($targetDate);
            foreach ($employees as $emp) {
                $result = $this->sendVacationReminder14Days($emp, $chatId);
                if ($result['success']) $sent++; else $errors++;
            }
        }

        if (($settings['vacation_7_days'] ?? null)?->enabled) {
            $targetDate = $today->copy()->addDays(7);
            $employees = $this->getEmployeesByDate($targetDate);
            foreach ($employees as $emp) {
                $result = $this->sendVacationReminder7Days($emp, $chatId);
                if ($result['success']) $sent++; else $errors++;
            }
        }

        return [
            'success' => $sent > 0 || $errors === 0,
            'sent' => $sent,
            'errors' => $errors,
        ];
    }

    private function getEmployeesByDate(Carbon $date)
    {
        $dateStr = $date->format('Y-m-d');

        return Employee::whereNotNull('vacation_start')
            ->whereNotNull('vacation_end')
            ->whereDate('vacation_start', $dateStr)
            ->get();
    }
}

// Helper функция для заглавной буквы
if (!function_exists('mb_ucfirst')) {
    function mb_ucfirst($string) {
        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
    }
}
