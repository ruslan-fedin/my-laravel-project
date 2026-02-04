<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    // Справочник статусов (Ц, В, и т.д.)
    Schema::create('travel_statuses', function (Blueprint $table) {
        $table->id();
        $table->string('name');       // Полное: "Выезд в центр"
        $table->string('short_name'); // Краткое: "Ц"
        $table->string('color');      // Цвет для ячейки
        $table->timestamps();
    });

    // Записи в табеле выездов
    Schema::create('travel_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('travel_timesheet_id')->constrained()->onDelete('cascade');
        $table->foreignId('employee_id')->constrained()->onDelete('cascade');
        $table->date('date');
        $table->foreignId('travel_status_id')->nullable()->constrained()->onDelete('set null');
        $table->text('comment')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_system_tables');
    }
};
