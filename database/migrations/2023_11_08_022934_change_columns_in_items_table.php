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
        Schema::table('items', function (Blueprint $table) {
            $table->integer('location_id')->unsigned()->nullable()->change();
            $table->integer('warehouse_id')->unsigned()->nullable()->change();
            $table->integer('group_id')->unsigned()->nullable()->change();
            $table->integer('rack_id')->unsigned()->nullable()->change();
            $table->integer('moq')->default(0)->nullable()->change();
            $table->integer('running_balance')->default(0)->nullable()->change();
            $table->integer('composite_flag')->default(0)->nullable()->change();
            $table->integer('variant_flag')->default(0)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            //
        });
    }
};
