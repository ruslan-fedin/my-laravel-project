<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            // ФИО пишем полностью
            $table->string('last_name');       // Фамилия
            $table->string('first_name');      // Имя
            $table->string('middle_name')->nullable(); // Отчество

            $table->date('birthday');          // Дата рождения
            $table->date('hired_at');          // Дата приема
            $table->string('phone');           // Телефон

            // Связи
            $table->foreignId('position_id')->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(true); // Статус: активен или нет

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('employees');
    }
};
