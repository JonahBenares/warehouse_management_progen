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
        Schema::create('gatepass_items', function (Blueprint $table) {
            $table->id();
            $table->integer('gatepass_head_id')->default(0);
            $table->text('item_description')->nullable();
            $table->double('quantity')->default(0);
            $table->string('uom')->nullable();
            $table->string('type')->nullable();
            $table->text('remarks')->nullable();
            $table->string('image')->nullable();
            $table->integer('display_flag')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gatepass_items');
    }
};
