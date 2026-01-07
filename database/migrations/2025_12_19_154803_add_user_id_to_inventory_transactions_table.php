<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('inventory_transactions', function (Blueprint $table) {
        $table->unsignedBigInteger('user_id')->nullable()->after('note');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
{
    Schema::table('inventory_transactions', function (Blueprint $table) {
        $table->dropColumn('user_id');
    });
}
};
