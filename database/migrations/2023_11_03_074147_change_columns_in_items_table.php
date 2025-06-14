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
            $table->integer('item_category_id')->unsigned()->default(0)->change();
            $table->integer('item_sub_category_id')->unsigned()->default(0)->change();
            $table->integer('location_id')->unsigned()->default(0)->change();
            $table->integer('warehouse_id')->unsigned()->default(0)->change();
            $table->integer('group_id')->unsigned()->default(0)->change();
            $table->integer('rack_id')->unsigned()->default(0)->change();
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
