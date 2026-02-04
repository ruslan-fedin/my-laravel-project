<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up() : void
{
    Schema::create('timesheet_items', function (Blueprint $table) {
        $table->id();

        // Оставьте только ОДНУ эту строку для связи с табелем:
        $table->foreignId('timesheet_id')
              ->constrained()
              ->onDelete('cascade'); // Это обеспечит удаление записей при удалении табеля

        $table->foreignId('employee_id')->constrained();
        $table->date('date');
        $table->foreignId('status_id')->nullable()->constrained();
        $table->text('comment')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timesheet_items');
    }
};
