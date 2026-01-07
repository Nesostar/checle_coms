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
    Schema::table('sale_items', function (Blueprint $table) {
        $table->enum('price_type', ['retail', 'wholesale'])
              ->default('retail')
              ->after('price');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
{
    Schema::table('sale_items', function (Blueprint $table) {
        $table->dropColumn('price_type');
    });
}
};
