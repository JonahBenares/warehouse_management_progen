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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('noted_flag')->default(0)->after('password');
            $table->integer('inspected_flag')->default(0)->after('password');
            $table->integer('delivered_flag')->default(0)->after('password');
            $table->integer('reviewed_flag')->default(0)->after('password');
            $table->integer('released_flag')->default(0)->after('password');
            $table->integer('requested_flag')->default(0)->after('password');
            $table->integer('approved_flag')->default(0)->after('password');
            $table->integer('acknowledge_flag')->default(0)->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
