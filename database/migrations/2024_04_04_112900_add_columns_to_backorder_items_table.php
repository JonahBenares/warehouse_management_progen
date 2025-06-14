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
        Schema::table('backorder_items', function (Blueprint $table) {
            $table->string('pn_no')->nullable()->after('item_description');
            $table->integer('receive_items_id')->default(0)->after('backorder_details_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('backorder_items', function (Blueprint $table) {
            //
        });
    }
};
