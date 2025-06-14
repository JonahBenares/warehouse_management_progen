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
        Schema::table('issuance_head', function (Blueprint $table) {
            $table->integer('prepared_by')->default(0)->after('saved');
            $table->string('prepared_by_name')->nullable()->after('prepared_by');
            $table->string('prepared_by_pos')->nullable()->after('prepared_by_name');
            $table->integer('received_by')->default(0)->after('prepared_by_pos');
            $table->string('received_by_name')->nullable()->after('received_by');
            $table->string('received_by_pos')->nullable()->after('received_by_name');
            $table->integer('released_by')->default(0)->after('received_by_pos');
            $table->string('released_by_name')->nullable()->after('released_by');
            $table->string('released_by_pos')->nullable()->after('released_by_name');
            $table->integer('noted_by')->default(0)->after('released_by_pos');
            $table->string('noted_name')->nullable()->after('noted_by');
            $table->string('noted_pos')->nullable()->after('noted_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issuance_head', function (Blueprint $table) {
            //
        });
    }
};
