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
            $table->string('contractor')->nullable()->after('noted_pos');
            $table->integer('requested_by')->default(0)->after('contractor');
            $table->string('requested_by_name')->nullable()->after('requested_by');
            $table->string('requested_by_pos')->nullable()->after('requested_by_name');
            $table->integer('approved_by')->default(0)->after('requested_by_pos');
            $table->string('approved_by_name')->nullable()->after('approved_by');
            $table->string('approved_by_pos')->nullable()->after('approved_by_name');
            $table->integer('recommended_by')->default(0)->after('approved_by_pos');
            $table->string('recommended_by_name')->nullable()->after('recommended_by');
            $table->string('recommended_by_pos')->nullable()->after('recommended_by_name');
            $table->integer('inspected_by')->default(0)->after('recommended_by_pos');
            $table->string('inspected_by_name')->nullable()->after('inspected_by');
            $table->string('inspected_by_pos')->nullable()->after('inspected_by_name');
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
