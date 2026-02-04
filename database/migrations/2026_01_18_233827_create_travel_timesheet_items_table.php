<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('travel_timesheet_items', function (Blueprint $table) {
            $table->id();
            // Связь с табелем
            $table->unsignedBigInteger('travel_timesheet_id');
            // Связь с сотрудником
            $table->unsignedBigInteger('employee_id');
            // Дата дня
            $table->date('date');
            // Связь со статусом (Ц, В, У) - может быть пустой
            $table->unsignedBigInteger('status_id')->nullable();
            // Комментарий
            $table->text('comment')->nullable();
            $table->timestamps();

            // Индексы для быстрой работы базы
            $table->index('travel_timesheet_id');
            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_timesheet_items');
    }
};
