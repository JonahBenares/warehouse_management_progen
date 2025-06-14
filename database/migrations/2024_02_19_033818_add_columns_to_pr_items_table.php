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
        Schema::table('pr_items', function (Blueprint $table) {
            $table->double('borrow_deduct')->default(0)->after('damage_qty');
            $table->double('replenish_add')->default(0)->after('borrow_deduct');
            $table->double('borrow_add')->default(0)->after('replenish_add');
            $table->double('replenish_deduct')->default(0)->after('borrow_add');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pr_items', function (Blueprint $table) {
            //
        });
    }
};
