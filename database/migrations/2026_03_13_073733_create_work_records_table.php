<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flower_bed_id')->constrained('flower_beds')->onDelete('cascade');
            $table->foreignId('work_type_id')->constrained('work_types');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('completed'); // completed, in_progress, planned
            $table->date('work_date')->nullable();  // Дата работы
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['flower_bed_id', 'work_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_records');
    }
};
