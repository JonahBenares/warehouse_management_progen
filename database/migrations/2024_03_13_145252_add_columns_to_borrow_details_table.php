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
        Schema::table('borrow_details', function (Blueprint $table) {
            $table->double('replenished_qty')->default(0)->after('quantity');
            $table->double('balance')->default(0)->after('replenished_qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrow_details', function (Blueprint $table) {
            //
        });
    }
};
