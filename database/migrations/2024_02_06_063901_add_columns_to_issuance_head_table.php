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
            $table->string('pr_no')->nullable()->after('mif_no');
            $table->integer('department_id')->unsigned()->after('issuance_time');
            $table->text('department_name')->nullable()->after('department_id');
            $table->integer('purpose_id')->unsigned()->after('department_name');
            $table->text('purpose_name')->nullable()->after('purpose_id');
            $table->integer('enduse_id')->unsigned()->after('purpose_name');
            $table->text('enduse_name')->nullable()->after('enduse_id');
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
