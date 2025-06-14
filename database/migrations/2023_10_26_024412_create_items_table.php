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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->integer('item_category_id')->unsigned();
            $table->integer('item_sub_category_id')->unsigned();
            $table->text('item_description')->nullable();
            $table->integer('location_id')->unsigned();
            $table->text('location_description')->nullable();
            $table->integer('warehouse_id')->unsigned();
            $table->text('warehouse_description')->nullable();
            $table->integer('group_id')->unsigned();
            $table->text('group_description')->nullable();
            $table->integer('rack_id')->unsigned();
            $table->text('rack_description')->nullable();
            $table->string('uom')->nullable();
            $table->integer('moq')->default(0);
            $table->string('image1')->nullable();
            $table->string('image2')->nullable();
            $table->string('image3')->nullable();
            $table->integer('running_balance')->default(0);
            $table->integer('composite_flag')->default(0);
            $table->integer('variant_flag')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
