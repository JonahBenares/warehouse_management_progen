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
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->string('reminder_date')->nullable();
            $table->text('title')->nullable();
            $table->text('notes')->nullable();
            $table->integer('person_to_notify_id')->default(0);
            $table->string('person_to_notify_name')->nullable();
            $table->integer('added_by_id')->default(0);
            $table->string('added_by_name')->nullable();
            $table->integer('done')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
