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
        Schema::create('gatepass_head', function (Blueprint $table) {
            $table->id();
            $table->string('gatepass_no')->nullable();
            $table->string('to_company')->nullable();
            $table->string('destination')->nullable();
            $table->string('vehicle_no')->nullable();
            $table->date('date_issued')->nullable();
            $table->string('status')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('user_id')->default(0);
            $table->integer('saved')->default(0);
            $table->integer('prepared_by')->default(0);
            $table->string('prepared_by_name')->nullable();
            $table->string('prepared_by_position')->nullable();
            $table->integer('noted_by')->default(0);
            $table->string('noted_by_name')->nullable();
            $table->string('noted_by_position')->nullable();
            $table->integer('approved_by')->default(0);
            $table->string('approved_by_name')->nullable();
            $table->string('approved_by_position')->nullable();
            $table->integer('received_by')->default(0);
            $table->string('received_by_name')->nullable();
            $table->string('received_by_position')->nullable();
            $table->string('cpgc_guard_name')->nullable();
            $table->string('npc_guard_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gatepass_head');
    }
};
