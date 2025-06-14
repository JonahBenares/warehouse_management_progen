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
        Schema::create('override_logs', function (Blueprint $table) {
            $table->id();
            $table->text('receive')->nullable();
            $table->text('request')->nullable();
            $table->text('issuance')->nullable();
            $table->text('backorder')->nullable();
            $table->text('restock')->nullable();
            $table->integer('override_user_id')->default(0);
            $table->integer('user_id')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('override_logs');
    }
};
