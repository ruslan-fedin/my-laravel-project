<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->date('vacation_start')->nullable()->change();
            $table->date('vacation_end')->nullable()->change();
            $table->string('vacation_type')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->date('vacation_start')->nullable(false)->change();
            $table->date('vacation_end')->nullable(false)->change();
            $table->string('vacation_type')->nullable(false)->change();
        });
    }
};
