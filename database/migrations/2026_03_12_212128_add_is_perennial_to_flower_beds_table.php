<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flower_beds', function (Blueprint $table) {
            $table->boolean('is_perennial')->default(false)->after('is_active');
            $table->index('is_perennial');
        });
    }

    public function down(): void
    {
        Schema::table('flower_beds', function (Blueprint $table) {
            $table->dropColumn('is_perennial');
        });
    }
};
