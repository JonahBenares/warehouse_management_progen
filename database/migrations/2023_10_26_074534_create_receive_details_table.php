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
        Schema::create('receive_details', function (Blueprint $table) {
            $table->id();
            $table->integer('receive_head_id')->unsigned();
            $table->string('pr_no')->nullable();
            $table->integer('department_id')->unsigned();
            $table->text('department_name')->nullable();
            $table->integer('enduse_id')->unsigned();
            $table->text('enduse_name')->nullable();
            $table->integer('purpose_id')->unsigned();
            $table->text('purpose_name')->nullable();
            $table->integer('inspected_id')->unsigned();
            $table->text('inspected_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receive_details');
    }
};
