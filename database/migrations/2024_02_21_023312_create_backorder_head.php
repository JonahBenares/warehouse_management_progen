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
        Schema::create('backorder_head', function (Blueprint $table) {
            $table->id();
            $table->string('mrecf_no')->nullable();
            $table->string('dr_no')->nullable();
            $table->string('po_no')->nullable();
            $table->string('si_or')->nullable();
            $table->integer('pcf')->default(0);
            $table->integer('user_id')->unsigned();
            $table->integer('saved')->default(0);
            $table->integer('closed')->default(0);
            $table->integer('draft')->default(0);
            $table->integer('prepared_by')->default(0);
            $table->string('prepared_by_name')->nullable();
            $table->string('prepared_position')->nullable();
            $table->text('delivered_by')->nullable();
            $table->integer('received_by')->default(0);
            $table->string('received_by_name')->nullable();
            $table->string('receive_position')->nullable();
            $table->integer('acknowledged_by')->default(0);
            $table->string('acknowledged_by_name')->nullable();
            $table->string('acknowledged_position')->nullable();
            $table->integer('noted_by')->default(0);
            $table->string('noted_name')->nullable();
            $table->string('noted_position')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backorder_head');
    }
};
