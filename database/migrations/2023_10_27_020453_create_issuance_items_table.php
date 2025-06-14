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
        Schema::create('issuance_items', function (Blueprint $table) {
            $table->id();
            $table->integer('issuance_head_id')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->text('item_description')->nullable();
            $table->integer('variant_id')->unsigned();
            $table->integer('request_items_id')->unsigned();
            $table->double('inventory_balance')->default(0);
            $table->double('quantity')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issuance_items');
    }
};
