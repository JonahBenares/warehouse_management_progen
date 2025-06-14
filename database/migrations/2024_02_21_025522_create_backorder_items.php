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
        Schema::create('backorder_items', function (Blueprint $table) {
            $table->id();
            $table->integer('backorder_head_id')->default(0);
            $table->integer('backorder_details_id')->default(0);
            $table->integer('variant_id')->default(0);
            $table->integer('item_id')->default(0);
            $table->text('item_description')->nullable();
            $table->integer('supplier_id')->default(0);
            $table->text('supplier_name')->nullable();
            $table->integer('brand')->nullable();
            $table->string('catalog_no')->nullable();
            $table->string('serial_no')->nullable();
            $table->string('barcode')->nullable();
            $table->string('location')->nullable();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->string('uom')->nullable();
            $table->string('expiry_date')->nullable();
            $table->integer('item_status_id')->default(0);
            $table->text('item_status')->nullable();
            $table->double('bo_quantity')->default(0);
            $table->double('unit_cost')->default(0);
            $table->double('selling_price')->default(0);
            $table->string('pr_replenish')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backorder_items');
    }
};
