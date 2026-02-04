<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');       // Например: "Явка"
            $table->string('short_name'); // Например: "Я"
            $table->string('color');      // HEX код цвета, например: #ff0000
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('statuses');
    }
};
