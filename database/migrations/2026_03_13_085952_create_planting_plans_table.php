<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planting_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // Наименование (район/улица)
            $table->decimal('area', 10, 2);      // Общая площадь (м²)
            $table->integer('planting_rate')->default(60);  // Норма посадки (шт/м²)
            $table->integer('total_quantity');   // Общее количество (авто)
            $table->integer('sort_order')->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planting_plans');
    }
};
