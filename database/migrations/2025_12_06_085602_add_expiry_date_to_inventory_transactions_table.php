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
        if (!Schema::hasColumn('inventory_transactions', 'expiry_date')) {
            $table->date('expiry_date')->nullable()->after('quantity');
        }
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
        if (Schema::hasColumn('inventory_transactions', 'expiry_date')) {
            $table->dropColumn('expiry_date');
        }
    });
}
};
