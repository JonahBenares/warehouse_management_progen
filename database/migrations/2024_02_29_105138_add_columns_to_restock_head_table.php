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
            $table->string('returned_by_name')->nullable()->after('returned_by');
            $table->string('returned_by_position')->nullable()->after('returned_by_name');
            $table->string('acknowledged_by_name')->nullable()->after('acknowledged_by');
            $table->string('acknowledged_by_position')->nullable()->after('acknowledged_by_name');
            $table->string('inspected_by_name')->nullable()->after('inspected_by');
            $table->string('inspected_by_position')->nullable()->after('inspected_by_name');
            $table->string('noted_by_name')->nullable()->after('noted_by');
            $table->string('noted_by_position')->nullable()->after('noted_by_name');
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
