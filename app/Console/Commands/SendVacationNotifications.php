<?php

namespace App\Console\Commands;

use App\Services\TelegramVacationService;
use Illuminate\Console\Command;

class SendVacationNotifications extends Command
{
    protected $signature = 'vacations:send-notifications';
    protected $description = 'Отправить ежедневные напоминания об отпусках';

    protected TelegramVacationService $telegramService;

    public function __construct(TelegramVacationService $telegramService)
    {
        parent::__construct();
        $this->telegramService = $telegramService;
    }

    public function handle()
    {
        $this->info('🚀 Запуск отправки уведомлений об отпусках...');

        $result = $this->telegramService->sendDailyReminders();

        if ($result['success']) {
            $this->info("✅ Отправлено: {$result['sent']} уведомлений");
            if ($result['errors'] > 0) {
                $this->warn("⚠️ Ошибок: {$result['errors']}");
            }
            return 0;
        }

        $this->error("❌ Ошибка: {$result['error']}");
        return 1;
    }
}
