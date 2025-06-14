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
        Schema::create('request_head', function (Blueprint $table) {
            $table->id();
            $table->date('request_date')->nullable();
            $table->string('request_time')->nullable();
            $table->string('request_type')->nullable();
            $table->string('pr_no')->nullable();
            $table->integer('department_id')->unsigned();
            $table->text('department_name')->nullable();
            $table->integer('purpose_id')->unsigned();
            $table->text('purpose_name')->nullable();
            $table->integer('enduse_id')->unsigned();
            $table->text('enduse_name')->nullable();
            $table->string('borrow_from_pr')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('user_id')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_head');
    }
};
