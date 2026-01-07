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
    Schema::table('items', function (Blueprint $table) {
        $table->decimal('retail_price', 10, 2)->default(0);
        $table->decimal('whole_price', 10, 2)->default(0);
        $table->integer('quantity')->default(0); // This will serve as your stock
        $table->date('expiry_date')->nullable();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
{
    Schema::table('items', function (Blueprint $table) {
        $table->dropColumn(['retail_price', 'whole_price', 'quantity', 'expiry_date']);
    });
}
};
