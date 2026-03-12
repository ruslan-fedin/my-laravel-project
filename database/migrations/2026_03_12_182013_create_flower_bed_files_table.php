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
        Schema::create('flower_bed_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flower_bed_id')->constrained('flower_beds')->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('file_type');
            $table->decimal('file_size', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('flower_bed_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flower_bed_files');
    }
};
