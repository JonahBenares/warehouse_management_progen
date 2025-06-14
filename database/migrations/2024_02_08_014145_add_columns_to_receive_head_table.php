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
            $table->integer('prepared_by')->default(0)->after('saved');
            $table->string('prepared_by_name')->nullable()->after('saved');
            $table->string('prepared_position')->nullable()->after('prepared_by_name');
            $table->string('noted_position')->nullable()->after('noted_by');
            $table->string('acknowledged_position')->nullable()->after('acknowledged_by');
            $table->string('receive_position')->nullable()->after('received_by');
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
