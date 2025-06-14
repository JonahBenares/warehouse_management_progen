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
        Schema::create('gatepass_returned_history', function (Blueprint $table) {
            $table->id();
            $table->integer('gatepass_head_id')->default(0);
            $table->integer('gatepass_item_id')->default(0);
            $table->date('date_returned')->nullable();
            $table->double('returned_qty')->default(0);
            $table->text('remarks')->nullable();
            $table->integer('user_id')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gatepass_returned_history');
    }
};
