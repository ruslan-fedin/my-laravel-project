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
    Schema::create('brigade_locations', function (Blueprint $table) {
        $table->id();
        // Это связь: мы записываем ID бригадира, чтобы знать, чьё это место работы
        $table->foreignId('brigadier_id')->constrained('employees')->onDelete('cascade');
        // А это само поле, куда будем писать текст (например, "Цех №2")
        $table->string('location_name')->nullable();
        $table->timestamps(); // Время создания и обновления
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brigade_locations');
    }
};
