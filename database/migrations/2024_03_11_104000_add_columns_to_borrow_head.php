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
        Schema::table('borrow_head', function (Blueprint $table) {
            $table->integer('saved')->default(0)->after('user_id');
            $table->string('borrowed_by_user_name')->nullable()->after('borrowed_by_user');
            $table->integer('prepared_by')->default(0);
            $table->string('prepared_by_name')->nullable();
            $table->string('prepared_by_position')->nullable();
            $table->integer('requested_by')->default(0);
            $table->string('requested_by_name')->nullable();
            $table->string('requested_by_position')->nullable();
            $table->integer('noted_by')->default(0);
            $table->string('noted_by_name')->nullable();
            $table->string('noted_by_position')->nullable();
            $table->integer('approved_by')->default(0);
            $table->string('approved_by_name')->nullable();
            $table->string('approved_by_position')->nullable();
            $table->integer('reviewed_by')->default(0);
            $table->string('reviewed_by_name')->nullable();
            $table->string('reviewed_by_position')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrow_head', function (Blueprint $table) {
            //
        });
    }
};
