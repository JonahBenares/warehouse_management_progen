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
        Schema::table('issuance_items', function (Blueprint $table) {
            $table->double('shipping_cost')->default(0)->after('issued_qty');
            $table->double('unit_cost')->default(0)->after('shipping_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issuance_items', function (Blueprint $table) {
            //
        });
    }
};
