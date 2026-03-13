<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_record_id')->constrained('work_records')->onDelete('cascade');
            $table->enum('photo_type', ['before', 'during', 'after']);
            $table->string('file_name');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type');
            $table->decimal('file_size', 10, 2);
            $table->text('caption')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['work_record_id', 'photo_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_photos');
    }
};
