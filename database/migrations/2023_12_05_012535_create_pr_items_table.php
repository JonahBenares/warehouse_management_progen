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
        Schema::create('pr_items', function (Blueprint $table) {
            $table->id();
            $table->string('pr_no')->nullable();
            $table->double('item_id')->default(0);
            $table->double('begbal')->default(0);
            $table->double('receive_qty')->default(0);
            $table->double('issuance_qty')->default(0);
            $table->double('restock_qty')->default(0);
            $table->double('damage_qty')->default(0);
            $table->double('balance')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pr_items');
    }
};
