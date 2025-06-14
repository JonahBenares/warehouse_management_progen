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
            $table->string('borrow_date')->nullable()->after('mbr_no');
            $table->string('borrow_time')->nullable()->after('borrow_date');
            $table->integer('borrowed_by_user')->unsigned()->after('borrow_time');
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
