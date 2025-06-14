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
            $table->integer('department_id')->default(0)->after('mrs_no');
            $table->string('department')->nullable()->after('department_id');
            $table->integer('enduse_id')->default(0)->after('department');
            $table->string('enduse')->nullable()->after('enduse_id');
            $table->integer('purpose_id')->default(0)->after('enduse');
            $table->string('purpose')->nullable()->after('purpose_id');
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
