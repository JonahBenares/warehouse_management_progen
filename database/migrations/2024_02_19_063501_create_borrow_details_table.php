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
        Schema::create('borrow_details', function (Blueprint $table) {
            $table->id();
            $table->integer('borrow_head_id')->default(0);
            $table->string('borrowed_by')->nullable();
            $table->string('borrowed_from')->nullable();
            $table->integer('item_id')->default(0);
            $table->text('item_description')->nullable();
            $table->integer('variant_id')->default(0);
            $table->double('avail_qty')->default(0);
            $table->double('quantity')->default(0);
            $table->integer('department_id')->unsigned();
            $table->text('department_name')->nullable();
            $table->integer('enduse_id')->unsigned();
            $table->text('enduse_name')->nullable();
            $table->integer('purpose_id')->unsigned();
            $table->text('purpose_name')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrow_details');
    }
};
