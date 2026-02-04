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
    Schema::create('travel_records', function (Blueprint $table) {
        $table->id();
        $table->foreignId('travel_timesheet_id')->constrained()->onDelete('cascade');
        $table->foreignId('employee_id')->constrained()->onDelete('cascade');
        $table->date('date');
        // Статусы: 'center' (Центр), 'field' (Выезд), 'off' (Выходной/Прочее)
        $table->string('location_status')->default('center');
        $table->text('comment')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_records');
    }
};
