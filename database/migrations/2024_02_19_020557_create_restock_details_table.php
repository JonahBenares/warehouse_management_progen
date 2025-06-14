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
        Schema::create('restock_details', function (Blueprint $table) {
            $table->id();
            $table->integer('restock_head_id')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->text('item_description')->nullable();
            $table->integer('variant_id')->unsigned();
            $table->integer('receive_items_id')->unsigned();
            $table->double('quantity')->default(0);
            $table->text('reason')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restock_details');
    }
};
