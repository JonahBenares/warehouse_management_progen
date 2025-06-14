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
        Schema::create('issuance_head', function (Blueprint $table) {
            $table->id();
            $table->integer('request_head_id')->unsigned();
            $table->string('mreqf_no')->nullable();
            $table->string('mrif_no')->nullable();
            $table->date('issuance_date')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('user_id')->unsigned();
            $table->integer('saved')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issuance_head');
    }
};
