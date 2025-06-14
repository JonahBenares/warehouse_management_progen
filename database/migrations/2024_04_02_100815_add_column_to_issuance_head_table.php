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
        Schema::table('issuance_head', function (Blueprint $table) {
            $table->string('contractor_name')->nullable()->after('contractor');
            $table->integer('noted_by_gp')->default(0)->after('inspected_by_pos');
            $table->string('noted_by_name_gp')->nullable()->after('noted_by_gp');
            $table->string('noted_by_pos_gp')->nullable()->after('noted_by_name_gp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issuance_head', function (Blueprint $table) {
            //
        });
    }
};
