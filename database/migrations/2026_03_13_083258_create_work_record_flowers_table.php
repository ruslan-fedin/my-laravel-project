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
        Schema::create('work_record_flowers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_record_id')->constrained('work_records')->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->string('flower_color')->nullable();
            $table->string('flower_variety')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['work_record_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_record_flowers');
    }
};
