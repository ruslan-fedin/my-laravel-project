<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::table('employees', function (Blueprint $table) {
        // parent_id указывает на ID другого сотрудника из этой же таблицы
        $table->unsignedBigInteger('parent_id')->nullable()->after('position_id');
        $table->foreign('parent_id')->references('id')->on('employees')->onDelete('set null');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            //
        });
    }
};
