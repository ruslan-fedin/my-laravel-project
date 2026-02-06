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
    // Проверяем, есть ли уже такая колонка в таблице
    if (!Schema::hasColumn('statuses', 'color')) {
        Schema::table('statuses', function (Blueprint $table) {
            $table->string('color')->default('#3b82f6')->after('name');
        });
    }
}

public function down(): void
{
    if (Schema::hasColumn('statuses', 'color')) {
        Schema::table('statuses', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
}
};
