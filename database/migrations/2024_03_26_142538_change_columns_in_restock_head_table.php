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
        Schema::table('restock_head', function (Blueprint $table) {
            $table->integer('returned_by')->change()->default(0);
            $table->integer('acknowledged_by')->change()->default(0);
            $table->integer('inspected_by')->change()->default(0);
            $table->integer('noted_by')->change()->default(0);
            $table->integer('user_id')->change()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restock_head', function (Blueprint $table) {
            //
        });
    }
};
