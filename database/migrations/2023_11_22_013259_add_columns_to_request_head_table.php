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
            $table->string('mreqf_no')->after('request_time')->nullable();
            $table->integer('saved')->after('user_id')->default(0);
            $table->integer('draft')->after('saved')->default(0);
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
