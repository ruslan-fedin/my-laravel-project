<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planting_plan_bed_colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planting_plan_id')->constrained('planting_plans')->onDelete('cascade');
            $table->foreignId('flower_bed_id')->constrained('flower_beds')->onDelete('cascade');
            $table->string('color_type');
            $table->integer('quantity')->default(0);
            $table->timestamps();

            $table->unique(['planting_plan_id', 'flower_bed_id', 'color_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planting_plan_bed_colors');
    }
};
