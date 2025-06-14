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
        Schema::create('piv_balance', function (Blueprint $table) {
            $table->id();
            $table->string('pr_no')->nullable();
            $table->integer('item_id')->default(0);
            $table->integer('variant_id')->default(0);
            $table->double('quantity')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('piv_balance');
    }
};
