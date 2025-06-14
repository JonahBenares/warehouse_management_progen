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
        Schema::table('receive_items', function (Blueprint $table) {
            $table->integer('eval_flag')->default(0)->comment('0=Pending,1=Accepted,2=Rejected')->after('remarks');
            $table->string('eval_date')->nullable()->after('eval_flag');
            $table->integer('eval_user')->default(0)->after('eval_date');
            $table->text('eval_reason')->nullable()->after('eval_user');
            $table->double('rejected_qty')->default(0)->after('eval_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receive_items', function (Blueprint $table) {
            //
        });
    }
};
