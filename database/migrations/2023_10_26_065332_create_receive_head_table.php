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
        Schema::create('receive_head', function (Blueprint $table) {
            $table->id();
            $table->date('receive_date')->nullable();
            $table->string('mrec_no')->nullable();
            $table->string('dr_no')->nullable();
            $table->string('po_no')->nullable();
            $table->string('si_or')->nullable();
            $table->integer('pcf')->default(0);
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
        Schema::dropIfExists('receive_head');
    }
};
