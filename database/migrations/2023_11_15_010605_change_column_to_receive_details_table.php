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
        Schema::table('receive_details', function (Blueprint $table) {
            $table->string('detail_no')->nullable()->change();
            $table->string('department_id')->nullable()->change();
            $table->string('enduse_id')->nullable()->change();
            $table->string('purpose_id')->nullable()->change();
            $table->string('inspected_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receive_details', function (Blueprint $table) {
            //
        });
    }
};
