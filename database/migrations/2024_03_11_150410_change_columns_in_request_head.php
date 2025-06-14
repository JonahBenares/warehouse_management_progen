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
        Schema::table('request_head', function (Blueprint $table) {
            $table->string('request_date')->nullable()->change();
            $table->integer('user_id')->change()->default(0);
            $table->integer('department_id')->change()->default(0);
            $table->integer('enduse_id')->change()->default(0);
            $table->integer('purpose_id')->change()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_head', function (Blueprint $table) {
            //
        });
    }
};
