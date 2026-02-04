<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Изменяем колонки, чтобы они могли принимать NULL
            $table->date('birth_date')->nullable()->change();
            $table->date('hire_date')->nullable()->change();
            $table->string('phone')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->date('birth_date')->nullable(false)->change();
            $table->date('hire_date')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
        });
    }
};
