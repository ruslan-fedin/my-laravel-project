<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Поле для статуса (active/vacation)
            $table->string('status')->default('active')->after('middle_name');

            // Поле для ID заместителя (ссылается на ту же таблицу employees)
            $table->unsignedBigInteger('substitute_id')->nullable()->after('status');

            // Индекс для ускорения поиска при замещении
            $table->index('substitute_id');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['status', 'substitute_id']);
        });
    }
};
