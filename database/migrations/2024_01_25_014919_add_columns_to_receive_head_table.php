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
        Schema::table('receive_head', function (Blueprint $table) {
            $table->text('delivered_by')->nullable()->after('saved');
            $table->integer('received_by')->default(0)->after('saved');
            $table->string('received_by_name')->nullable()->after('saved');
            $table->integer('acknowledged_by')->default(0)->after('saved');
            $table->string('acknowledged_by_name')->nullable()->after('saved');
            $table->integer('noted_by')->default(0)->after('saved');
            $table->string('noted_name')->nullable()->after('saved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receive_head', function (Blueprint $table) {
            //
        });
    }
};
