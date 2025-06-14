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
            $table->string('issuance_time')->nullable()->after('issuance_date');
            $table->renameColumn('mrif_no', 'mif_no');
            $table->string('issuance_date')->change();
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
