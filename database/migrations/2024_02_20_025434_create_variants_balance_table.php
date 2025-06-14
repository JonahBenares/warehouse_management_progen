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
        Schema::create('variants_balance', function (Blueprint $table) {
            $table->id();
            $table->double('item_id')->default(0)->unsigned();
            $table->double('variant_id')->default(0)->unsigned();
            $table->double('whstocks_qty')->default(0)->comment('add');
            $table->double('composite_qty')->default(0)->comment('deduct');
            $table->double('receive_qty')->default(0)->comment('add');
            $table->double('issuance_qty')->default(0)->comment('deduct');
            $table->double('restock_qty')->default(0)->comment('add');
            $table->double('transfer_qty')->default(0)->comment('deduct');
            $table->double('damage_qty')->default(0)->comment('deduct');
            $table->double('borrow_deduct')->default(0)->comment('deduct');
            $table->double('replenish_add')->default(0)->comment('add');
            $table->double('borrow_add')->default(0)->comment('add');
            $table->double('replenish_deduct')->default(0)->comment('deduct');
            $table->double('backorder_qty')->default(0)->comment('add');
            $table->double('balance')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variants_balance');
    }
};
