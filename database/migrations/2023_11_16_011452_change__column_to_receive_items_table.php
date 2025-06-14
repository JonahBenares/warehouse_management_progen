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
        Schema::table('receive_items', function (Blueprint $table) {
            $table->integer('brand_id')->nullable()->change();
            $table->renameColumn('brand_id', 'brand');
            $table->integer('receive_head_id')->default(0)->change();
            $table->integer('receive_details_id')->default(0)->change();
            $table->integer('item_id')->default(0)->change();
            $table->integer('supplier_id')->default(0)->change();
            $table->integer('item_status_id')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receive_items', function (Blueprint $table) {
            //
        });
    }
};
