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
        Schema::table('borrow_details', function (Blueprint $table) {
            $table->integer('department_id')->change()->default(0);
            $table->integer('purpose_id')->change()->default(0);
            $table->integer('enduse_id')->change()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrow_details', function (Blueprint $table) {
            //
        });
    }
};
