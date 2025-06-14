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
            $table->string('reviewed_by_position')->nullable()->after('draft');
            $table->string('reviewed_by_name')->nullable()->after('draft');
            $table->integer('reviewed_by')->default(0)->after('draft');
            $table->string('approved_by_position')->nullable()->after('draft');
            $table->string('approved_by_name')->nullable()->after('draft');
            $table->integer('approved_by')->default(0)->after('draft');
            $table->string('noted_by_position')->nullable()->after('draft');
            $table->string('noted_by_name')->nullable()->after('draft');
            $table->integer('noted_by')->default(0)->after('draft');
            $table->string('requested_by_position')->nullable()->after('draft');
            $table->string('requested_by_name')->nullable()->after('draft');
            $table->integer('requested_by')->default(0)->after('draft');
            $table->string('prepared_by_position')->nullable()->after('draft');
            $table->string('prepared_by_name')->nullable()->after('draft');
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
