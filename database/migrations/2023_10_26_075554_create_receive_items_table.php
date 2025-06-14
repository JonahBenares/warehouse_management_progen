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
        Schema::create('receive_items', function (Blueprint $table) {
            $table->id();
            $table->integer('receive_head_id')->unsigned();
            $table->integer('receive_details_id')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->text('item_description')->nullable();
            $table->integer('supplier_id')->unsigned();
            $table->text('supplier_name')->nullable();
            $table->integer('brand_id')->unsigned();
            $table->string('catalog_no')->nullable();
            $table->string('serial_no')->nullable();
            $table->string('barcode')->nullable();
            $table->string('location')->nullable();
            $table->integer('item_status_id')->unsigned();
            $table->text('item_status')->nullable();
            $table->double('exp_quantity')->default(0);
            $table->double('rec_quantity')->default(0);
            $table->double('unit_cost')->default(0);
            $table->double('selling_price')->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receive_items');
    }
};
