<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flower_bed_files', function (Blueprint $table) {
            $table->string('upload_session')->nullable()->after('flower_bed_id');
            $table->index('upload_session');
        });
    }

    public function down(): void
    {
        Schema::table('flower_bed_files', function (Blueprint $table) {
            $table->dropColumn('upload_session');
        });
    }
};
