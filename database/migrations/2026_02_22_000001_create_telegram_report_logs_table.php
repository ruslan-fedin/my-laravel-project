<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_report_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('timesheet_id');
            $table->string('date');                    // Дата отчета
            $table->unsignedBigInteger('status_id');   // Статус
            $table->integer('employees_count');        // Количество сотрудников
            $table->text('message');                   // Полное сообщение
            $table->json('fields')->nullable();        // Поля (JSON)
            $table->string('sent_by')->nullable();     // Кто отправил (email или ID)
            $table->boolean('success')->default(true); // Успешно/ошибка
            $table->text('error_message')->nullable(); // Текст ошибки
            $table->timestamps();

            $table->foreign('timesheet_id')->references('id')->on('travel_timesheets')->onDelete('cascade');
            $table->index('timesheet_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_report_logs');
    }
};
