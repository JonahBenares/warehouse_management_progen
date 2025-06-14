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
        Schema::create('backorder_details', function (Blueprint $table) {
            $table->id();
            $table->integer('backorder_head_id')->default(0);
            $table->integer('detail_no')->nullable();
            $table->string('pr_no')->nullable();
            $table->string('department_id')->nullable()->default(0);
            $table->text('department_name')->nullable();
            $table->string('enduse_id')->nullable()->default(0);
            $table->text('enduse_name')->nullable();
            $table->string('purpose_id')->nullable()->default(0);
            $table->text('purpose_name')->nullable();
            $table->text('inspected_name')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backorder_details');
    }
};
