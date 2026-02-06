<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('timesheets', function (Blueprint $table) {
        $table->id();
        // Создает колонку и привязывает её к таблице employees
        $table->foreignId('employee_id')->constrained()->onDelete('cascade');
        $table->date('date');
        $table->integer('hours')->default(0);
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timesheets');
    }
};
