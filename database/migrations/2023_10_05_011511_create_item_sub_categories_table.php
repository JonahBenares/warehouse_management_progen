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
        Schema::create('item_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('item_category_id')->unsigned();
            $table->text('subcat_code')->nullable();
            $table->text('subcat_prefix')->nullable();
            $table->text('subcat_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_sub_categories');
    }
};
