<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planting_plan_flower_beds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planting_plan_id')->constrained('planting_plans')->onDelete('cascade');
            $table->foreignId('flower_bed_id')->constrained('flower_beds')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planting_plan_flower_beds');
    }
};
